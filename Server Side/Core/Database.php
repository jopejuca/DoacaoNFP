<?php
class Database
{
	//Vari�veis privadas
	
	/**
    * Nome do banco de dados.
    * @var string
    */	
	private $database;

	/**
    * Objeto PDO referente a conex�o com o banco de dados.
    * @var PDO
    */	
	private $connection;
	
	//Construtor
	
	function __construct($host, $username, $password, $database) 
	{
		$this->database = $database;
		$this->connection = new PDO("mysql:host=$host;dbname=".$database, $username, $password);
	}
	
	//M�todos p�blicos
	
	/*
	Fun��o executeQuery: Fun��o para executar uma query sem retorno de dados (INSERT, UPDATE, etc.).
	Entradas:
	- sql (string) - Query para execu��o com seus valores de par�metro como "?" ou ":nomdedoparametro".
	- params (array) - Array contendo apenas os valores de par�metros (caso a query seja modelada com "?") ou uma array associativa (caso os valores estejam no formato :nomedoparametro).
	Sa�da: booleano, retorna TRUE em caso de sucesso ou FALSE em caso de falha. 
	*/
	function executeQuery($sql, $params)
	{
		$query = $this->connection->prepare($sql);		
		return $query->execute($params);
	}
	/*
	Fun��o readRequest: Fun��o para executar uma query com retorno de dados (SELECT, etc.).
	Entradas:
	- sql (string) - Query para execu��o com seus valores de par�metro como "?" ou ":nomdedoparametro".
	- params (array) - Array contendo apenas os valores de par�metros (caso a query seja modelada com "?") ou uma array associativa (caso os valores estejam no formato :nomedoparametro).
	Sa�da: Retorna uma array com os dados selecionados (se retornar um array com 0 elementos � que n�o h� valores selecionados) ou FALSE caso aconte�a algum erro.
	*/
	function readRequest($sql, $params)
	{
		$query = $this->connection->prepare($sql);		
		$query->execute($params);
		$result = $query->fetchAll();
		return $result;
	}
	/*
	Fun��o getLastError: Fun��o que retorna o �ltimo erro ocorrido com alguma consulta.
	Entrada: nenhuma.
	Sa�da: string, contendo o ID do erro seguido do detalhamento dele.
	*/
	function getLastError()
	{
		return $this->connection->errorCode().": ".$this->connection->errorInfo()[2];
	}	
}
?>