<?php
class ManagerArchives
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
	
	public function add(Archive $archive)
	{
		$req = $this->bdd->prepare("INSERT INTO archives(titre,date_ajout)
										VALUES(:titre,NOW())");
		$req->execute(array('titre' => $archive->titre()));
		$req->closeCursor();
	}
	
	public function update(Archive $archive)
	{
		$req = $this->bdd->prepare("UPDATE archives SET titre = :titre WHERE id = :id");
		$req->execute(array(
								'titre' => $archive->titre(),
								'id' => $archive->id()
								));
		$req->closeCursor();
	}
	
	public function delete(archive $archive)
	{
		$req = $this->bdd->prepare("DELETE FROM archives WHERE id = :id");
		$req->execute(array('id' => $archive->id()));
		$req->closeCursor();
	}
	
	public function count()
	{
		$req = $this->bdd->query("SELECT COUNT(*) FROM archives");
		return $req->fetchColumn();
	}
	
	public function exist($id)
	{
		$req = $this->bdd->prepare("SELECT COUNT(*) FROM archives WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		return (bool) $req->fetchColumn();
	}
	
	public function get($id)
	{
		$req = $this->bdd->prepare("SELECT id,titre,DATE_FORMAT(date_ajout,'le %d/%m/%Y à %Hh%i') AS date_ajout
									FROM archives WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		$donnees = $req->fetch();
		$req->closeCursor();
		return new Archive($donnees);
	}
	
	public function getList()
	{
		$archives = array();
		$req = $this->bdd->query("SELECT id,titre,DATE_FORMAT(date_ajout,'le %d/%m/%Y à %Hh%i') AS date_ajout
									FROM archives ORDER BY date_ajout DESC");
		while($donnees = $req->fetch())
		{
			$archives[] = new Archive($donnees);
		}
		$req->closeCursor();
		return $archives;
	}
	
	//getter
	
	//setter
	
}