<?php
class Config
{
	//Variáveis estáticas
	/**
    * Nome do aplicativo. Será mostrado no título e demais localizações.
    * @var string
    */	
	public static $AppName = "Doação NFP";
	/**
    * Host do banco de dados.
    * @var string
    */	
	public static $Host = "127.0.0.1";
	/**
    * Usuário do banco de dados.
    * @var string
    */	
	public static $Username = "root";
	/**
    * Senha do banco de dados.
    * @var string
    */	
	public static $Password = "";
	/**
    * Nome do banco de dados.
    * @var string
    */	
	public static $Database = "nfp";
	/**
    * Caminho para um arquivo onde logs de erros do PHP serão salvos. Necessita permissão de escrita.
    * @var string
    */	
	public static $LogPhpErrors = "";
	/**
    * Chave utilizada para gerar um hash junto com o conteúdo das requisições feitas pelo app. Deve ser a mesma do aplicativo.
    * @var string
    */	
	public static $SecureKey = "";
	/**
    * Caminho para uma pasta temporária onde o armazenamento dos cookies do Bot para acesso do site da Fazenda será feito. Necessita permissão de escrita.
    * @var string
    */	
	public static $CookiesTempPath;
}

Config::$CookiesTempPath = sys_get_temp_dir();

if(Config::$LogPhpErrors != "")
{
	ini_set('log_errors', true);
	ini_set('error_log', Config::$LogPhpErrors);
}
?>