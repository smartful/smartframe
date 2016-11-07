<?php
class ManagerAlbums
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
	
	public function add(Album $album)
	{
		$req = $this->bdd->prepare("INSERT INTO albums(titre,description)
										VALUES(:titre,:description)");
		$req->execute(array(
							'titre' => $album->titre(),
							'description' => $album->description() 
							));
		$req->closeCursor();
	}
	
	public function update(Album $album)
	{
		$req = $this->bdd->prepare("UPDATE albums SET titre = :titre, description = :description WHERE id = :id");
		$req->execute(array(
								'titre' => $album->titre(),
								'description' => $album->description(),
								'id' => $album->id()
								));
		$req->closeCursor();
	}
	
	public function delete(Album $album)
	{
		$req = $this->bdd->prepare("DELETE FROM albums WHERE id = :id");
		$req->execute(array('id' => $album->id()));
		$req->closeCursor();
	}
	
	public function count()
	{
		$req = $this->bdd->query("SELECT COUNT(*) FROM albums");
		return $req->fetchColumn();
	}
	
	public function exist($id)
	{
		$req = $this->bdd->prepare("SELECT COUNT(*) FROM albums WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		return (bool) $req->fetchColumn();
	}
	
	public function get($id)
	{
		$req = $this->bdd->prepare("SELECT id,titre,description
									FROM albums WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		$donnees = $req->fetch();
		$req->closeCursor();
		return new Album($donnees);
	}
	
	public function getList()
	{
		$albums = array();
		$req = $this->bdd->query("SELECT id,titre,description
									FROM albums ORDER BY titre DESC");
		while($donnees = $req->fetch())
		{
			$albums[] = new Album($donnees);
		}
		$req->closeCursor();
		return $albums;
	}
	
	//getter
	
	//setter
	
}