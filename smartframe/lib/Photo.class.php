<?php
class Photo
{
	/******************************************************************
	*					ATTRIBUTS
	*******************************************************************/
	protected $id;
	protected $id_album;
	protected $titre;
	
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
	public function id_album(){ return $this->id_album ;}
	public function titre(){ return $this->titre ;}

	//setter
	public function setId($id)
	{
		$id = (int) $id;
		$this->id = $id;
	}
	
	public function setId_album($id_album)
	{
		$id_album = (int) $id_album;
		$this->id_album = $id_album;
	}
	
	public function setTitre($titre)
	{
		if(is_string($titre))
		{
			$this->titre = $titre;
		}
	}

}