<?php
class Donation
{
	//Vari�veis privadas
	
	/**
    * ID da ONG que recebeu a doa��o.
    * @var integer
    */	
	private $ongId;
	/**
    * C�digo de 44 caracteres referente ao documento fiscal doado.
    * @var string
    */	
	private $code;
	/**
    * Data de recebimento da doa��o.
    * @var string
    */	
	private $date;
	/**
    * Status da doa��o (0 = recebida, 1 = enviada a Fazenda).
    * @var integer
    */	
	private $status;
	/**
    * IP do doador
    * @var string
    */	
	private $ip;
	
	//Construtor
	
	function __construct($ong, $code, $date, $status, $ip) 
	{
		$this->ongId = $ong;
		$this->code = $code;
		$this->date = $date;		
		$this->status = $status;
		$this->ip = $ip;		
	}
	
	//Getters
	
	function getOngId()
	{
		return $this->ongId;
	}
	function getCode()
	{
		return $this->code;
	}
	function getDate()
	{
		return $this->date;
	}
	function getStatus()
	{
		return $this->status;
	}
	function getIp()
	{
		return $this->ip;
	}
	
	//Setters
	
	function setStatus($value)
	{
		if($value < 0 || $value > 1)
			return;
		
		$this->status = $value;
	}	
}
?>