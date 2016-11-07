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

//Création du manager de billets (billet = news)
$manager = new ManagerBillets($bdd);

//Chargement des billets
$billets = $manager->getList();



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
				<link rel="stylesheet" href="css/style.css" type="text/css" />
		<!-- [SMARTFUL] Pour accroitre le référencement, il est conseillé de mettre un titre différent sur chaque page -->
        <title>Actualités</title>
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
						<li> <a href="actualites.php" title="actualites"><h3>Actualités</h3></a> </li>
						<li> <a href="contact.php" title="contact"><h3>Contact</h3></a> </li>
						<li> <a href="acces.php" title="acces adherents"><h3>Accès Adhérents</h3></a> </li>
					</ul>
				</div>

				<div class="col-md-6">
				<?php
					foreach($billets as $ticket)
					{
					?>
						<article class="col-md-12">
							<h1><?php echo $ticket->titre(); ?></h1>

							<p>
								<?php echo nl2br($ticket->contenu()); ?>
							</p>

							<h4>Rédigé <?php echo $ticket->date_ajout(); ?> | <em>Modifié <?php echo $ticket->date_modif();?> </em> </h4>
						</article>
					<?php
					}
					?>
				</div>
			</section>

			<footer>
				<a href="?deconnexion">Déconnexion</a>
			</footer>

		</div>
	</body>
</html>
