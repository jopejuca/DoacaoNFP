<?php
class FazendaBot
{
	//Variáveis privadas
	
	/**
     * Caminho para o arquivo onde os cookies serão armazenados.
     * @var string
     */
	 private $cookies;	
	/**
     * Nome de usuário (CPF) do site da Fazenda.
     * @var string
     */
	private $username;
	/**
     * Senha do site da Fazenda.
     * @var string
     */
	private $password;
	/**
     * Variável utilizada para login no site da Fazenda, contida no formulário de login.
     * @var string
     */	
	private $viewState = "";
	/**
     * Variável utilizada para login no site da Fazenda, contida no formulário de login.
     * @var string
     */	
	private $viewStateGenerator = "";
	/**
     * Variável utilizada para login no site da Fazenda, contida no formulário de login.
     * @var string
     */		
	private $eventValidation = "";
	/**
     * Identificador do browser do requisitante.
     * @var string
     */	
	private $userAgent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";	
	/**
     * Indica se o bot está autenticado ou não.
     * @var boolean
     */	
	private $isLogged = false;
	/**
     * Endereço da imagem do captcha caso ele apareça.
     * @var string
     */		
	private $captchaUrl = "";
	/**
     * Detalhes do último erro ocorrido no bot.
     * @var string
     */	
	private $lastError;
	
	//Construtor
	
	function __construct($sessionId, $user, $pass) 
	{
		$this->cookies = sys_get_temp_dir().'/'.$sessionId.'.txt';
		$this->username = $user;
		$this->password = $pass;		
		$this->isLogged = false;
		$this->checkSession();
	}
	
	//Métodos públicos
	
