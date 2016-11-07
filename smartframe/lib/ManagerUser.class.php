<?php

class ManagerUser
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

	public function add(User $user)
	{
		$req = $this->bdd->prepare("INSERT INTO adherents(email,pass,prenom,nom,age,sexe,date_inscription)
										VALUES(:email,:pass,:prenom,:nom,:age,:sexe,NOW())") ;
		$req->execute(array(
								'email' => $user->email(),
								'pass' => sha1($user->pass()),
								'prenom' => $user->prenom(),
								'nom' => $user->nom(),
								'age' => $user->age(),
								'sexe' => $user->sexe()
								));
		$req->closeCursor();
	}

	public function supprimer(User $user)
	{
		$req = $this->bdd->prepare("DELETE FROM adherents WHERE id = :id");
		$req->execute(array('id' => $user->id()));
		$req->closeCursor();
	}

	public function exist($id)
	{
		$req = $this->bdd->prepare("SELECT COUNT(*) FROM adherents WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		return (bool) $req->fetchColumn();
	}

	public function count()
	{
		$req = $this->bdd->query("SELECT COUNT(*) FROM adherents");
		return $req->fetchColumn();
	}

	public function get($id)
	{
		$req = $this->bdd->prepare("SELECT id,email,pass,prenom,nom,age,sexe,
											DATE_FORMAT(date_inscription,'le %d/%m/%Y à %Hh%i') AS date_inscription
									FROM adherents WHERE id = :id");
		$req->execute(array('id' => (int) $id));
		$donnees = $req->fetch();
		$req->closeCursor();
		return new User($donnees);
	}

	public function getEmail($email)
	{
		$req = $this->bdd->prepare("SELECT id,email,pass,prenom,nom,age,sexe,
											DATE_FORMAT(date_inscription,'le %d/%m/%Y à %Hh%i') AS date_inscription
									FROM adherents WHERE email = :email");
		$req->execute(array('email' => $email));
		$donnees = $req->fetch();

		//gestion d'un email n'étant pas présent dans la base de données
		if($donnees == FALSE)
		{
			return FALSE;
		}

		$req->closeCursor();
		return new User($donnees);
	}

	public function verifPass(User $user, $pass)
	{
		//Vérifie que le mot de passe entrer par l'utilisateur, correspond bien à l'adresse email
		if($user->pass() == sha1($pass))
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public function update(User $user)
	{
		$req = $this->bdd->prepare("UPDATE adherents SET pass = :pass, prenom = :prenom, nom = :nom, age = :age, sexe = :sexe
										WHERE id = :id");
		$req->execute(array(
								'pass' => sha1($user->pass()),
								'prenom' => $user->prenom(),
								'nom' => $user->nom(),
								'age' => $user->age(),
								'sexe' => $user->sexe(),
								'id' => $user->id()
								));
		$req->closeCursor();
	}

	public function getList()
	{
		$users = array();
		$req = $this->bdd->query("SELECT id,email,pass,prenom,nom,age,sexe,
											DATE_FORMAT(date_inscription,'le %d/%m/%Y à %Hh%i') AS date_inscription,validation
									FROM adherents");
		while($donnees = $req->fetch())
		{
			$users[] = new User($donnees);
		}
		$req->closeCursor();
		return $users;
	}

	public function estValide(User $user)
	{
		//Renvoi un booléen
		//Vérifie si l'utilisateur à bien été validé par l'administrateur du site
		$req = $this->bdd->prepare("SELECT validation FROM adherents WHERE id = :id");
		$req->execute(array('id' => $user->id()));
		return $req->fetchColumn();
	}

	public function validation(User $user)
	{
		$req = $this->bdd->prepare("UPDATE adherents SET validation = 1 WHERE id = :id");
		$req->execute(array('id' => $user->id()));
		$req->closeCursor();
	}

}
