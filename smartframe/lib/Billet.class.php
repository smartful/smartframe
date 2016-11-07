<?php
class Billet
{
	/******************************************************************
	*					ATTRIBUTS
	*******************************************************************/
	protected $id;
	protected $titre;
	protected $contenu;
	protected $date_ajout;
	protected $date_modif;
	
	const TITRE_VIDE = 1;
	const CONTENU_VIDE = 2;
	
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
		if(empty($this->titre) || empty($this->contenu))
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
	public function contenu(){ return $this->contenu ;}
	public function date_ajout(){ return $this->date_ajout ;}
	public function date_modif(){ return $this->date_modif ;}
	
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
	
	public function setContenu($contenu)
	{
		if(is_string($contenu))
		{
			$this->contenu = $contenu;
		}
	}
	
	public function setDate_ajout($date_ajout)
	{
		if (is_string($date_ajout) && preg_match("#le [0-9]{2}/[0-9]{2}/[0-9]{4} à [0-9]{2}h[0-9]{2}#", $date_ajout))
		{
			$this->date_ajout = $date_ajout;
		}
	}
	
	public function setDate_modif($date_modif)
	{
		if (is_string($date_modif) && preg_match("#le [0-9]{2}/[0-9]{2}/[0-9]{4} à [0-9]{2}h[0-9]{2}#", $date_modif))
		{
			$this->date_modif = $date_modif;
		}
	}

}