<?php
class Database
{
	//Variбveis privadas
	
	/**
    * Nome do banco de dados.
    * @var string
    */	
	private $database;

	/**
    * Objeto PDO referente a conexгo com o banco de dados.
    * @var PDO
    */	
	private $connection;
	
	//Variбveis estбticas
	
	private static $instance = null;	
	
	//Mйtodo do Singleton
	public static function getInstance()
	{
		if(self::$instance == null)
			self::$instance = new Database(Config::$Host, Config::$Username, Config::$Password, Config::$Database);
		return self::$instance;
	}
	
	//Construtor
	private function __construct($host, $username, $password, $database) 
	{
		$this->database = $database;
		$this->connection = new PDO("mysql:host=$host;dbname=".$database, $username, $password);
		$this->connection->query("SET NAMES UTF8");
	}
	
	//Mйtodos pъblicos
	
	/*
	Funзгo executeQuery: Funзгo para executar uma query sem retorno de dados (INSERT, UPDATE, etc.).
	Entradas:
	- sql (string) - Query para execuзгo com seus valores de parвmetro como "?" ou ":nomdedoparametro".
	- params (array) - Array contendo apenas os valores de parвmetros (caso a query seja modelada com "?") ou uma array associativa (caso os valores estejam no formato :nomedoparametro).
	Saнda: booleano, retorna TRUE em caso de sucesso ou FALSE em caso de falha. 
	*/
	function executeQuery($sql, $params = array())
	{
		$query = $this->connection->prepare($sql);	
		return $query->execute($params);
	}
	/*
	Funзгo readRequest: Funзгo para executar uma query com retorno de dados (SELECT, etc.).
	Entradas:
	- sql (string) - Query para execuзгo com seus valores de parвmetro como "?" ou ":nomdedoparametro".
	- params (array) - Array contendo apenas os valores de parвmetros (caso a query seja modelada com "?") ou uma array associativa (caso os valores estejam no formato :nomedoparametro).
	Saнda: Retorna uma array com os dados selecionados (se retornar um array com 0 elementos й que nгo hб valores selecionados) ou FALSE caso aconteзa algum erro.
	*/
	function readRequest($sql, $params = array())
	{
		$query = $this->connection->prepare($sql);		
		$query->execute($params);
		$result = $query->fetchAll();
		return $result;
	}
	/*
	Funзгo getLastError: Funзгo que retorna o ъltimo erro ocorrido com alguma consulta.
	Entrada: nenhuma.
	Saнda: string, contendo o ID do erro seguido do detalhamento dele.
	*/
	function getLastError()
	{
		return $this->connection->errorCode().": ".$this->connection->errorInfo()[2];
	}
	/*
	Funзгo getlastInsertId: Funзгo que retorna o ъltimo ID inserido ou atualizado em alguma consulta.
	Entrada: nenhuma.
	Saнda: string, contendo o ъtlimo ID inserido ou alterado.
	*/
	function getLastInsertId()
	{
		return $this->connection->lastInsertId();
	}
}
?>