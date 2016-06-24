<?php
require_once('../core/DonationManager.php');
require_once('../core/Database.php');
require_once('../core/FazendaBot.php');

function statusToString($s)
{
	switch($s)
	{
		case 0:
			return "Aguardando envio";
		case 1:
			return "Enviada";
		default:
			return "Não classificado";
	}	
}
function remoteStatusToString($s)
{
	switch($s)
	{
		case -1:
			return "Não enviada";
		case 0:
			return "Confirmação pendente";
		case 1:
			return "Contabilizada";
		case 2:
			return "Rejeitada";
		default:
			return "Não classificado";
	}	
}
if($actionType != "send")
	$_SESSION['referer'] = '/panel/invoice/'.$actionType;

switch($actionType)
{
	case "new":
		$title = "Notas novas recebidas pelo sistema";
		$query = "SELECT * FROM donations WHERE OngId = ? AND Status=0";
	break;
	case "pending":
		$title = "Notas pendentes no site da Secretaria da Fazenda";
		$query = "SELECT * FROM donations WHERE OngId = ? AND Status=1 AND RemoteStatus=0";
	break;
	case "received":
		$title = "Notas contabilizadas no site da Secretaria da Fazenda";
		$query = "SELECT * FROM donations WHERE OngId = ? AND Status=1 AND RemoteStatus=1";
	break;
	case "rejected":
		$query = "SELECT * FROM donations WHERE OngId = ? AND Status=1 AND RemoteStatus=2";
		$title = "Notas rejeitadas no site da Secretaria da Fazenda";
	break;
	case "messages":
		$query = "SELECT * FROM donations WHERE OngId = ? AND Status=0 AND Message <> ''";
		$title = "Notas novas recebidas pelo sistema com mensagem";
	break;
}
?>        
          <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Notas</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->    			
            <?php if($actionType != "send")
			{?>
			 <div class="row">
                <div class="col-lg">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i><?php echo $title; ?>                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Data de recebimento</th>
                                            <th>Código</th>
                                            <th>Status</th>
                                            <th>Status na Fazenda</th>
                                            <th>Mensagem</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$donations = Database::getInstance()->readRequest($query, array($_SESSION['ongId']));
									foreach($donations as $donation)
									{										
										echo '<tr class="gradeA">';
										echo '<td>'.date("d/m/y H:i:s",$donation["Date"])."</td>";
										echo '<td>'.$donation["Code"].'</span></div></td>';
										echo '<td>'.statusToString($donation["Status"])."</td>";
										echo '<td>'.remoteStatusToString($donation["RemoteStatus"])."</td>";
										echo '<td>'.($donation["Message"] != ""?'<div class="tooltip-demo"><a class="btn btn-outline btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="'.$donation["Message"].'">Ver</a></div>':"-")."</td>";
										echo '<td>'.($donation["Status"] == 0?'<a href="/panel/invoice/send/'.$donation["Code"].'" class="btn btn-outline btn-default">Enviar</a>':"-").'</td>';
										echo '</tr>';
									}
									?>                                                                             
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->					
                        </div>						
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
				</div>
				<!-- /.row -->  
				<?php
				}
				else
				{
					$donation = DonationManager::getInstance()->getDonationFromCode($invoiceId);
					
					if(!$donation || $donation->getOngId() != $_SESSION['ongId'])
					{
						echo '<div class="alert alert-danger"> Código de doação inválido!</div>';
					}
					else
					{
						if(isset($_SESSION['bot']))
						{
							$bot = unserialize($_SESSION['bot']);											
						}
						else
						{
							$bot = new FazendaBot(session_id(),$_SESSION['cpf'],$_SESSION['remotePassword']);
							$bot->checkSession();
							$_SESSION['bot'] = serialize($bot);
						}
						
						if(isset($_POST['captcha']))
						{
							if(!$bot->insertDonation($invoiceId, $_POST['captcha']))
							{
								echo '<div class="alert alert-danger">'.$bot->getlastError().'</div>';
								//Tratamento de alguns erros...
								if(strpos($bot->getlastError(), "Este pedido já existe no sistema") !== FALSE || strpos($bot->getlastError(), "O Código de barras informado contém erros") !== FALSE)
								{
									$donation->setStatus(1);
									$donation->setRemoteStatus(2);
									DonationManager::getInstance()->updateDonation($donation);
								}
							}																
							else
							{
								echo '<div class="alert alert-success">Envio efetuado com sucesso!</div>';
								
								$donation->setStatus(1);
								$donation->setRemoteStatus(0);
								DonationManager::getInstance()->updateDonation($donation);
							}
						}
						else
						{
							if(!$bot->doLogin())
							{
								echo '<div class="alert alert-danger">'.$bot->getlastError().'</div>';
							}
							else
							{
								$captcha = $bot->getCapctha();
								if($captcha == "")
								{
									if(!$bot->insertDonation($invoiceId, null))
									{
										echo '<div class="alert alert-danger">'.$bot->getlastError().'</div>';
										
										//Tratamento de alguns erros...
										if(strpos($bot->getlastError(), "Este pedido já existe no sistema") !== FALSE || strpos($bot->getlastError(), "O Código de barras informado contém erros") !== FALSE)
										{
											$donation->setStatus(1);
											$donation->setRemoteStatus(2);
											DonationManager::getInstance()->updateDonation($donation);
										}
									}										
									else
									{
										echo '<div class="alert alert-success">										
										Envio efetuado com sucesso!.                        
										</div>';
										
										$donation->setStatus(1);
										$donation->setRemoteStatus(0);
										DonationManager::getInstance()->updateDonation($donation);
									}
								}
								else
								{
									$_SESSION['bot'] = serialize($bot);
									
									echo '<div class="alert alert-warning">Capctha requerido</div>';
										
									$image = $bot->downloadCaptcha($captcha);
									$imageData = base64_encode(file_get_contents($image));									
									echo '<img src="' . 'data: '.'image/jpeg'.';base64,'.$imageData . '">';
									
									echo '<br><br><form method="post" action=""><input class="form-control" placeholder="Captcha" name="captcha"/><br><input type="submit" class="btn btn-outline btn-default" value="Confirmar"/></form>';
								}
							}
						}
					}
					if(isset($_SESSION['referer']))
						echo '<br><center><a href="'.$_SESSION['referer'].'" type="button" class="btn btn-outline btn-default">Voltar</a></center>';
				}
				?>
			</div>               		
        <!-- /#page-wrapper -->
