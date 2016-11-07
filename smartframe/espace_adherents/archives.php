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

//création du manager d'archives
$manager = new ManagerArchives($bdd);

//Chargement des archives
$archives = $manager->getList();



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
      <title>Archives</title>
    </head>

    <body>
		<div class="container">

				<header class="page-header">
					<h1>SMARTFUL</h1>
					<a href="../index.php">Accueil</a> <a href="../admin/admin.php" style="color:white;">Admin</a>
				</header>



					<div class="col-md-4">
						<ul>
							<li> <a href="../actualites.php" title="actualites"><h3>Actualités</h3></a> </li>
							<li> <a href="../contact.php" title="contact"><h3>Contact</h3></a> </li>
							<li> <a href="../acces.php" title="acces adherents"><h3>Accès Adhérents</h3></a> </li>
						</ul>
					</div>

						<?php
						/*Condition vérifiant si l'utilisateur est bien un inscrit */
						if(isset($_SESSION['id']))
						{
						?>
								<p>
									<a href="adherents.php">Espace Adhérents</a>
								</p>

								<section class="col-md-6">
									<?php
									foreach($archives as $ticket)
									{
									?>
										<article>
											<h1><?php echo $ticket->titre(); ?></h1>

											<a href="<?php echo "../archives/".$ticket->titre().".pdf";?>" title="télécharger"> Télécharger </a>

											<em>Ajouté le <?php echo $ticket->date_ajout(); ?> </em>
										</article>
									<?php
									}
									?>
								</section>

						<?php
						}
						else
						{
						?>
							<p>
								Il y a eu un problème avec votre identification ! <br/>
								Veuillez vous reconnecter : <a href=\"../acces.php\">Accès</a>
							</p>
						<?php
						}
						?>



				<footer>
					<a href="?deconnexion">Déconnexion</a>
				</footer>

		</div>

	</body>
</html>
