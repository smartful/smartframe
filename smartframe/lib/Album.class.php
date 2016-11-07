<?php
class Album
{
	/******************************************************************
	*					ATTRIBUTS
	*******************************************************************/
	protected $id;
	protected $titre;
	protected $description;
	
	const TITRE_VIDE = 1;
	const DESCRIPTION_VIDE = 2;
	
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
			$method = "set".ucfirst($key);
			if(method_exists($this,$method))
			{
				$this->$method($value);
			}
		}
	}
	
	public function valide()
	{
		//Regarge si l'objet à des champs non-vides
		if(empty($this->titre))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	//getter
	public function id(){ return $this->id ;}
	public function titre(){ return $this->titre ;}
	public function description(){ return $this->description ;}

	//setter
	public function setId($id)
	{
		$id = (int) $id;
		$this->id = $id;
	}
	
	public function setTitre($titre)
	{
		if(is_string($titre))
		{
			$this->titre = $titre;
		}
	}
	
	public function setDescription($description)
	{
		if (is_string($description))
		{
			$this->description = $description;
		}
	}

}