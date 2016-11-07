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

//Vérification administrateur
$manager_admin = new ManagerAdmin($bdd);
if(isset($_POST['login']) AND isset($_POST['pass']))
{
	if($_POST['login'] != "" AND $_POST['pass'] != "")
	{
		$login = htmlspecialchars($_POST['login']);
		$pass = htmlspecialchars($_POST['pass']);

		if(preg_match("#^[a-zA-Z0-9éèùà@&]{7,15}$#",$pass))
		{
			$admin = $manager_admin->getLogin($login);
			//Gestion du cas où le login n'est pas présent dans la base de données
			if($admin == FALSE)
			{
				$message_admin = "Votre adresse login n'est pas présente dans la base de données ! <br/>
								Veuillez revenir dans l'espace utilisateur : <a href=\"../index.php\">Acceuil</a>";
				goto b;
			}
			if($manager_admin->verifPass($admin,$_POST['pass']))
			{
				//Création de la variable sessions
				$_SESSION['id_admin'] = $admin->id();
			}
			else
			{
				$message_admin = "Votre mot de passe <strong>".$pass."</strong> <br/>
								ne correspond pas avec le login <strong>".$login." </strong>  <br/>
								Veuillez revenir dans l'espace utilisateur : <a href=\"../index.php\">Acceuil</a>";
			}
		}
	}
	else
	{
		$message_admin = "Vous n'avez pas renseigné le login ou le mot de passe! <br/>
						Veuillez revenir dans l'espace utilisateur : <a href=\"../index.php\">Acceuil</a>";
	}
}
else
{
	if(isset($_SESSION['id_admin']))
	{
		$id_admin = $_SESSION['id_admin'];
		$admin = $manager_admin->get($id_admin);
	}
	else
	{
		$message_admin = "Il y a eu un problème lors de votre connexion à l'espace administrateur! <br/>
						Veuillez revenir dans l'espace utilisateur : <a href=\"../index.php\">Acceuil</a>";
	}
}

//création du manager d'utilisateur
$manager_user = new ManagerUser($bdd);

//Validation adhérent
if(isset($_GET['valider']))
{
	//On regarge si l'identifiant correspond bien à un adhérents présent dans la BDD
	if($manager_user->exist((int)$_GET['valider']))
	{
		$adherentAValider = $manager_user->get((int)$_GET['valider']);
		$manager_user->validation($adherentAValider);
		echo "La validation s'est déroulée correctement";
	}
	else
	{
		$message = "L'identifiant de l'adhérent ne correspond à aucun adhérent enregistré dans la base de données !";
	}
}

//Suppression adhérent
if(isset($_GET['supprimer']))
		{
			$adherentASupprimer = $manager_user->get((int)$_GET['supprimer']);
			$manager_user->supprimer($adherentASupprimer);
			echo "L'adhérent a été supprimée correctement. <br/>";
		}

/* Deconnecter l'utilisateur */
if(isset($_GET['deconnexion']))
{
	session_destroy();
	echo "Votre êtes bien déconnecté !";
}

b:
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
			  <title>Administration</title>
    </head>

    <body>
		<div class=container>

				<header class="page-header">
					<h1>SMARTFRAME ADMIN</h1>
					<a href="../index.php">Accueil</a>
				</header>

				<?php
					if(isset($message_admin))
					{
						echo $message_admin;
					}
					else
					{
					?>

						<div class="col-md-4">
							<ul>
								<li> <a href="gestion_news.php" title="gestion des news"><h3 >Gestion news</h3></a> </li>
								<li> <a href="gestion_archives.php" title="gestion des archives"><h3>Archives</h3></a> </li>
								<li> <a href="gestion_album.php" title="gestion des albums photos"><h3>Album photos</h3></a> </li>
								<li> <a href="?liste" title="liste des adhérents"><h3>Liste Adhérents</h3></a> </li>
							</ul>
						</div>


						<section class="col-md-6">
							<h2>Page d'administration</h2>

								<?php
								if(isset($message))
								{
									echo $message;
								}
								else
								{
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
												<th>valid</th>
												<th></th>
												<th></th>
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
												<td><?php echo $membre->validation(); ?> </td>
												<td><a href="?valider=<?php echo $membre->id(); ?>">Valider</a> </td>
												<td><a id="click_secure" href="?supprimer=<?php echo $membre->id();?>">suppr.</a></td>
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
										?>
										</table>
										<?php
									}
								}
								?>
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
