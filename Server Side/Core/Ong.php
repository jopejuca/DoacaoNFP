<?php
class Ong
{
	//Variáveis privadas
	
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
    * CNPJ da ONG sem pontos ou traços, utilizado também como Nome de Usuário do website de gerenciamento das notas.
    * @var string
    */	
	private $cnpj;
	/**
    * Endereço do website da ONG ou página de alguma rede social, entre outros.
    * @var string
    */
	private $website;	
	/**
    * Endereço da ONG.
    * @var string
    */	
	private $address;	
	/**
    * Indica se a Ong foi autorizada ou não por algum administrador do sistema.
    * @var boolean
    */
	private $valid = FALSE;
	/**
    * Senha da Ong para acesso do website de gerenciamento.
    * @var string
    */	
	private $password;
	/**
    * E-mail para contato da ONG.
    * @var string
    */	
	private $email;
	/**
    * CPF autorizado para cadastrar doações da ONG no site da Fazenda.
    * @var string
    */	
	private $cpf;
	/**
    * Senha do CPF autorizado no site da Fazenda.
    * @var string
    */	
	private $remotePassword;
	
	//Construtor
	
	function __construct($id, $name, $cnpj, $website, $valid, $pass, $email, $cpf, $remotePass, $address) 
	{
		$this->id = $id;
		$this->name = $name;			
		$this->cnpj = $cnpj;
		$this->website = $website;
		$this->valid = $valid == 1;
		$this->password = $pass;
		$this->email = $email;
		$this->cpf = $cpf;
		$this->remotePassword = $remotePass;
		$this->address = $address;
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
	function getCnpj()
	{
		return $this->cnpj;
	}
	function getWebsite()
	{
		return $this->website;
	}
	function getValid()
	{
		return $this->valid;
	}
	function getPassword()
	{
		return $this->password;
	}
	function getEmail()
	{
		return $this->email;
	}
	function getCpf()
	{
		return $this->cpf;
	}	
	function getRemotePassword()
	{
		return $this->remotePassword;
	}
	function getAddress()
	{
		return $this->address;
	}	
	//Setters
	function setId($value)
	{
		$this->id = $value;
	}
	function setValid($value)
	{
		$this->valid = $value;
	}
	function setWebsite($value)
	{
		$this->website = $value;
	}
	function setPassword($value)
	{
		$this->password = $value;
	}
	function setEmail($value)
	{
		$this->email = $value;
	}
	function setCpf($value)
	{
		$this->cpf = $value;
	}
	function setRemotePassword($value)
	{
		$this->remotePassword = $value;
	}
	function setAddress($value)
	{
		$this->address = $value;
	}
}
?>