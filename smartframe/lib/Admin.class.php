<?php

class Admin
{
	/******************************************************************
	*					ATTRIBUTS
	*******************************************************************/
	protected $id;
	protected $nom_admin;
	protected $pass_admin;

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
	public function nom_admin() {return $this->nom_admin;}
	public function pass_admin() {return $this->pass_admin;}
	
	//SETTERS
	public function setId($id)
	{
		$id = (int) $id;
		$this->id = $id;
	}
	
	public function setNom_admin($nom)
	{
		if(is_string($nom))
		{
			$this->nom_admin = $nom;
		}
	}
	
	public function setPass_admin($pass)
	{
		if(is_string($pass))
		{
			$this->pass_admin = $pass;
		}
	}
	
}