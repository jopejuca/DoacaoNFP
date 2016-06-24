<?php
require_once ('../core/Config.php');
class FazendaBot
{
	//Variáveis privadas
	/**
     * ID de sessão gerado pelo PHP vinculada ao usuário.
     * @var string
     */
	 private $sessionId;
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
     * Variáveis utilizadas para login no site da Fazenda, contida no formulário de login, inserir nota, etc.
     * @var string
     */	
	private $viewState;	
	private $viewStateGenerator;	
	private $eventValidation;
	private $codeFieldId;
	private $codeCnpj;		
	private $codeDate;
	private $codeNr;	
	private $codeVal;
	private $DCSIMG;
	/**
     * Identificador do browser do requisitante.
     * @var string
     */	
	private $userAgent = "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0";	
	/**
     * Indica se o bot está autenticado ou não.
     * @var boolean
     */	
	private $isLogged;
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
		$this->sessionId = $sessionId;
		$this->cookies = Config::$TempPath.'/'.$sessionId.'.txt';
		$this->username = $user;
		$this->password = $pass;	
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
	//Setters
	function setIsLogged($value)
	{
		$this->isLogged = $value;
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
			'__VIEWSTATE' => ($this->viewState),			
			'__VIEWSTATEGENERATOR' => ($this->viewStateGenerator),
			'__EVENTVALIDATION' => ($this->eventValidation)
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
		
		if($errorMessage != NULL && $errorMessage->nodeValue != "")
		{
			$this->lastError = $errorMessage->nodeValue;
			$this->isLogged = false;
			return false;			
		}
		
		$this->isLogged = true;	
		
		return true;
	}
	public function getCapctha($pos = 1)
	{
		
		//Tratamento de erros
		if(!$this->isLogged)
		{
			$this->lastError = "O bot não está logado. Chame a função doLogin() antes de executar alguma requisição.";			
			return false;
		}
		
		//GET na primeira página...
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/CadastroNotaEntidadeAviso.aspx");
		
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
		
		$firstInfo = array(
			'ctl00$ConteudoPagina$btnOk' => 'Prosseguir',			
			'__EVENTTARGET' => '',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATE' => ($this->viewState),			
			'__VIEWSTATEGENERATOR' => ($this->viewStateGenerator),
			'__EVENTVALIDATION' => ($this->eventValidation)
			);
		
		//POST com OK...		
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/CadastroNotaEntidadeAviso.aspx", $firstInfo);		
			
		try
		{
			$doc = new DOMDocument();
			@$doc->loadHTML($result);
			
			$this->viewState = $doc->getElementById('__VIEWSTATE')->getAttribute("value");
			$this->viewStateGenerator = $doc->getElementById('__VIEWSTATEGENERATOR')->getAttribute("value");
			$this->eventValidation = $doc->getElementById('__EVENTVALIDATION')->getAttribute("value");
			
			if($doc->getElementById('ddlEntidadeFilantropica') == null)
			{
				$this->lastError = "Ocorreu um erro durante a requisição do Captcha.";			
				return false;
			}
			
			$id = $doc->getElementById('ddlEntidadeFilantropica')->getElementsByTagName("option")->item($pos)->attributes->getNamedItem("value")->nodeValue;			
		}
		catch(Exception $e)
		{
		}		
		
		$postInfo = array(
			'ctl00$ConteudoPagina$hfEntidadeFilantropicaSelecionada' => "".$id,
			'ctl00$ConteudoPagina$ddlEntidadeFilantropica' => "".$id,
			'ctl00$ConteudoPagina$btnNovaNota' => 'Nova Nota',
			'ctl00$ConteudoPagina$ddlMes' 		=> str_pad(date("m"), 2, '0', STR_PAD_LEFT),
			'ctl00$ConteudoPagina$ddlAno' 		=> date("Y"),
			'__EVENTTARGET' => '',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATE' => ($this->viewState),			
			'__VIEWSTATEGENERATOR' => ($this->viewStateGenerator),
			'__EVENTVALIDATION' => ($this->eventValidation)
			);
		
		//POST na página para pegar as variáveis necessárias do formulário de preenchimento.
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/ListagemNotaEntidade.aspx", $postInfo);
		
		try
		{
			$doc = new DOMDocument();
			@$doc->loadHTML($result);
			
			$this->viewState = $doc->getElementById('__VIEWSTATE')->getAttribute("value");
			$this->viewStateGenerator = $doc->getElementById('__VIEWSTATEGENERATOR')->getAttribute("value");
			$this->eventValidation = $doc->getElementById('__EVENTVALIDATION')->getAttribute("value");			
			$this->codeFieldId =  $doc->getElementById('divDocComChave')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;
			$this->codeCnpj =  $doc->getElementById('divCNPJEstabelecimento')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;		
			$this->codeDate =  $doc->getElementById('divtxtDtNota')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;		
			$this->codeNr =  $doc->getElementById('divtxtNrNota')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;		
			$this->codeVal =  $doc->getElementById('divtxtVlNota')->getElementsByTagName("input")->item(0)->attributes->getNamedItem("name")->nodeValue;
			$this->DCSIMG =  $doc->getElementById('DCSIMG')->getAttribute("src");
			$captchaVal = $doc->getElementById('captchaNFP');			
			
			if($captchaVal != NULL)
			{
				return $captchaVal->getAttribute("src");
			}
			
			
		}
		catch(Exception $e)
		{
		}
		
		return "";
	}
	/*
	Função insertDonation: Função para inserir uma doação para um CNPJ especificado.
	Entradas: 
	- code (string) - Código da nota de 44 caracteres formatado.
	- captcha (string) - (Opcional)Resposta do captcha fornecida pelo usuário.
	- pos (int) - (Opcional)Posição da entidade na lista de entidades em que o CPF está cadastrado.
	Saída: boolean, FALSE se houve algum erro (detalhes no lastError) ou TRUE caso nada for reportado.
	*/
	public function insertDonation($code, $captcha = null, $pos = 1)
	{
		//Tratamento de erros
		if(!$this->isLogged)
		{
			$this->lastError = "O bot não está logado. Chame a função doLogin() antes de executar alguma requisição.";			
			return false;
		}
		if(preg_match('/([0-9][0-9][0-9][0-9]-){10}[0-9][0-9][0-9][0-9]/', $code) == 0)
		{
			$this->lastError = "O código da nota está no formato incorreto. Deve ser formatado da seguinte maneira: 0000-0000-0000-0000-0000-0000-0000-0000-0000-0000-0000";			
			return false;
		}
		
		$insertInfo = array(
		'__LASTFOCUS' => '',
		'__VIEWSTATE' => $this->viewState,
		'__VIEWSTATEGENERATOR' => $this->viewStateGenerator,
		'__EVENTTARGET' => '',
		'__EVENTARGUMENT' => '',
		'__VIEWSTATEENCRYPTED' => '',
		'__EVENTVALIDATION' => $this->eventValidation,
		'ctl00$ConteudoPagina$ckbxddlEntidadeFilantropica' => 'on',
		$this->codeFieldId => $code,
		$this->codeCnpj => '',
		'ctl00$ConteudoPagina$ckbxtxtCNPJEstabelecimento' => 'on',
		'ctl00$ConteudoPagina$ddlTpNota' 	=> "CF",
		'ctl00$ConteudoPagina$ckbxddlTpNota' => 'on',
		$this->codeDate => '',
		'ctl00$ConteudoPagina$ckbxtxtDtNota' => 'on',
		$this->codeNr => '',
		$this->codeVal => '',		
		'ctl00$ConteudoPagina$btnSalvarNota' => ('Salvar Nota'),							
		);
		
		if($captcha != null)
			$insertInfo['ctl00$ConteudoPagina$CaptchaSefaz$ImagemRand'] = $captcha;
		
		//JSON definindo o campo
		$jsonData = array(
		"valor"=>"",
		"campo" => substr($this->codeFieldId, strlen($this->codeFieldId) - 32, 32)
		);	
		
		$this->postDataJson("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/CadastroNotaEntidade.aspx/DefinirValor", $jsonData);
		
		//POST com os parâmetros informados...
		$result = $this->postData("https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/CadastroNotaEntidade.aspx", $insertInfo, "https://www.nfp.fazenda.sp.gov.br/EntidadesFilantropicas/ListagemNotaEntidade.aspx");	
		
		//Processamento de erros
		$doc = new DOMDocument();
		@$doc->loadHTML($result);
		
		$errorMessage = $doc->getElementById('lblErroMaster');
		$errorMessage2 = $doc->getElementById('lblErro');
		
		if(strpos($result, "Ocorreu uma falha no processamento da requisi") !== FALSE)
		{
			$this->lastError = "Ocorreu uma falha no processamento da requisição.";			
			return false;
		}
		
		if($errorMessage != NULL && $errorMessage->nodeValue != "")
		{
			$this->lastError = $errorMessage->nodeValue;			
			return false;			
		}
		
		if($errorMessage2 != NULL && $errorMessage2->nodeValue != "")
		{
			$this->lastError = $errorMessage2->nodeValue;			
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
		'__VIEWSTATE' => ($this->viewState),			
		'__VIEWSTATEGENERATOR' => ($this->viewStateGenerator),
		'__EVENTVALIDATION' => ($this->eventValidation)
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
	
	public function downloadCaptcha($captchaUrl)
	{
		$url = "https://www.nfp.fazenda.sp.gov.br".str_replace("../","/", $captchaUrl);
		
		$saveto = Config::$TempPath."/".$this->sessionId.".jpg";
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER ,false);     // Verificação de certificado SSL.);
		
		$raw = curl_exec($ch);
		curl_close ($ch);
		
		if(file_exists($saveto))
		{
			unlink($saveto);
		}
		$fp = fopen($saveto,'x');
		fwrite($fp, $raw);
		fclose($fp);
		
		return $saveto;
	}
	//Métodos privados
	private function postDataJson($url, $postValues, $referer = null)
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
			CURLOPT_COOKIEFILE 	   => $this->cookies,
			CURLOPT_HTTPHEADER => array('Content-Type: application/json')
		);
			
		curl_setopt_array( $ch, $options );
		
		if($referer != null)
			curl_setopt($ch, CURLOPT_REFERER, $referer);
			
		//Se houver valores no post, então adiciona-os a requisição, transformando-a em POST.		
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($postValues));	
		
		//Faz a requisição
		$result = curl_exec($ch);
		
		//Fecha a requsição.
		curl_close($ch);
		
		return $result;
	}
	/*
	Função postData: Função para fazer as requisições GET e POST para o servidor da Fazenda.
	Entradas:
		- url (string) - URL da página onde a requisição será feita.
		- (Opcional) postValues (array) - Array contendo os valores da requisição POST, caso necessário.
	Saída: boolean, indicando se o login foi feito com suceso ou não.
	*/
	private function postData($url, $postValues = null, $referer = null)
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
		if($referer != null)
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		//Se houver valores no post, então adiciona-os a requisição, transformando-a em POST.		
		if($postValues != null)
		{
			$fields_string = "";
			foreach($postValues as $key=>$value)			
				$fields_string .= urlencode($key).'='.urlencode($value).'&';
			
			$fields_string = rtrim($fields_string, '&');
			
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
