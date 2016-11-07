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

//Création du manager d'archives
$manager = new ManagerArchives($bdd);

/* Vérification de l'envoi des documents d'archives */
if(isset($_GET['envoi']))
{
	//On vérifie que le fichier envoyé existe et qu'il n'y a pas eu d'erreur
	if(isset($_FILES['archive']) AND $_FILES['archive']['error'] == 0)
	{
		//Par défaut PHP est limité à 8 Mo, on s'assure donc que le fichier ne dépasse pas 8 Mo
		if($_FILES['archive']['size'] <= 8388608) // 8 Mo font 8 388 608 octets
		{
			//On vérifie l'extension (dans notre cas, les archives seront en PDF)
			$infosfichier = pathinfo($_FILES['archive']['name']);
			$extension_upload = $infosfichier['extension'];
			$extensions_autorisees = array('pdf');
			if(in_array($extension_upload,$extensions_autorisees))
			{
				$titre = basename($_FILES['archive']['name'],".pdf");

				//Inscription dans la base de données
				$archive = new Archive(array('titre' =>$titre));
				if($archive->valide())
				{
					$manager->add($archive);
					//Stockage définitif du fichier
					move_uploaded_file($_FILES['archive']['tmp_name'],'../archives/'.basename($_FILES['archive']['name']));
					$message = "L'envoi de votre fichier est un succès !!!";
				}
				else
				{
					$message = "Il y a eu une erreur dans l'inscription de la base de données !";
				}
			}
			else
			{
				$message = "Votre fichier n'a pas l'extension adéquate : <strong>.pdf </strong> ! <br/>
							Veuillez recommencer avec un fichier adapté !";
			}
		}
		else
		{
			$message = "La taille du fichier doit être inférieur à 8 Mo ! <br/>
						Veillez recommencer avec un fichier adapté ! ";
		}
	}
	else
	{
		$message = "Il y a eu une erreur lors de l'envoi du fichier ! <br/>
					Veuillez recommencer !";
	}
}

//Suppression de l'archive
if(isset($_GET['supprimer']))
{
	//On regarge si l'identifiant correspond bien à une news présente dans la BDD
	if($manager->exist((int)$_GET['supprimer']))
	{
		$archive_suppr = $manager->get((int) $_GET['supprimer']);
		//Suppression du fichier dans le dossier archives
		$chemin = "../archives/".$archive_suppr->titre().".pdf";
		unlink($chemin);
		//Suppression du fichier dans la base de données
		$manager->delete($archive_suppr);
	}
	else
	{
		$message = "L'identifiant de l'archive ne correspond à aucune archives enregistrées dans la base de données !";
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
      <title>Gestion des archives</title>
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
							/* Zone d'affichage des messages d'erreurs ou du message de succès d'envoi d'un fichier ! */
							if(isset($message))
							{
								echo "<p>".$message."</p>.";
							}
						?>

						<section class="col-md-12">
							<h2>Liste des archives</h2>
							<?php
								$archives = $manager->getList();
							?>
								<!-- Tableau listant les billets -->
								<p>
									<table class="table table-bordered table-striped table-condensed">
										<tr>
											<th>Titre</th>
											<th>Ajouté le</th>
											<th>Action</th>
										</tr>
										<?php
										if(isset($archives))
										{
											foreach($archives as $ticket)
											{
											?>
												<tr>
													<td><?php echo $ticket->titre();  ?></td>
													<td><?php echo $ticket->date_ajout();  ?></td>
													<td><a id="click_secure" href="?supprimer=<?php echo $ticket->id(); ?>">supprimer</a></td>
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
						</section>

						<section class="col-md-12">
							<h2>Ajouter une archive</h2>
							<p>
								<em>Pour le titre de votre document, veuillez éviter les espaces (utilisez \"-\" ou \"_\") et les accents !</em>
							</p>

							<form action="?envoi" method="post" enctype="multipart/form-data" class="well">
								<p>
									<div class="form-group">
										<label for="file">Formulaire d'envoi de fichier : </label>
										<input type="file" name="archive" id="file" class="form-control"/>
									</div>
									<input type="submit" value="Envoyer" class="btn btn-primary"/>
								</p>
							</form>
						</section>
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
