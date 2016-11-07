<?php

class ManagerAdmin 
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
	
	public function add(Admin $admin)
	{
		$req = $this->bdd->prepare("INSERT INTO admin(nom_admin,pass_admin)
										VALUES(:nom_admin,:pass_admin") ;
		$req->execute(array(
								'nom_admin' => $admin->nom_admin(),
								'pass_admin' => ($admin->pass_admin())
								));
		$req->closeCursor();
	}
	
	public function supprimer(Admin $admin)
	{
		$req = $this->bdd->prepare("DELETE FROM admin WHERE id = :id");
		$req->execute(array('id' => $admin->id()));
		$req->closeCursor();
	}
	
	public function exist($id)
	{
		$req = $this->bdd->prepare("SELECT COUNT(*) FROM admin WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		return (bool) $req->fetchColumn();
	}
	
	public function count()
	{
		$req = $this->bdd->query("SELECT COUNT(*) FROM admin");
		return $req->fetchColumn();
	}
	
	public function get($id)
	{
		$req = $this->bdd->prepare("SELECT id,nom_admin,pass_admin
									FROM admin WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		$donnees = $req->fetch();
		$req->closeCursor();
		return new User($donnees);
	}
	
	public function getLogin($login)
	{
		$req = $this->bdd->prepare("SELECT id,nom_admin,pass_admin
									FROM admin WHERE nom_admin = :login");
		$req->execute(array('login' => $login));
		$donnees = $req->fetch();
		
		//gestion d'un login n'étant pas présent dans la base de données
		if($donnees == FALSE)
		{
			return FALSE;
		}
		
		$req->closeCursor();
		return new admin($donnees);
	}
	
	public function verifPass(Admin $admin, $pass)
	{
		//Vérifie que le mot de passe entrer par l'utilisateur, correspond bien au login de l'administrateur
		if($admin->pass_admin() == $pass)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
	public function update(Admin $admin)
	{
		$req = $this->bdd->prepare("UPDATE admin SET nom_admin = :nom, pass_admin = :pass
										WHERE id = :id");
		$req->execute(array(
								'nom' => $admin->nom_admin(),
								'pass' => ($admin->pass_admin())
								));
		$req->closeCursor();
	}
	
	public function getList()
	{
		$admins = array();
		$req = $this->bdd->query("SELECT id,nom_admin,pass_admin
									FROM admin");
		while($donnees = $req->fetch())
		{
			$admins[] = new admin($donnees);
		}
		$req->closeCursor();
		return $admins;
	}
	
}