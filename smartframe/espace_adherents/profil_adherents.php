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

//chargement de l'utilisateur
if(isset($_SESSION['id']))
{
	$id_user = $_SESSION['id'];
	$user = $manager_user->get($id_user);
}
else
{
	$message = "Il y a eu un problème avec votre identification ! <br/>
				Veuillez vous reconnecter : <a href=\"../acces.php\">Accès</a>";
}

/* Vérification du formulaire de modification du profil */
if(isset($_POST['passProfil']) AND isset($_POST['passVerifProfil']))
{
	if(preg_match("#^[a-zA-Z0-9éèùà@&]{7,15}$#",$_POST['passProfil']) AND $_POST['passProfil'] == $_POST['passVerifProfil'])
	{
		$pass =  $_POST['passProfil'];
		$user->setPass($pass);
		$manager_user->update($user);

		$message_modif = "Votre modification de mot de passe est un succès ! ";
	}
	else
	{
		$message_modif = "Votre mot de passe doit contenir entre 7 et 15 caractères <br/>
					(les caractères spéciaux autorisés : <strong> éèùà@& </strong>) ";
	}
}

if(isset($_POST['ageProfil']))
{
	if($_POST['ageProfil'] != "")
	{
		$age = (int) $_POST['ageProfil'];
		$user->setAge($age);
		$manager_user->update($user);

		$message_modif = "La modification de votre âge est un succès ! ";
	}
	else
	{
		$message_modif = "Vous n'avez pas rempli le champ du formulaire. ";
	}
}

/* Deconnecter l'utilisateur */
if(isset($_GET['deconnexion']))
{
	session_destroy();
	echo "Votre êtes bien déconnecté !";
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
			<title>Profil</title>
    </head>

	<body>
	<div class="container">
		<header class="page-header">
			<h1>SMARTFRAME</h1>
			<a href="../index.php">Accueil</a> <a href="../admin/admin.php" style="color:white;">Admin</a>
		</header>

		<div class="col-md-4">
			<ul>
				<li> <a href="../actualites.php" title="actualites"><h3 >Actualités</h3></a> </li>
				<li> <a href="../contact.php" title="contact"><h3>Contact</h3></a> </li>
				<li> <a href="adherents.php" title="espace adherents"><h3>Retour Adhérents</h3></a> </li>
			</ul>
		</div>

		<section class="col-md-6">
			<?php
			if(isset($_SESSION['id']))
			{
			?>
				<!-- Profil de l'utilisateur -->
				<table class="table table-bordered table-striped table-condensed">
					<tr>
						<td>Prénom </td>
						<td><?php echo $user->prenom();?> </td>
						<td> </td>
					</tr>

					<tr>
						<td>Nom </td>
						<td><?php echo $user->nom();?> </td>
						<td> </td>
					</tr>

					<tr>
						<td>Email</td>
						<td><?php echo htmlspecialchars($user->email());?> </td>
						<td> </td>
					</tr>

					<tr>
						<td>Mot de passe </td>
						<td>C'est votre secret </td>
						<td><a href="?modif&amp;champ=pass">modifier</a></td>
					</tr>

					<tr>
						<td>Âge </td>
						<td><?php echo htmlspecialchars($user->age());?> </td>
						<td><a href="?modif&amp;champ=age">modifier</a></td>
					</tr>

					<tr>
						<td>Date d'inscription </td>
						<td><?php echo $user->date_inscription();?> </td>
						<td> </td>
					</tr>
				</table>

				<?php

				if(isset($message_modif))
				{
					echo $message_modif;
				}

				if(isset($_GET['modif']) AND isset($_GET['champ']))
				{
				?>
					<!-- Interface de modification du profil -->
					<div class="row">
						<form method="post" action="profil_adherents.php" class="well">
							<fieldset >
								<legend>Modification</legend>
								<?php
									if($_GET['champ'] == "pass")
									{
									?>

									<div class="form-group">
										<label for="passProfil">Password </label>
										<input type="password" name="passProfil" id="passProfil" class="form-control"/>
									</div>

									<div class="form-group">
										<label for="passVerifProfil">Password Vérification </label>
										<input type="password" name="passVerifProfil" id="passVerifProfil" class="form-control"/>
									</div>

									<?php
									}

									if($_GET['champ'] == "age")
									{
									?>
									<div class="form-group">
										<label for="ageProfil">Âge </label>
										<input type="number" name="ageProfil" id="ageProfil" class="form-control"/>
									</div>

									<?php
									}
									?>

								<input type="submit" value="Valider" class="btn btn-primary"/><br/>
								<input type="reset" value="Effacer" class="btn btn-danger"/>
							</fieldset>
						</form>
					</div>
				<?php
				}
			}
			else
			{
				echo $message;
			}
			?>
		</section>

		<footer>
			<a href="?deconnexion">Déconnexion</a>
		</footer>

	</div>
	</body>
</html>
