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

//création du manager d'albums
$manager_albums = new ManagerAlbums($bdd);
//création du manager des photos
$manager_photos = new ManagerPhotos($bdd);

//Chargement des albums
$albums = $manager_albums->getList();


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
        <title>Photos</title>
    </head>

    <body>
		<div class="container">
				<header class="page-header">
					<h1>SMARTFRAME</h1>
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
						if(isset($_SESSION['id']))
						{
						?>
							<p>
								<a href="adherents.php">Espace Adhérents</a>
							</p>
							<section class="col-md-6">
							<h2>Liste de tous les albums</h2>
							<p>
								<table class="table table-bordered table-striped table-condensed">
									<tr>
										<th>Titre</th>
										<th>Description</th>
									</tr>
								<?php
								if (isset($albums))
								{
									foreach($albums as $ticket)
									{
									?>
										<tr>
											<td><a href="?numero=<?php echo $ticket->id();?>" title="liste des photos"><?php echo $ticket->titre();  ?></a></td>
											<td><?php echo $ticket->description();  ?></td>
										</tr>
									<?php
									}
								}
								?>
								</table>
							</p>
							</section>

							<?php
							if(isset($_GET['numero']))
							{
								$id_album_liste = (int) $_GET['numero'];
								$album_liste = $manager_albums->get($id_album_liste);
								$photos = $manager_photos->getListByAlbum($id_album_liste);
								?>
								<section class="col-md-12">
								<p>
									<h3><?php echo $album_liste->titre(); ?></h3>

									<p>
									<em>L'aperçu ne sera visible que pour les images ayant pour extension .jpeg</em>
									</p>

									<table class="table table-bordered table-striped table-condensed">
										<tr>
											<th>Titre</th>
											<th>Aperçu</th>
										</tr>
									<?php
									if (isset($photos))
									{
										foreach($photos as $picture)
										{
											$chemin_miniature = "../albums/".$album_liste->titre()."/mini_".$picture->titre();
										?>
											<tr>
												<td><a href="../albums/<?php echo $album_liste->titre()."/";?><?php echo $picture->titre();?>" title="voir la photo"><?php echo $picture->titre();?></a></td>
												<td><img src="<?php echo $chemin_miniature;?>" title="<?php echo $picture->titre();?>" alt="<?php echo $picture->titre();?>"/> </td>
											</tr>
										<?php
										}
									}
									?>
									</table>
								</p>
								</section>
							<?php
							}
						}
						else
						{
						?>
							<p>
								Il y a eu un problème avec votre identification ! <br/>
								Veuillez vous reconnecter : <a href="../acces.php">Accès</a>
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
