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

//Création du manager d'albums
$manager_albums = new ManagerAlbums($bdd);

//Création du manager de photos
$manager_photos = new ManagerPhotos($bdd);

/* Vérification pour la création d'un album*/
if(isset($_GET['crea_album']))
{
	if(isset($_POST['titre_album']) AND isset($_POST['description_album']))
	{
		$titre = $_POST['titre_album'];
		$description = $_POST['description_album'];
		$album = new Album(array(
								'titre' =>$titre,
								'description' => $description
								));
		if($album->valide())
		{
			$manager_albums->add($album);
			//Création du dossier portant le nom de l'album
			if(mkdir("../albums/".$titre."/",0777)){
				$message = "La création de l'album est un succès !!!";
			}
			else {
				$message = "La création de l'album est un échec :-( ";
			}

		}
		else
		{
			$message = "Il y a eu une erreur dans l'inscription de la base de données !";
		}
	}
	else
	{
		$message = "Le champs titre ou description est vide !";
	}
}

/* Vérification de l'envoi d'une photo */
if(isset($_GET['envoi_photo']))
{
	if(isset($_POST['albums']))
	{
		$choix_album = (int) $_POST['albums'];
		$album_choisi = $manager_albums->get($choix_album);

		//On vérifie que la photo envoyée existe et qu'il n'y a pas eu d'erreurs
		if(isset($_FILES['photo']) AND $_FILES['photo']['error'] == 0)
		{
			//Par défaut PHP est limité à 8 Mo, on s'assure donc que la photo ne dépasse pas 8 Mo
			if($_FILES['photo']['size'] <= 8388608) // 8 Mo font 8 388 608 octets
			{
				//On vérifie l'extension (dans notre cas, les photos seront en .jpeg, .jpg, .gif, .png)
				$infosfichier = pathinfo($_FILES['photo']['name']);
				$extension_upload = $infosfichier['extension'];
				$extensions_autorisees = array('jpeg','jpg','JPG','gif','png');
				if(in_array($extension_upload,$extensions_autorisees))
				{
					$titre = basename($_FILES['photo']['name']);

					//Inscription dans la base de données
					$photo = new Photo(array(
												'id_album' =>$choix_album,
												'titre' =>$titre
												));
					if($photo->valide())
					{
						//Stockage de la photo dans le dossier d'album correspondant
						$tmp = $_FILES['photo']['tmp_name'];
						$destination = '../albums/'.$album_choisi->titre().'/'.basename($_FILES['photo']['name']);
						move_uploaded_file($tmp,$destination);


						//On vas maintenant miniaturiser la photo qui nous a été envoyé
						$infos_chemin = pathinfo($destination);
						if($infos_chemin['extension'] == 'jpg' OR $infos_chemin['extension'] == 'jpeg' OR $infos_chemin['extension'] == 'JPG')
						{
							$destination_final = '../albums/'.$album_choisi->titre().'/mini_'.basename($_FILES['photo']['name']);
							$source = imagecreatefromjpeg($destination);//création des images
							$final = imagecreatetruecolor(210,200);

							$largeur_final = imagesx($final);//affectation des mesures des images
							$hauteur_final = imagesy($final);
							$largeur_source = imagesx($source);
							$hauteur_source = imagesy($source);

							imagecopyresampled($final,$source,0,0,0,0,$largeur_final,$hauteur_final,$largeur_source,$hauteur_source);

							imagejpeg($final,$destination_final);
							$message_mini = "La miniaturisation de l'image est un succès !";
						}
						else
						{
							$message_mini = "L'extention n'a pas rendu possible la miniaturisation de l'image";
						}
						//Enregistrement dans la base de données
						$manager_photos->add($photo);
						$message = "L'envoi de votre fichier est un succès !!!<br/>".$message_mini;
					}
					else
					{
						$message = "La photo n'a pas été bien enregistrée dans la base de données !";
					}
				}
				else
				{
					$message = "Votre photo n'a pas l'extension adéquate : <strong>'.jpeg','.jpg','.gif','.png' </strong> ! <br/>
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
	else
	{
		$message = "Aucun album n'a été choisi ! <br/>
					Veuillez recommencer !";
	}
}

//Suppression d'une photo
if(isset($_GET['supprimer_photo']))
{
	//On regarge si l'identifiant correspond bien à une news présente dans la BDD
	if($manager_photos->exist((int)$_GET['supprimer_photo']))
	{
		$photo_suppr = $manager_photos->get((int) $_GET['supprimer_photo']);
		$id_album_photo_suppr = $photo_suppr->id_album();
		$album_photo_suppr = $manager_albums->get($id_album_photo_suppr);
		$nom_album_photo_suppr = $album_photo_suppr->titre();
		//Suppression de la photo dans son dossier
		$chemin = "../albums/".$nom_album_photo_suppr."/".$photo_suppr->titre();
		$infos_photo = pathinfo($chemin);
		unlink($chemin);

		//Vérification de l'existence d'une miniature en regardant l'extension (il n'y a pas de miniature si ce n'est pas du .jpeg)
		if($infos_photo['extension'] == 'jpg' OR $infos_photo['extension'] == 'jpeg' OR $infos_photo['extension'] == 'JPG')
		{
			$chemin_mini = "../albums/".$nom_album_photo_suppr."/mini_".$photo_suppr->titre();
			unlink($chemin_mini); //suppression de la miniature
		}
		//Suppression la photo dans la base de données
		$manager_photos->delete($photo_suppr);

		$message = "La suppression de la photo et de sa miniature est un succès !!!";
	}
	else
	{
		$message = "L'identifiant de la photo ne correspond à aucunes photos enregistrées dans la base de données !";
	}
}

//Suppression d'un album
if(isset($_GET['supprimer_album']))
{
	//On regarge si l'identifiant correspond bien à une news présente dans la BDD
	if($manager_albums->exist((int)$_GET['supprimer_album']))
	{
		$album_suppr = $manager_albums->get((int) $_GET['supprimer_album']);
		$nom_album_suppr = $album_suppr->titre();

		/*Suppression de l'album et de toutes les photos comprises dans l'album*/
		//Il faut au préalable supprimer toutes les photos présentes dans l'album
		$photos_de_album = $manager_photos->getListByAlbum($album_suppr->id());
		foreach($photos_de_album as $photo_a_detruire)
		{
			//Suppression de la photo dans son dossier
			$chemin_photo = "../albums/".$nom_album_suppr."/".$photo_a_detruire->titre();
			$infos_photo_a_detruire = pathinfo($chemin_photo);
			unlink($chemin_photo);

			//Vérification de l'existence d'une miniature en regardant l'extension (il n'y a pas de miniature si ce n'est pas du .jpeg)
			if($infos_photo_a_detruire['extension'] == 'jpg' OR $infos_photo_a_detruire['extension'] == 'jpeg')
			{
				$chemin_photo_mini = "../albums/".$nom_album_suppr."/mini_".$photo_a_detruire->titre();
				unlink($chemin_photo_mini); //suppression de la miniature
			}
			//Suppression la photo dans la base de données
			$manager_photos->delete($photo_a_detruire);
		}

		//Une fois toutes les photos détruites on supprime le dossier
		$dossier = "../albums/".$nom_album_suppr;
		rmdir($dossier);
		//Suppression de l'album dans la base de données
		$manager_albums->delete($album_suppr);
		$message = "La suppression de l'album photo un succès !!!";
	}
	else
	{
		$message = "L'identifiant de l'album ne correspond à aucun <br/>
					albums enregistrés dans la base de données !";
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
      <title>Gestion des albums</title>
    </head>

    <body>
			<div class="container">
				<header class="page-header">
					<h1>SMARTFRAME ADMIN</h1>
					<a href="../index.php">Accueil</a>
				</header>

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
							<?php
							}
						?>

						<section class="col-md-12">
							<h2>Liste de tous les albums</h2>
							<p>
									<table class="table table-bordered table-striped table-condensed">
									<tr>
										<th>Titre</th>
										<th>Description</th>
										<th>Action</th>
									</tr>
									<?php
									$albums = $manager_albums->getList();
									if (isset($albums))
									{
										foreach($albums as $ticket)
										{
										?>
											<tr>
												<td><a href="?numero=<?php echo $ticket->id();?>" title="liste des photos"><?php echo $ticket->titre();  ?></a></td>
												<td><?php echo $ticket->description();  ?></td>
												<td><a id="click_secure" href="?supprimer_album=<?php echo $ticket->id(); ?>">supprimer</a></td>
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
								<table class="table table-bordered table-striped table-condensed">
								<tr>
									<th>Titre</th>
									<th>Aperçu</th>
									<th>Action</th>
								</tr>
								<?php
								if (isset($photos))
								{
									foreach($photos as $picture)
									{

										$chemin_miniature = '../albums/'.$album_liste->titre().'/mini_'.$picture->titre();
									?>
										<tr>
											<td><?php echo $picture->titre();  ?></td>
											<td><img src="<?php echo $chemin_miniature;?>" title="<?php echo $picture->titre();?>" alt="<?php echo $picture->titre();?>"/> </td>
											<td><a id="click_secure" href="?supprimer_photo=<?php echo $picture->id(); ?>">supprimer</a></td>
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
						<?php
						}
						?>

						<section class="col-md-12">
							<h2>Créer un album</h2>

							<form action="?crea_album" method="post" class="well">
								<p>
									<em>Ne pas mettre d'espace (utiliser "-", "_") ou d'accent dans le titre d'un album</em>
									<fieldset>
									<legend>Création d'un album</legend>
										<div class="form-group">
											<label for="titre">Titre : </label>
											<input type="text" name="titre_album" id="titre" class="form-control"/>
										</div>

										<div class="form-group">
											<label for="description">Description : </label>
											<textarea id="description" name="description_album" class="form-control">
											</textarea>
										</div>

										<input type="submit" value="Valider" class="btn btn-primary"/>
									</fieldset>
								</p>
							</form>
						</section>

						<section class="col-md-12">
							<h2>Ajouter une photo</h2>

							<p>
								<em>Favoriser les photos ayant pour extensions <strong>.jpeg</strong></em>
							</p>

							<fieldset>
							<legend>Envoi d'une photo</legend>
								<form action="?envoi_photo" method="post" enctype="multipart/form-data" class="well">
									<p>
										<div class="form-group">
											<label for="file">Formulaire d'envoi d'une photo : </label>
											<input type="file" name="photo" id="file" class="form-control"/>
										</div>

										<div class="form-group">
											<label for="albums">Dans quel album voulez vous placez cette photo? </label>
											<select name="albums" id="albums" class="form-control">
												<option value=" "> </option>
												<?php
												if (isset($albums))
												{
													foreach($albums as $classeur)
													{
														echo "<option value=\"".$classeur->id()."\">".$classeur->titre()."</option>";
													}
												}
												?>
											</select>
										</div>
										<input type="submit" value="Envoyer" class="btn btn-primary"/>
									</p>
								</form>
							</fieldset>

							<p> </p>

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

	</body>
</html>
