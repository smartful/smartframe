<?php
class Archive
{
	/******************************************************************
	*					ATTRIBUTS
	*******************************************************************/
	protected $id;
	protected $titre;
	protected $date_ajout;
	
	const TITRE_VIDE = 1;
	
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
	public function date_ajout(){ return $this->date_ajout ;}

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
	
	public function setDate_ajout($date_ajout)
	{
		if (is_string($date_ajout) && preg_match("#le [0-9]{2}/[0-9]{2}/[0-9]{4} à [0-9]{2}h[0-9]{2}#", $date_ajout))
		{
			$this->date_ajout = $date_ajout;
		}
	}

}