	//Getters
	function getIsLogged()
	{
		return $this->isLogged;
	}
	function getCaptchaUrl()
	{
		return $this->captchaUrl;
	}
	function getlastError()
	{
		return $this->lastError;
	}
	/*
	Função checkSession: Função para verificar se a sessão atual do bot é válida, atualizando a variável $isLogged e armazenando valores para futuro login.
	Entrada: nenhuma.
	Saída: nenhuma.
	*/
	public function checkSession()
	{
		//Verifica se há alguma sessão aberta entrando na página inicial...
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/Inicio.aspx");
		try
		{
			$doc = new DOMDocument();
			@$doc->loadHTML($result);
			
			//Se não houver campo de login no retorno, então estamos logados...
			$this->isLogged = $doc->getElementById('divLogin') == NULL;
			
			//Se não está logado, armazena informações para futuro login.
			if(!$this->isLogged)
			{			
				$this->viewState = $doc->getElementById('__VIEWSTATE')->getAttribute("value");
				$this->viewStateGenerator = $doc->getElementById('__VIEWSTATEGENERATOR')->getAttribute("value");
				$this->eventValidation = $doc->getElementById('__EVENTVALIDATION')->getAttribute("value");
				
				//Caso captcha apareça, armazena-o para futuro login.
				$captcha = $doc->getElementById('captchaNFP');
				
				if($captcha != NULL)			
					$this->captchaUrl = $captcha->getAttribute("src");		
			}
		}
		catch(Exception $e)
		{
		}		
	}
	/*
	Função doLogin: Função para efetuar o login no site da Fazenda com os dados fornecidos pelo usuário. Caso haja captcha, efetua o login com o valor digitado pelo usuário.
	Entrada: (Opcional) captchaValue (string) - String contendo o texto da imagem CAPTCHA digitada pelo usuário.
	Saída: boolean, indicando se o login foi feito com suceso ou não.
	*/
	public function doLogin($captchaValue = null)
	{
		//Já está logado, então não precisa refazer o login...
		if($this->isLogged)
			return true;
		
		if($this->captchaUrl != "" && $captchaValue == null)
		{
			echo "Digite o captcha: <img src='$captchaUrl'></img>";
			return false;
		}
		
		//Parâmetros do formulário de login
		$loginInfo = array(
			'ctl00$ConteudoPagina$Login1$UserName' => $this->username,
			'ctl00$ConteudoPagina$Login1$Password' => $this->password,
			'ctl00$ConteudoPagina$Login1$Login' => "Acessar",
			'ctl00$ConteudoPagina$Login1$rblTipo' => "rdBtnNaoContribuinte",
			'__EVENTTARGET' => '',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATE' => urlencode($this->viewState),			
			'__VIEWSTATEGENERATOR' => urlencode($this->viewStateGenerator),
			'__EVENTVALIDATION' => urlencode($this->eventValidation)
			);
			
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/login.aspx", $loginInfo);

		$doc = new DOMDocument();
		@$doc->loadHTML($result);
		
		//Verificação de erro no login...
		$errorMessage = $doc->getElementById('lblErroMaster');
		if(strpos($result, "Ocorreu uma falha no processamento da requisi") !== FALSE)
		{
			$this->lastError = "Ocorreu uma falha no processamento da requisição.";
			$this->isLogged = false;
			return false;
		}
		if($errorMessage != NULL)
		{
			$this->lastError = utf8_decode($errorMessage->nodeValue);
			$this->isLogged = false;
			return false;
		}
		
		$this->isLogged = true;	
		
		return true;
	}
	/*
	Função insertDonation: Função para inserir uma doação para um CNPJ especificado.
	Entradas: 
	- code (string) - Código da nota de 44 caracteres formatado.
	- cnpj (string) - CNPJ da ong.
	Saída: boolean, FALSE se houve algum erro (detalhes no lastError) ou TRUE caso nada for reportado.
	*/
	public function insertDonation($code, $cnpj)
	{
		//Tratamento de erros
		if(!$this->isLogged)
		{
			$this->lastError = "O bot não está logado. Chame a função doLogin() antes de executar alguma requisição.";			
			return false;
		}		
		
		//GET na página para pegar as variáveis necessárias do formulário de busca.
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/DoacaoNotas.aspx");
		
		try
		{
			$doc = new DOMDocument();
			@$doc->loadHTML($result);
			
			$this->viewState = $doc->getElementById('__VIEWSTATE')->getAttribute("value");
			$this->viewStateGenerator = $doc->getElementById('__VIEWSTATEGENERATOR')->getAttribute("value");			
			$codeFieldId =  $doc->getElementById('divDocComChave')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;		
			$codeDate =  $doc->getElementById('infoDtNota')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;		
			$codeNr =  $doc->getElementById('infoNrNota')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;		
			$codeVal =  $doc->getElementById('infoVlNota')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;		
		}
		catch(Exception $e)
		{
		}
		
		$searchInfo = array(
		$codeFieldId => $code,
		$codeDate => '',
		$codeNr => '',
		$codeVal => '',
		'ctl00$ConteudoPagina$txtCNPJEntidadeFilantropica' 		=> $cnpj,
		'ctl00$ConteudoPagina$ddlTpNota' 											=> "CF",
		'ctl00$ConteudoPagina$btnSalvarNota'	=> 'Registrar Doação',		
		'ctl00$ConteudoPagina$hddKeyValueCtx'										=> '',
		'__EVENTTARGET' => '',
		'__EVENTARGUMENT' => '',
		'__VIEWSTATE' => urlencode($this->viewState),			
		'__VIEWSTATEGENERATOR' => urlencode($this->viewStateGenerator),		
		'__VIEWSTATEENCRYPTED' => '',		
		'__LASTFOCUS' => '',		
		);
		
		//POST com os parâmetros informados...
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/DoacaoNotas.aspx", $searchInfo);	
		
		//Processamento de erros
		$doc = new DOMDocument();
		@$doc->loadHTML($result);
		
		$errorMessage = $doc->getElementById('lblErroMaster');
		
		if(strpos($result, "Ocorreu uma falha no processamento da requisi") !== FALSE)
		{
			$this->lastError = "Ocorreu uma falha no processamento da requisição.";			
			return false;
		}
		if($errorMessage != NULL)
		{
			$this->lastError = $errorMessage->nodeValue;			
			return false;
		}
		return true;
	}	
	/*
	Função listDonations: Função para listar as doações feitas no período especificado.
	Entradas: 
	- month (int) - Mês no formato numérico. Deve estar entre 1 e 12.
	- year (int) - Ano do período. Deve estar entre 2009 e o ano atual.
	Saída: bool false se ocorrer algum erro (leia a variável lastError para mais informações) ou um array contendo as doações. O array pode conter 0 itens, indicando que não houve nenhuma doação no período.
	*/
	public function listDonations($month, $year)
	{
		//Tratamento de erros
		if(!$this->isLogged)
		{
			$this->lastError = "O bot não está logado. Chame a função doLogin() antes de executar alguma requisição.";			
			return false;
		}		
		if(!is_numeric($month) || is_numeric($month) && ($month <= 0 || $month > 12))
		{
			$this->lastError = "O valor do mês não é valido. Deve estar entre 1 e 12.";			
			return false;
		}
		if(!is_numeric($year) || is_numeric($year) && ($year < 2008 || $year > date('Y')))
		{
			$this->lastError = "O valor do ano não é valido. Deve estar entre 2009 e ".date('Y');			
			return false;
		}		
		
		//GET na página para pegar as variáveis necessárias do formulário de busca.
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/DoacaoNotasListagem.aspx");
		try
		{
			$doc = new DOMDocument();
			@$doc->loadHTML($result);
			
			$this->viewState = $doc->getElementById('__VIEWSTATE')->getAttribute("value");
			$this->viewStateGenerator = $doc->getElementById('__VIEWSTATEGENERATOR')->getAttribute("value");
			$this->eventValidation = $doc->getElementById('__EVENTVALIDATION')->getAttribute("value");			
		}
		catch(Exception $e)
		{
		}
		
		$searchInfo = array(
		'ctl00$ConteudoPagina$ddlMes' 		=> str_pad($month, 2, '0', STR_PAD_LEFT),
		'ctl00$ConteudoPagina$ddlAno' 		=> $year,
		'ctl00$ConteudoPagina$btnBuscar'	=> 'Buscar Notas',
		'__EVENTTARGET' => '',
		'__EVENTARGUMENT' => '',
		'__VIEWSTATE' => urlencode($this->viewState),			
		'__VIEWSTATEGENERATOR' => urlencode($this->viewStateGenerator),
		'__EVENTVALIDATION' => urlencode($this->eventValidation)
		);
		
		//POST com os parâmetros informados...
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/DoacaoNotasListagem.aspx", $searchInfo);
		
		$doc = new DOMDocument();
		@$doc->loadHTML($result);
		
		$notasResult = $doc->getElementById("gdvConsultaNotas");
		
		$returnArray = array();
		
		if($notasResult == null)
		{
			$this->lastError = "Ocorreu uma falha no processamento da requisição.";			
			return false;
		}	
		
		if(strpos($notasResult->nodeValue, "Nenhum registro") === false)
		{
			//ToDO: Parsing da lista de doações.
		}
		
		return $returnArray;
	}	
	/*
	Função closeSession: Função para apagar arquivos temporários do bot e demais limpezas pertinentes.
	Entrada: nenhuma.		
	Saída: nenhuma.
	*/
	public function closeSession()
	{
		unlink($this->cookies);
	}	
	
