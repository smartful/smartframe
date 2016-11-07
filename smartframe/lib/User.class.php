<?php

class User
{
	/******************************************************************
	*					ATTRIBUTS
	*******************************************************************/
	protected $id;
	protected $prenom;
	protected $nom;
	protected $email;
	protected $pass;
	protected $sexe;
	protected $age;
	protected $date_inscription;
	protected $validation;

	/******************************************************************
	*					METHODES
	*******************************************************************/
	
	public function __construct(array $donnees)
	{
		$this->hydrate($donnees);
	}
	
	public function hydrate(array $donnees)
	{
		foreach($donnees as $key => $value)
		{
			//On récupère le nom du setter
			$method = "set".ucfirst($key);
			
			if(method_exists($this, $method))
			{
				$this->$method($value);
			}
		}
	}
	
	//GETTERS
	public function id() {return $this->id;}
	public function email() {return $this->email;}
	public function pass() {return $this->pass;}
	public function prenom() {return $this->prenom;}
	public function nom() {return $this->nom;}
	public function age() {return $this->age;}
	public function sexe() {return $this->sexe;}
	public function date_inscription() {return $this->date_inscription;}
	public function validation() {return $this->validation;}
	
	//SETTERS
	public function setId($id)
	{
		$id = (int) $id;
		$this->id = $id;
	}
	
	public function setEmail($email)
	{
		if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#",$email))
		{
			$this->email = $email;
		}
	}
	
	public function setPass($pass)
	{
		if(is_string($pass))
		{
			$this->pass = $pass;
		}
	}
	
	public function setPrenom($prenom)
	{
		if(is_string($prenom))
		{
			$this->prenom = $prenom;
		}
	}
	
	public function setNom($nom)
	{
		if(is_string($nom))
		{
			$this->nom = $nom;
		}
	}
	
	public function setAge($age)
	{
		$age = (int) $age;
		$this->age = $age;
	}
	
	public function setSexe($sexe)
	{
		if(is_string($sexe))
		{
			$this->sexe = $sexe;
		}
	}
	
	public function setDate_inscription($date_inscription)
	{
		if (is_string($date_inscription) && preg_match("#le [0-9]{2}/[0-9]{2}/[0-9]{4} à [0-9]{2}h[0-9]{2}#", $date_inscription))
		{
			$this->date_inscription = $date_inscription;
		}
	}
	
	public function setValidation($validation)
	{
		$validation = (int) $validation;
		$this->validation = $validation;
	}
}