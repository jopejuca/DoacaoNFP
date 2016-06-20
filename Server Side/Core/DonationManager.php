<?php
require_once ('Core/Config.php');
require_once ('Core/Donation.php');
require_once ('Core/Database.php');

class DonationManager
{
	//Variáveis estáticas
	
	private static $instance = null;	
	
	//Métodos estáticos
	
	//Método do Singleton
	public static function getInstance()
	{
		if(self::$instance == null)
			self::$instance = new DonationManager();
		return self::$instance;
	}
	
	//Variáveis privadas
	
	private $database;
	
	//Construtor
	
	function __construct() 
	{
		$this->database = new Database(Config::$Host, Config::$Username, Config::$Password, Config::$Database);
	}
	
	//Métodos públicos
	
	/*
	Função getDonationsToOng: Retorna uma lista das doações feitas para a ONG especificada.
	Entradas:
	- ongId (int) - ID da ong.	
	Saída: array, contendo as doações para a ONG.
	*/
	function getDonationsToOng($ongId)
	{
		$rawDonations = $this->database->readRequest("SELECT * FROM donations WHERE OngId=?", array($ongId));
		
		$donations = array();
		if($rawDonations)
		{
			foreach($rawDonations as $rawDonation)			
				array_push($donations, new Donation($rawDonation["OngId"], $rawDonation["Code"], $rawDonation["Date"], $rawDonation["Status"], $rawDonation["Ip"]));			
		}
		return $donations;
	}
	function insertDonationToOng($ongId, $donation)
	{
		
	}
	function updateDonation($donation)
	{
		
	}
}
?>