	//Métodos privados
	
	/*
	Função postData: Função para fazer as requisições GET e POST para o servidor da Fazenda.
	Entradas:
		- url (string) - URL da página onde a requisição será feita.
		- (Opcional) postValues (array) - Array contendo os valores da requisição POST, caso necessário.
	Saída: boolean, indicando se o login foi feito com suceso ou não.
	*/
	private function postData($url, $postValues = null)
	{
		//Iniciando requisição CURL GET.
		$ch = curl_init();

		//Setando algumas opções (URL, User Agent, Cookies, etc.)
		$options = array(
            CURLOPT_RETURNTRANSFER => true,     // Retornar web page.            
            CURLOPT_FOLLOWLOCATION => true,     // Seguir redirects.
			CURLOPT_RETURNTRANSFER => true,	   // Setar 'Referer' no redirect.
            CURLOPT_CONNECTTIMEOUT => 30,      // Timeout de conexão (s).
            CURLOPT_TIMEOUT        => 30,      // Timeout de resposta (s).
            CURLOPT_MAXREDIRS      => 10,       // Máx. de 10 redirects.            
            CURLOPT_SSL_VERIFYPEER => false,     // Verificação de certificado SSL.
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_URL 		   => $url,
			CURLOPT_REFERER		   => $url,
			CURLOPT_USERAGENT      => $this->userAgent,
			CURLOPT_COOKIEJAR      => $this->cookies,
			CURLOPT_COOKIEFILE 	   => $this->cookies
		);
			
		curl_setopt_array( $ch, $options );	
		
		//Se houver valores no post, então adiciona-os a requisição, transformando-a em POST.		
		if($postValues != null)
		{
			$fields_string = "";
			foreach($postValues as $key=>$value)			
				$fields_string .= $key.'='.$value.'&';
			
			rtrim($fields_string, '&');
			
			curl_setopt($ch,CURLOPT_POST, count($postValues));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		}
		
		//Faz a requisição
		$result = curl_exec($ch);
		
		//Fecha a requsição.
		curl_close($ch);
		
		return $result;
	}	
}
?>
