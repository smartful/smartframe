<?php
class ManagerPhotos
{
	/******************************************************************
	*					ATTRIBUTS
	*******************************************************************/
	
	protected $bdd;
	
	/******************************************************************
	*					METHODES
	*******************************************************************/

	public function __construct($bdd)
	{
		$this->bdd = $bdd;
	}
	
	public function add(Photo $photo)
	{
		$req = $this->bdd->prepare("INSERT INTO photos(id_album,titre)
										VALUES(:id_album,:titre)");
		$req->execute(array(
							'id_album' => $photo->id_album(),
							'titre' => $photo->titre()
							));
		$req->closeCursor();
	}
	
	public function update(Photo $photo)
	{
		$req = $this->bdd->prepare("UPDATE photos SET titre = :titre WHERE id = :id");
		$req->execute(array(
								'titre' => $photo->titre(),
								'id' => $photo->id()
								));
		$req->closeCursor();
	}
	
	public function delete(Photo $photo)
	{
		$req = $this->bdd->prepare("DELETE FROM photos WHERE id = :id");
		$req->execute(array('id' => $photo->id()));
		$req->closeCursor();
	}
	
	public function count()
	{
		$req = $this->bdd->query("SELECT COUNT(*) FROM photos");
		return $req->fetchColumn();
	}
	
	public function exist($id)
	{
		$req = $this->bdd->prepare("SELECT COUNT(*) FROM photos WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		return (bool) $req->fetchColumn();
	}
	
	public function get($id)
	{
		$req = $this->bdd->prepare("SELECT id,id_album,titre
									FROM photos WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		$donnees = $req->fetch();
		$req->closeCursor();
		return new Photo($donnees);
	}
	
	public function getList()
	{
		$photos = array();
		$req = $this->bdd->query("SELECT id,id_album,titre
									FROM photos ORDER BY titre DESC");
		while($donnees = $req->fetch())
		{
			$photos[] = new Photo($donnees);
		}
		$req->closeCursor();
		return $photos;
	}
	
	public function getListByAlbum($id_album)
	{
		$photos = array();
		$req = $this->bdd->prepare("SELECT id,titre
									FROM photos 
									WHERE id_album = :id_album
									ORDER BY titre DESC");
		$req->execute(array('id_album' => (int) $id_album));
		while($donnees = $req->fetch())
		{
			$photos[] = new Photo($donnees);
		}
		$req->closeCursor();
		return $photos;
	}
	
	//getter
	
	//setter
	
}