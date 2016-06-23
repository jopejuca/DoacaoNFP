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
    * Data de recebimento da doa��o no formato UNIX.
    * @var integer
    */	
	private $date;
	/**
    * Status da doa��o no banco de dados (0 = recebida, 1 = enviada a Fazenda).
    * @var integer
    */	
	private $status;
	/**
    * Status da doa��o na Fazenda (0 = em processamento, 1 = contabilizada, 2 = n�o aceita).
    * @var integer
    */	
	private $remoteStatus;
	/**
    * IP do doador
    * @var string
    */	
	private $ip;	
	/**
    * Mensagem enviada pelo doador (opcional).
    * @var string
    */
	private $message;
	
	//Construtor
	
	function __construct($ong, $code, $date, $status, $remoteStatus, $ip, $msg) 
	{
		$this->ongId = $ong;
		$this->code = $code;
		$this->date = $date;		
		$this->status = $status;
		$this->remoteStatus = $remoteStatus;
		$this->ip = $ip;
		$this->message = $msg;
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
	function getRemoteStatus()
	{
		return $this->remoteStatus;
	}
	function getIp()
	{
		return $this->ip;
	}
	function getMessage()
	{
		return $this->message;
	}
	
	//Setters	
	function setStatus($value)
	{
		if($value < 0 || $value > 1)
			return;
		
		$this->status = $value;
	}
	function setRemoteStatus($value)
	{
		if($value < 0 || $value > 2)
			return;
		
		$this->remoteStatus = $value;
	}
}
?>