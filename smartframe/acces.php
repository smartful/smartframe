<?php
/* Initialisation */
session_start();

//chargement des classes
require "lib/autoload.inc.php";

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

/* Vérification des données du formulaire */
if(isset($_GET['go']))
{
	if($_POST['email'] != "" AND $_POST['pass'] != "" AND $_POST['passVerif'] != "" AND $_POST['prenom'] != "" AND $_POST['nom'] != ""
					AND $_POST['age'] != "" AND $_POST['sexe'] != "" )
	{
		if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#",$_POST['email']))
		{
			if(preg_match("#^[a-zA-Z0-9éèùà@&]{7,15}$#",$_POST['pass']) AND $_POST['pass'] == $_POST['passVerif'])
			{
				$user = new User(array(
										'email' => $_POST['email'],
										'pass' => $_POST['pass'],
										'prenom' => $_POST['prenom'],
										'nom' => $_POST['nom'],
										'age' => $_POST['age'],
										'sexe' => $_POST['sexe']
										));
				$manager_user->add($user);

				//Création des sessions
				$_SESSION['id'] = $user->id();

				$message = "Votre inscription est un succès ! <br/>
							Vous pourrez accéder à la page d'accès dès que <br/>
							l'administrateur du site aura validé votre inscription !";
			}
			else
			{
				$message = "Votre mot de passe doit contenir entre 7 et 15 caractères <br/>
							(les caractères spéciaux autorisés : <strong> éèùà@& </strong>)  <br/>
							Veuillez recommencer : <a href=\"acces.php\">Inscription</a>";
			}
		}
		else
		{
			$message = "Votre adresse email <strong>".htmlspecialchars($_POST['email'])."</strong> <br/>
						ne ressemble pas à une adresse email standard! <br/>
						Veuillez recommencer : <a href=\"acces.php\">Inscription</a>";
		}
	}
	else
	{
		$message = "Vous n'avez pas rempli tous le champs du formulaire! <br/>
					Veuillez recommencer : <a href=\"acces.php\">Inscription</a>";
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
				  <link rel="stylesheet" href="style_ie.css" />
				<![endif]-->
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
				<link rel="stylesheet" href="style.css"type="text/css" />
				<!-- [SMARTFUL] Pour accroitre le référencement, il est conseillé de mettre un titre différent sur chaque page -->
        <title>Accès Adhérents</title>
    </head>

    <body>
			<div class='container'>

				<header class="page-header">
					<h1>SMARTFRAME</h1>
					<?php include ("includes/wrapper.php"); ?>
				</header>

				<section class="row">
					<div class="col-md-4">
						<ul>
							<li> <a href="actualites.php" title="actualites"><h3 class="actualites">Actualités</h3></a> </li> <br />
							<li> <a href="contact.php" title="contact"><h3 class="contact">Contact</h3></a> </li><br />
							<li> <a href="acces.php" title="acces adherents"><h3 class="acces">Accès Adhérents</h3></a> </li><br />
						</ul>
					</div>

					<div class="col-md-6">
						<p>
							Si vous vous êtes déjà connectez cliquez ici : <a href="espace_adherents/adherents.php" title="accès direct"> Accès direct </a>
						</p>

						<h2>Identifiez vous</h2> pour accéder à l'espace adhérents :

						<form method="post" action="espace_adherents/adherents.php" class="well">
							<p>
								<label for="mail">Adresse Email : </label><br/>
								<input type="text" name="email" id="mail" class="form-control"/>
							</p>

							<p>
								<label for="pass">Mot de passe : </label><br/>
								<input type="password" name="pass" id="pass" class="form-control"/>
							</p>

							<p>
								<input type="submit" value="Valider" class="btn btn-primary"/>
							</p>
						</form>
					</div>

				</section>
					<?php
					if(!empty($message))
					{
					?>
						<p>
							<?php echo $message ;?>
						</p>
					<?php
					}
					else
					{
					?>
						<section class="row">
							<div class="col-md-12">
								<h2>Inscription sur le site</h2>

									<p style="color: rgb(100,100,100);">
										Tous les champs sont obligatoires !
									</p>

									<p>
										<form method="post" action="acces.php?go" class="well">
											<fieldset id="inscription_form">
												<legend>Inscription</legend>
												<div class="form-group">
													<label for="email">Email </label>
													<input type="text" name="email" id="email" class="form-control"/>
												</div>

												<div class="form-group">
													<label for="pass">Password </label>
													<input type="password" name="pass" id="pass" class="form-control"/><br/>
												</div>

												<div class="form-group">
													<label for="passVerif">Password Vérification </label>
													<input type="password" name="passVerif" id="passVerif" class="form-control"/>
												</div>

												<div class="form-group">
													<label for="prenom">Prenom </label>
													<input type="text" name="prenom" id="prenom" class="form-control"/>
												</div>

												<div class="form-group">
													<label for="nom">Nom </label>
													<input type="text" name="nom" id="nom" class="form-control"/>
												</div>

												<div class="form-group">
													<label for="age">Age </label>
													<input type="number" name="age" id="age" class="form-control"/>
												</div>

												<h4>Sexe </h4>
												<div class="radio">
													<label for="homme" class="radio">
														<input type="radio" name="sexe" value="m" id="homme"/> Homme
													</label>
												</div>

												<div class="radio">
													<label for="femme" class="radio">
														<input type="radio" name="sexe" value="f" id="femme" /> Femme
													</label>
												</div>

												<div class="radio">
													<input type="submit" value="Go" class="btn btn-primary"/>
													<input type="reset" value="Effacer" class="btn btn-danger"/>
												</div>
											</fieldset>
										</form>
									</p>
								</div>
						</section>
					<?php
					}
					?>

				<footer>
					<a href="?deconnexion">Déconnexion</a>
				</footer>

		</div>

	</body>
</html>
