<?php
require_once ('../Core/Config.php');
require_once ('../Core/Donation.php');
require_once ('../Core/Database.php');

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
	
	//Métodos públicos
	
	/*
	Função getDonationsToOng: Retorna uma lista das doações feitas para a ONG especificada.
	Entradas:
	- ongId (int) - ID da ong.	
	- (Opcional) time (int) - Data limite para listar as doações no formato UNIX.
	Saída: array, contendo as doações para a ONG.
	*/
	function getDonationsToOng($ongId, $time = null)
	{
		if($time == null)
			$rawDonations = Database::getInstance()->readRequest("SELECT * FROM donations WHERE OngId=?", array($ongId));
		else
			$rawDonations = Database::getInstance()->readRequest("SELECT * FROM donations WHERE OngId=? AND Date >= ?", array($ongId, $time));
		
		$donations = array();
		
		if($rawDonations)
		{
			foreach($rawDonations as $rawDonation)			
				array_push($donations, new Donation($rawDonation["OngId"], $rawDonation["Code"], $rawDonation["Date"], $rawDonation["Status"], $rawDonation["RemoteStatus"],$rawDonation["Ip"], $rawDonation["Message"]));			
		}
		return $donations;
	}
	/*
	Função insertDonationToOng: Insere uma doação no banco de dados.
	Entradas:
	- donation (classe Donation) - Doação a ser inserida no banco de dados.	
	Saída: booleano, retorna TRUE em caso de sucesso ou FALSE em caso de falha.
	*/
	function insertDonationToOng($donation)
	{
		return Database::getInstance()->executeQuery("INSERT INTO donation VALUES (?,?,?,?,?,?,?)", array($donation->getOngId(), $donation->getCode(), $donation->getStatus(), $donation->getDate(), $donation->getIp(), $donation->getMessage(), $donation->getRemoteStatus()));
	}
	/*
	Função updateDonation: Atualiza dados de uma doação no banco de dados.
	Entradas:
	- donation (classe Donation) - Doação a ser atualizada no banco de dados.	
	Saída: booleano, retorna TRUE em caso de sucesso ou FALSE em caso de falha.
	*/
	function updateDonation($donation)
	{
		return Database::getInstance()->executeQuery("UPDATE donation SET Status=?,RemoteStatus=? WHERE Code=?", array($donation->getStatus(), $donation->getRemoteStatus(),$donation->getCode()));
	}
}
?>