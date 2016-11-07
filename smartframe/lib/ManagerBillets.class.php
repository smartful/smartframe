<?php
class ManagerBillets
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
	
	public function add(Billet $billet)
	{
		$req = $this->bdd->prepare("INSERT INTO billets(titre,contenu,date_ajout,date_modif)
										VALUES(:titre,:contenu,NOW(),NOW())");
		$req->execute(array(
								'titre' => $billet->titre(),
								'contenu' => $billet->contenu()
								));
		$req->closeCursor();
	}
	
	public function update(Billet $billet)
	{
		$req = $this->bdd->prepare("UPDATE billets SET titre = :titre, contenu = :contenu, date_modif = NOW()
										WHERE id = :id");
		$req->execute(array(
								'titre' => $billet->titre(),
								'contenu' => $billet->contenu(),
								'id' => $billet->id()
								));
		$req->closeCursor();
	}
	
	public function delete(Billet $billet)
	{
		$req = $this->bdd->prepare("DELETE FROM billets WHERE id = :id");
		$req->execute(array('id' => $billet->id()));
		$req->closeCursor();
	}
	
	public function count()
	{
		$req = $this->bdd->query("SELECT COUNT(*) FROM billets");
		return $req->fetchColumn();
	}
	
	public function exist($id)
	{
		$req = $this->bdd->prepare("SELECT COUNT(*) FROM billets WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		return (bool) $req->fetchColumn();
	}
	
	public function get($id)
	{
		$req = $this->bdd->prepare("SELECT id,titre,contenu,DATE_FORMAT(date_ajout,'le %d/%m/%Y à %Hh%i') AS date_ajout, DATE_FORMAT(date_modif,'le %d/%m/%Y à %Hh%i') AS date_modif
									FROM billets WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		$donnees = $req->fetch();
		$req->closeCursor();
		return new Billet($donnees);
	}
	
	public function getList()
	{
		$billets = array();
		$req = $this->bdd->query("SELECT id,titre,contenu,DATE_FORMAT(date_ajout,'le %d/%m/%Y à %Hh%i') AS date_ajout, DATE_FORMAT(date_modif,'le %d/%m/%Y à %Hh%i') AS date_modif 
									FROM billets ORDER BY date_ajout");
		while($donnees = $req->fetch())
		{
			$billets[] = new Billet($donnees);
		}
		$req->closeCursor();
		return $billets;
	}
	
	//getter
	
	//setter
	
}