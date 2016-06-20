<?php
class Ong
{
	//Vari�veis privadas
	
	/**
    * ID da ONG no sistema.
    * @var integer
    */	
	private $id;
	/**
    * Nome da ONG.
    * @var string
    */	
	private $name;
	/**
    * Nome de usu�rio para acessar o site.
    * @var string
    */	
	private $username;
	/**
    * CNPJ da ONG.
    * @var string
    */	
	private $cnpj;
	/**
    * Endere�o do website da ONG ou p�gina de alguma rede social, entre outros.
    * @var string
    */	
	private $website;
	
	//Construtor
	
	function __construct($id, $name, $username, $cpnj, $website) 
	{
		$this->id = $id;
		$this->name = $name;
		$this->username = $username;		
		$this->cnpj = $cnpj;
		$this->website = $website;		
	}
	
	//Getters
	
	function getId()
	{
		return $this->id;
	}
	function getName()
	{
		return $this->name;
	}
	function getUsername()
	{
		return $this->username;
	}
	function getCnpj()
	{
		return $this->cnpj;
	}
	function getWebsite()
	{
		return $this->website;
	}	
}
?>