<?php 
require_once ('../core/OngManager.php');

if(isset($_SESSION['ongId']))
{
	?>
	<meta http-equiv="refresh" content="1;url=/panel/dashboard">
	<script type="text/javascript">
		window.location.href = "/panel/dashboard"
	</script>
	<?php
	return;
}
function validarCPF($cpf = null) {
 
    // Verifica se um número foi informado
    if(empty($cpf)) {
        return false;
    }
 
    // Elimina possivel mascara
    $cpf = ereg_replace('[^0-9]', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
     
    // Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequências invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' || 
        $cpf == '11111111111' || 
        $cpf == '22222222222' || 
        $cpf == '33333333333' || 
        $cpf == '44444444444' || 
        $cpf == '55555555555' || 
        $cpf == '66666666666' || 
        $cpf == '77777777777' || 
        $cpf == '88888888888' || 
        $cpf == '99999999999') {
        return false;
     // Calcula os digitos verificadores para verificar se o
     // CPF é válido
     } else {   
         
        for ($t = 9; $t < 11; $t++) {
             
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
 
        return true;
    }
}
function validarCnpj($cnpj)
{
	$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
	// Valida tamanho
	if (strlen($cnpj) != 14)
		return false;
	// Valida primeiro dígito verificador
	for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
	{
		$soma += $cnpj{$i} * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;
	if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
		return false;
	// Valida segundo dígito verificador
	for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
	{
		$soma += $cnpj{$i} * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;
	return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
}

function validRegister()
{
	if(!isset($_POST['name']) || $_POST['name'] == "" || !isset($_POST['cnpj']) || $_POST['cnpj'] == "" || !isset($_POST['password']) || $_POST['password'] == "" || !isset($_POST['confPassword']) || $_POST['confPassword'] == "" || !isset($_POST['email']) || $_POST['email'] == "" || !isset($_POST['address']) || $_POST['address'] == ""||  !isset($_POST['website'])|| $_POST['website'] == ""|| !isset($_POST['cpf']) || $_POST['cpf'] == ""|| !isset($_POST['remotePassword']) || $_POST['remotePassword'] == ""|| !isset($_POST['confRemotePassword']) || $_POST['confRemotePassword'] == "")
		return "Preencha todos os campos!";
	
	if(!validarCnpj($_POST['cnpj']))
		return "CNPJ inválido!";
	
	if(strlen($_POST['password']) > 32 || strlen($_POST['password']) < 6)
		return "A senha de acesso deve conter de 6 a 32 caracteres!";
		
	if(strcmp($_POST['password'], $_POST['confPassword']) != 0)
		return "As senhas de acesso não correspondem!";
	
	if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) == FALSE)
		return "Endereço de Email inválido!";
	
	if(!validarCPF($_POST['cpf']))
		return "CPF inválido!";
	
	if(strlen($_POST['remotePassword']) > 32 || strlen($_POST['remotePassword']) < 6)
		return "A senha do site da Fazenda deve conter de 6 a 32 caracteres!";
		
	if(strcmp($_POST['remotePassword'], $_POST['confRemotePassword']) != 0)
		return "As senhas do site da Fazenda não correspondem!";
	
	$ong = OngManager::getInstance()->getOngFromCnpj($_POST['cnpj']);
	
	if($ong)	
		return "ONG com este CNPJ já cadastrada!";	
	
	return "";
}

$registerError = "";
$registerSucess = false;

if(isset($_POST['Register']))
{
	$valid = validRegister();
	
	if($valid == "")
	{
		$ong = new Ong(0, htmlentities($_POST['name']), htmlentities($_POST['cnpj']), htmlentities($_POST['website']), 0, md5($_POST['password']), htmlentities($_POST['email']), htmlentities($_POST['cpf']), htmlentities($_POST['remotePassword']), htmlentities($_POST['address']));
		$registerSucess = OngManager::getInstance()->insertOng($ong);
		
		if(!$registerSucess)
			$registerError = "Erro interno do sistema. Caso o problema persista, notifique os administradores!";
	}
	else
	{
		$registerError = $valid;
	}
}
?>
    <div id="page-wrapper">
	
        <div class="row">
		<br>
            <div class="col-fd">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Cadastro de nova ONG</h3>
                    </div>
                    <div class="panel-body">
					<?php if($registerError != "")
							{?>
							</br>
						<div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <?php echo $registerError;?>
                        
						</div>
						<?php }
						else
						{
							if($registerSucess)
							{?>
								<div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Registro efetuado com sucesso! Em breve algum administrador entrará em contato via Email para validar o cadastro.
                        
						</div><?php
							}
							else
							{?>
							<div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Os cadastros apenas são válidos após autorização dos administradores. Forneça um endereço de Email válido para que eles possam entrar em contato para validar o cadastro da ONG.
                        
						</div>
						<?php }
						}?>						
                        <form role="form" method="POST" action="">
                            <fieldset>
								<div class="form-group">
                                    <input class="form-control" placeholder="Nome da ONG" name="name" maxlength="100" type="cnpj" autofocus required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="CNPJ (números somente)" name="cnpj" type="cnpj" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Senha de acesso (6 a 32 caracteres)" maxlength="32" name="password" type="password" value="" required>
                                </div>
								<div class="form-group">
                                    <input class="form-control" placeholder="Repita a senha de acesso (6 a 32 caracteres)" maxlength="32" name="confPassword" type="password" value="" required>
                                </div>
								<div class="form-group">
                                    <input class="form-control" placeholder="Endereço de Email para contato" name="email" type="cnpj" required>
                                </div>
								<div class="form-group">
                                    <input class="form-control" placeholder="Endereço" name="address" type="cnpj" required>
                                </div>
								<div class="form-group">
                                    <input class="form-control" placeholder="Website, Página em rede social, entre outros" name="website" type="cnpj" required>
                                </div>
								<div class="form-group">
                                    <input class="form-control" placeholder="CPF autorizado no site da Fazenda (números somente)" name="cpf" type="cnpj" required>
                                </div>
								<div class="form-group">
                                    <input class="form-control" placeholder="Senha do site da Fazenda (6 a 32 caracteres)" maxlength="32" name="remotePassword" type="password" value="" required>
                                </div>  
								<div class="form-group">
                                    <input class="form-control" placeholder="Repita a senha do site da Fazenda (6 a 32 caracteres)" maxlength="32" name="confRemotePassword" type="password" value="" required>
                                </div>  								
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="Cadastrar" name="Register"/>
                            </fieldset>
                        </form>						
                    </div>
                </div>
            </div>
        </div>
    </div>