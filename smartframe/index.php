<?php
/* Initialisation */
session_start();

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
				<!-- [SMARTFUL] Ajout de la balise meta keywords et description pour améliorer le référencement google -->
				<meta name="keywords" content="association, entreprise, ..."/>
				<meta name="description" content="Au sein de l'association, l'entreprise 'machin' ... "/>
				<!--Pour la compatibilité des balises html5 avec les anciennes versions de navigateurs>
		        <!--[if lt IE 9]>
					<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		    	<![endif]-->

		    	<!--[if lte IE 7]>
					<link rel="stylesheet" href="style_ie.css" />
				<![endif]-->
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
				<link rel="stylesheet" href="css/style.css" type="text/css" />
        <title>SMARTFRAME</title>

    </head>

    <body>
		<div id='container'>

			<header class="page-header">
				<h1>SMARTFRAME</h1>
			</header>

			<?php include("includes/menu_accueil.php"); ?>

				<section class="row">
					<article class="col-md-4">
						<h2>SMARTFUL FRAMEWORK</h2>

						<p>
						<strong>SMARTFRAME (SMARTFUL FRAMEWORK)</strong> est un framework léger dans la technologie PHP permettant de réaliser des sites à destination
						des associations et des entreprises (TPE, PME/PMI).
						</p>
					</article>

					<article class="col-md-4">
						<h2>Fonctionnalités</h2>
							<ul>
								<li>Espace administration</li>
								<li>Espace adhérents</li>
								<li>Gestion d'albums photos</li>
								<li>Gestion de la documentation (archives)</li>
								<li>Gestion d'actualités</li>
							<ul>
					</article>

					<article class="col-md-4">
						<h2>Pourquoi ?</h2>
						<p>
						Implémenter rapidement un site à l'usage d'une entreprise ou d'une association. <br/>
						Cette solution se pose comme étant un intermédiaire entre un CMS (wordpress, joomla, drupal, ...) et les frameworks plus complets.
						</p>
					</article>

				</section>

				<footer class="row">
					<h3>SMARTFUL</h3><br />
					"SMARTFUL" est une marque déposée à l’I.N.P.I par Rémi RODRIGUES.<br />
					"SMARTFUL" sous le n° 13/4054367 <br/>
					<h4>copyright : Rémi Matthieu RODRIGUES</h4>
					<a href="?deconnexion">Déconnexion</a>
				</footer>
		</div>
	</body>
</html>
