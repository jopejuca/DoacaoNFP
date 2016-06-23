<?php
require_once ('../core/Config.php');
require_once ('../core/Ong.php');
require_once ('../core/Database.php');

class OngManager
{
	//Variáveis estáticas
	
	private static $instance = null;	
	
	//Métodos estáticos
	
	//Método do Singleton
	public static function getInstance()
	{
		if(self::$instance == null)
			self::$instance = new OngManager();
		return self::$instance;
	}	
	//Métodos públicos
	
	/*
	Função getOngFromId: Retorna uma Ong pelo Id.
	Entradas:
	- ongId (int) - ID da ong.	
	Saída: classe Ong, caso a ong com o id especificado exista ou FALSE, caso não exista.
	*/
	function getOngFromId($ongId)
	{
		$rawOng = Database::getInstance()->readRequest("SELECT * FROM ongs WHERE Id=?", array($ongId));
		
		if($rawOng)		
			return new Ong($rawOng[0]["Id"], $rawOng[0]["Name"], $rawOng[0]["CNPJ"], $rawOng[0]["Website"],$rawOng[0]["Valid"],$rawOng[0]["Password"],$rawOng[0]["Email"]);
		
		return false;
	}
	/*
	Função getOngFromId: Retorna uma Ong pelo CNPJ.
	Entradas:
	- cnpj (string) - CNPJ da ONG já formatado (sem traços ou pontos).	
	Saída: classe Ong, caso a ong com o CNPJ especificado exista ou FALSE, caso não exista.
	*/
	function getOngFromCnpj($cnpj)
	{
		$rawOng = Database::getInstance()->readRequest("SELECT * FROM ongs WHERE CNPJ=?", array($cnpj));
		
		if($rawOng)		
			return new Ong($rawOng[0]["Id"], $rawOng[0]["Name"], $rawOng[0]["CNPJ"], $rawOng[0]["Website"],$rawOng[0]["Valid"],$rawOng[0]["Password"],$rawOng[0]["Email"]);
		
		return false;
	}	
	/*
	Função insertOng: Insere uma Ong no banco de dados e seta seu Id caso a operação seja bem sucedida.
	Entradas:
	- ong (classe Ong) - Ong a ser inserida no banco de dados.	
	Saída: booleano, retorna TRUE em caso de sucesso ou FALSE em caso de falha.
	*/
	function insertOng($ong)
	{
		if(Database::getInstance()->executeQuery("INSERT INTO ongs VALUES (`Name`,`CNPJ`, `Website`,`Password`,`Email`) (?,?,?,?,?)", array($ong->getName(),$ong->getCnpj(),$ong->getWebsite(),$ong->getPassword(),$ong->getEmail())))
		{
			$ong->setId(Database::getInstance()->getLastInsertId());
			return true;
		}
		return false;
	}
	/*
	Função updateOng: Atualiza dados de uma Ong no banco de dados.
	Entradas:
	- ong (classe Ong) - Ong a ser atualizada no banco de dados.	
	Saída: booleano, retorna TRUE em caso de sucesso ou FALSE em caso de falha.
	*/
	function updateOng($ong)
	{
		return Database::getInstance()->executeQuery("UPDATE ongs SET Website=?,Valid=?,Password=?,Email=? WHERE Id=?", array($ong->getWebsite(), $ong->getValid()?1:0,$ong->getPassword(),$ong->getEmail(),$ong->getId()));
	}
}
?>