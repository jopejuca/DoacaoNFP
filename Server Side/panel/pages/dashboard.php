<?php
require_once('../core/DonationManager.php');
require_once('../core/Database.php');
function getDashboardStats($ongId)
{
	$newInvoices = 0;
	$newMessages = 0;
	$pendentInvoices = 0;
	$rejectedInvoices = 0;	
	
	$newInvoices = Database::getInstance()->readRequest("SELECT COUNT(*) FROM donations WHERE OngId = ? AND Status=0",array($ongId))[0][0];
	$newMessages = Database::getInstance()->readRequest("SELECT COUNT(*) FROM donations WHERE OngId = ? AND Status=0 and Message <> ''",array($ongId))[0][0];
	$pendentInvoices = Database::getInstance()->readRequest("SELECT COUNT(*) FROM donations WHERE OngId = ? AND Status=1 and RemoteStatus=0",array($ongId))[0][0];
	$rejectedInvoices = Database::getInstance()->readRequest("SELECT COUNT(*) FROM donations WHERE OngId = ? AND Status=1 and RemoteStatus=2",array($ongId))[0][0];

	return array($newInvoices, $newMessages, $pendentInvoices, $rejectedInvoices);
}
function generateStats($ongId, $interval, $parts)
{
	$time = time();
	$donations = DonationManager::getInstance()->getDonationsToOng($ongId, $time - $interval);
	
	$partInterval = (int)$interval/$parts;
	
	$data = array();
	
	$result = "$(function() {
    Morris.Area({
        element: 'morris-area-chart',
        data: [";
		
		for($i = 0;$i < $parts;$i++)
		{
			$data[$i] = 0;
		}
		foreach($donations as $donate)
		{
			$r =(int)(($time - $donate->getDate())/$partInterval);		
			
			$data[$r] = $data[$r] + 1;			
		}
		
		$first = true;
		
		krsort($data);

		foreach($data as $key => $value)
		{
			if(!$first)
				$result .= ",";
				
			$first = false;
			
			$result .= "{period: '".date("Y-m-d", ($time - $interval)+(((int)($parts - $key))*$partInterval))."',notas: ".$value."}";
		}
		
		$result .= "],
        xkey: 'period',
        ykeys: ['notas'],
        labels: ['Notas'],
        pointSize: 2,
        hideHover: 'auto',
        resize: true
    });});";
	return $result;
}
?>
          <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
				<?php $stats = getDashboardStats($_SESSION['ongId'])?>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-comments fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $stats[1];?> </div>
                                    <div>Novas mensagens</div>
                                </div>
                            </div>
                        </div>
                        <a href="/panel/invoice/messages">
                            <div class="panel-footer">
                                <span class="pull-left">Ver detalhes</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tasks fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $stats[0];?></div>
                                    <div>Novas notas</div>
                                </div>
                            </div>
                        </div>
                        <a href="/panel/invoice/new">
                            <div class="panel-footer">
                                <span class="pull-left">Ver detalhes</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
				<div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-refresh fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $stats[2];?></div>
                                    <div>Notas pendentes</div>
                                </div>
                            </div>
                        </div>
                        <a href="/panel/invoice/pending">
                            <div class="panel-footer">
                                <span class="pull-left">Ver detalhes</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-exclamation fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $stats[3];?></div>
                                    <div>Notas rejeitadas</div>
                                </div>
                            </div>
                        </div>
                        <a href="/panel/invoice/rejected">
                            <div class="panel-footer">
                                <span class="pull-left">Ver detalhes</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
			 <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Notas recebidas nos últimos 30 dias                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="morris-area-chart"></div>							
                        </div>						
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
				</div>
				<!-- /.col-lg-8 -->
				<div class="col-lg-4">     
					<div class="chat-panel panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-comments fa-fw"></i>
                            Mensagens dos Administradores                            
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <ul class="chat">
							<?php
								$messages = Database::getInstance()->readRequest("SELECT * FROM messages WHERE Destination=-1 OR Destination=? ORDER BY Date DESC", array($_SESSION['ongId']));								
								foreach($messages as $message)
								{
									echo '<li class="left clearfix"><span class="chat-img pull-left"><img src="http://placehold.it/50/55C1E7/fff" alt="Avatar" class="img-circle" /></span>';
									echo '<div class="chat-body clearfix"><div class="header"> <strong class="primary-font">'.$message["Author"].'</strong><small class="pull-right text-muted"><i class="fa fa-clock-o fa-fw"></i><time class="timeago" datetime="'.date("c",$message["Date"]).'">'.date("d/m/y g:i:s a",$message["Date"]).'</time></small>';
									echo '</div><p>'.$message["Message"]."</p></div></li>";									
								}
							?>                                
                            </ul>
                        </div>
                        <!-- /.panel-body -->
                        <div class="panel-footer">                            
                        </div>
                        <!-- /.panel-footer -->
                    </div>
                    <!-- /.panel .chat-panel -->
                </div>
                <!-- /.col-lg-4 -->
			</div>			
            <!-- /.row -->
        </div>		
			<!-- Morris Charts JavaScript -->
			<script src="/panel/bower_components/raphael/raphael-min.js"></script>
			<script src="/panel/bower_components/morrisjs/morris.min.js"></script>	
							<script>
							<?php echo generateStats($_SESSION['ongId'], 2592000, 30);// 90 dias?>
							</script>
        <!-- /#page-wrapper -->  