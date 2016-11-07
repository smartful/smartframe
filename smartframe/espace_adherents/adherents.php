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

//création du manager d'utilisateur
$manager_user = new ManagerUser($bdd);

/* Vérification du formulaire accès */
if(isset($_POST['email']) AND isset($_POST['pass']))
{
	if($_POST['email'] != "" AND $_POST['pass'] != "")
	{
		if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#",$_POST['email']))
		{
			if(preg_match("#^[a-zA-Z0-9éèùà@&]{7,15}$#",$_POST['pass']))
			{
				$user = $manager_user->getEmail($_POST['email']);
				//Gestion du cas où le mail n'est pas présent dans la base de données
				if($user == FALSE)
				{
					$message = "Votre adresse email n'est pas présente dans la base de données ! <br/>
								Veuillez vous inscrire : <a href=\"../acces.php\">Page d'accès</a>";
					goto a;
				}
				if($manager_user->verifPass($user,$_POST['pass']))
				{
					//Création des sessions
					$_SESSION['id'] = $user->id();
				}
				else
				{
					$message = "Le mot de passe que vous avez tapez <strong>".htmlspecialchars($_POST['pass'])."</strong> <br/>
								ne correspond pas avec l'adresse email <strong>".htmlspecialchars($_POST['email'])." </strong>  <br/>
								Veuillez recommencer : <a href=\"../acces.php\">Page d'accès</a>";
				}
			}
			else
			{
				$message = "Votre mot de passe doit contenir entre 7 et 15 caractères <br/>
							(les caractères spéciaux autorisés : <strong> éèùà@& </strong>)  <br/>
							Veuillez recommencer : <a href=\"../acces.php\">Page d'accès</a>";
			}
		}
		else
		{
			$message = "Votre adresse email <strong>".htmlspecialchars($_POST['email'])."</strong> <br/>
						ne ressemble pas à une adresse email standard! <br/>
						Veuillez recommencer : <a href=\"../acces.php\">Page d'accès</a>";
		}
	}
	else
	{
		$message = "Vous n'avez pas renseigné l'adresse email ou le mot de passe! <br/>
					Veuillez recommencer : <a href=\"../acces.php\">Page d'accès</a>";
	}
}
else
{
	if(isset($_SESSION['id']))
	{
		$id_user = $_SESSION['id'];
		$user = $manager_user->get($id_user);
	}
	else
	{
		$message = "Il y a eu un problème lors de votre connexion ! <br/>
					Veuillez vous reconnecter : <a href=\"../acces.php\">Page d'accès</a>";
	}
}

/* Deconnecter l'utilisateur */
if(isset($_GET['deconnexion']))
{
	session_destroy();
	echo "Votre êtes bien déconnecté !";
}

a:
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
        <title>Espace Adhérents</title>
    </head>

    <body>
		<div class="container">

				<header class="page-header">
					<h1>SMARTFRAME</h1>
					<a href="../index.php">Accueil</a> <a href="../admin/admin.php" style="color:white;">Admin</a>
				</header>

				<section class="row">
					<div class="col-md-4">
						<ul>
							<li> <a href="../actualites.php" title="actualites"><h3>Actualités</h3></a> </li>
							<li> <a href="../contact.php" title="contact"><h3>Contact</h3></a> </li>
							<li> <a href="../acces.php" title="acces adherents"><h3>Accès Adhérents</h3></a> </li>
						</ul>
					</div>

					<?php

						if(isset($message))
						{
						?>
							<p>
								<?php echo $message; ?>
							</p>
						<?php
						}
						else
						{
							if(isset($_SESSION['id']))
							{
								$user_valide = $manager_user->estValide($user);

							?>
								<article class="col-md-6">
									<p>
										Bonjour <strong><a href="profil_adherents.php" title="profil"><?php echo $user->prenom()." ".$user->nom();?></a></strong>
									</p>
									<?php
									if($user_valide)
									{
									?>
									<ul>
										<li> <a href="archives.php" title="archives"><strong>Archives</strong> (compte rendus conférences, sortie, AG, ...)</a> </li>
										<li> <a href="albums_photos.php" title="albums photos"><strong>Albums photos</strong></a> </li>
										<li> <a href="?liste" title="liste des adhérents"><strong>Ils ont adhéré</strong></a> </li>
									</ul>

									<!-- Génération de la liste des adhérents -->
										<?php
										if(isset($_GET['liste']))
										{
											?>
											<table class="table table-bordered table-striped table-condensed">
												<tr>
													<th>prenom</th>
													<th>nom</th>
													<th>email</th>
													<th>sexe</th>
													<th>age</th>
												</tr>
											<?php
											$adherents = $manager_user->getList();
											foreach($adherents as $membre)
											{
											?>
												<tr>
													<td><?php echo $membre->prenom(); ?> </td>
													<td><?php echo $membre->nom(); ?> </td>
													<td><?php echo $membre->email(); ?> </td>
													<td><?php echo $membre->sexe(); ?> </td>
													<td><?php echo $membre->age(); ?> </td>
												</tr>
											<?php
											}
											?>
											</table>
											<?php
										}
										?>
									<?php
									}
									else
									{
									?>
										<p>
											Vous êtes bien enregistré dans la base de données, <br/>
											mais l'adminstrateur ne vous a toujours pas validé.
										</p>
									<?php
									}
									?>

								</article>
							<?php
							}
							else
							{
								echo "Il y a eu un problème avec votre identification ! <br/>
									  Veuillez vous reconnecter : <a href=\"../acces.php\">Accès</a>";
							}
						}
						?>

					</section>

			<footer>
				<a href="?deconnexion">Déconnexion</a>
			</footer>

		</div>

	</body>
</html>
