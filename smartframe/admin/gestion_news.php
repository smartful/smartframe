<?php
/* Initialisation */
session_start();

//chargement des classes
require "../lib/autoload.inc.php";

//Chargement de la BDD
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=smartfulframework','root','',array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
}
catch(Exception $e)
{
	die("Erreur : ".$e->getMessage());
}

//Création du manager de billets (billet = news)
$manager = new ManagerBillets($bdd);


//Ajout
if(isset($_POST['ajouter']))
{

	$billet = new Billet(array(
								'titre' => $_POST['titre'],
								'contenu' => $_POST['contenu']
								));
	if($billet->valide())
	{
		$manager->add($billet);
	}
	else
	{
		$message = "Les champs du titre ou du contenu sont vide et par conséquent invalides !";
	}
}

//Modification : affichage du formulaire
if(isset($_GET['modifier']))
{
	//On regarge si l'identifiant correspond bien à une news présente dans la BDD
	if($manager->exist((int)$_GET['modifier']))
	{
		$billet = $manager->get((int)$_GET['modifier']);
	}
	else
	{
		$message = "L'identifiant de la news ne correspond à aucune news enregistré dans la base de données !";
	}
}

//Modification : enregistrement dans la BDD
if(isset($_POST['modifier']))
{
	if(isset($_POST['id']))
	{
		//On regarge si l'identifiant correspond bien à une news présente dans la BDD
		if($manager->exist((int)$_POST['id']))
		{
			$billet_modif = $manager->get((int) $_POST['id']);
			$billet_modif->setTitre($_POST['titre']);
			$billet_modif->setContenu($_POST['contenu']);
			$manager->update($billet_modif);
		}
		else
		{
			$message = "L'identifiant de la news ne correspond à aucune news enregistré dans la base de données !";
		}
	}
	else
	{
		$message = "Aucun identifiant n'a été répertorié pour la modification de message";
	}
}

//Suppression de la news
if(isset($_GET['supprimer']))
{
	//On regarge si l'identifiant correspond bien à une news présente dans la BDD
	if($manager->exist((int)$_GET['supprimer']))
	{
		$billet_suppr = $manager->get((int) $_GET['supprimer']);
		$manager->delete($billet_suppr);
	}
	else
	{
		$message = "L'identifiant de la news ne correspond à aucune news enregistrées dans la base de données !";
	}
}
?>

<!DOCTYPE html>
<html>
    <head>
      <meta charset="utf-8" />
			<!-- [SMARTFUL] Ajout de la balise meta viewport pour l'adaptation aux différents types d'écran : effet dézoomage si les balises css
			d'adaptation ne fonctionne pas -->
			<meta name="viewport" content="width=device-width" />

			<!--Pour la compatibilité des balises html5 avec les anciennes versions de navigateurs>
			  <!--[if lt IE 9]>
			    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			  <![endif]-->
			  <!--[if lte IE 7]>
			  	<link rel="stylesheet" href="../style_ie.css" />
				<![endif]-->
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
			<link rel="stylesheet" href="../css/style.css"type="text/css" />
			<!-- [SMARTFUL] Pour accroitre le référencement, il est conseillé de mettre un titre différent sur chaque page -->
      <title>Gestion des news</title>
    </head>

    <body>
		<div class="container">

				<header class="page-header">
					<h1>SMARTFRAME ADMIN</h1>
					<a href="../index.php">Accueil</a>
				</header>

				<div class="row">
				<?php
				/*Condition vérifiant si l'utilisateur est bien un administrateur */
				if(isset($_SESSION['id_admin']))
				{
				?>
					<div class="col-md-4">
						<ul>
							<li> <a href="gestion_news.php" title="gestion des news"><h3>Gestion news</h3></a> </li>
							<li> <a href="gestion_archives.php" title="gestion des archives"><h3>Archives</h3></a> </li>
							<li> <a href="gestion_album.php" title="gestion des albums photos"><h3>Album photos</h3></a> </li>
							<li> <a href="admin.php?liste" title="liste des adhérents"><h3>Liste Adhérents</h3></a> </li>
						</ul>
					</div>

					<div class="col-md-6">
						<?php
						if(isset($message))
						{
						?>
							<p>
								<?php echo $message; ?>
							</p>
							<a href="gestion_news.php">Retour à la page de gestion des news</a>
						<?php
						}
						else
						{
						?>
							<!-- Formulaire d'ajout ou de modification -->
							<p>
								Il y a actuellement <?php echo $manager->count(); ?> news dans la base de données. <br/><br/>
								<a href="?ajouter=0">Ajouter</a>
							</p>
							<?php
							if(isset($_GET['ajouter']) || isset($_GET['modifier']))
							{
							?>
								<p>
									<form method="post" action="gestion_news.php" class="well">
										<div class="form-group">
											<label for="titre">Titre : </label>
											<input type="text" name="titre" id="titre" class="form-control" <?php if(isset($_GET['modifier'])) echo "value = \"".htmlspecialchars($billet->titre())."\""; ?>/>
										</div>

										<div class="form-group">
											<label for="contenu">Contenu :</label>
											<textarea rows="15" cols="70" name="contenu" id="contenu" class="form-control">
												<?php if(isset($_GET['modifier'])) echo htmlspecialchars($billet->contenu()); ?>
											</textarea>
										</div>
										<?php
										if(isset($_GET['modifier']))
										{
										?>
											<input type="hidden" name="id" value="<?php echo $_GET['modifier'];?>"  />
											<input type="submit" value="Valider" name="modifier" class="btn btn-warning"/>
										<?php
										}
										if(isset($_GET['ajouter']))
										{
										?>
											<input type="submit" value="Valider" name="ajouter" class="btn btn-primary"/>
										<?php
										}
										?>

									</form>
								</p>
							<?php
							}
							else
							{
								$billets = $manager->getList();
							?>
								<!-- Tableau listant les billets -->
								<p>
									<table class="table table-bordered table-striped table-condensed">
										<tr>
											<th>Titre</th>
											<th>Ajouté le</th>
											<th>Modifié le</th>
											<th>Action</th>
										</tr>
										<?php
										if(isset($billets))
										{
											foreach($billets as $ticket)
											{
											?>
												<tr>
													<td><?php echo $ticket->titre();  ?></td>
													<td><?php echo $ticket->date_ajout();  ?></td>
													<td><?php echo $ticket->date_modif(); ?></td>
													<td><a href="?modifier=<?php echo $ticket->id(); ?>">modifier</a> | <a id="click_secure" href="?supprimer=<?php echo $ticket->id(); ?>">supprimer</a></td>
													<script>
														var click_secure = document.getElementById('click_secure');

														click_secure.addEventListener('click', function(e) {
															if(!confirm("Êtes vous sur de vouloir supprimer ?"))
															{
																e.preventDefault(); // On bloque l'action par défaut de cet événement
															}
														}, false);
													</script>
												</tr>
											<?php
											}
										}
										?>
									</table>
								</p>
							<?php
							}
						}
						?>
					</div>
				<?php
				}
				else
				{
					echo "Il y a eu un problème lors de votre connexion à l'espace administrateur ! <br/>
						Veuillez revenir dans l'espace utilisateur : <a href=\"../index.php\">Acceuil</a>";
				}
				?>
				</div>

		</div>

	</body>
</html>
