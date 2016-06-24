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

$loginError = "";

if(isset($_POST['Login']))
{
	$ong = OngManager::getInstance()->getOngFromCnpj($_POST['cnpj']);
	
	if($ong)
	{
		if($ong->getPassword() == md5($_POST['password']))
		{
			if($ong->getValid())
			{
				$_SESSION['ongId'] = $ong->getId();
				$_SESSION['cpf'] = $ong->getCpf();
				$_SESSION['remotePassword'] = $ong->getRemotePassword();
				?>
				<meta http-equiv="refresh" content="1;url=/panel/dashboard">
				<script type="text/javascript">
					window.location.href = "/panel/dashboard"
				</script>
				<?php
			}
			else
			{
				$loginError = "O cadastro da ONG ainda não foi validado pelos administradores!";
			}
		}
		else
		{
			$loginError = "Usuário ou senha incorretos!";
		}
	}
	else
	{
		$loginError = "Usuário ou senha incorretos!";
	}
}
?>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Por Favor, faça o login</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST" action="">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="CNPJ" name="cnpj" type="cnpj" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Senha" name="password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Permanecer logado">Permanecer logado
                                    </label>
                                </div>                                
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="Login" name="Login"/>
                            </fieldset>
                        </form>
						<?php if($loginError != "")
							{?>
							</br>
						<div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <?php echo $loginError;?>
                        
						</div>
						<?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>