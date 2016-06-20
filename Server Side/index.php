<?php
require_once ('Core/DonationManager.php');

$users = array("admin" => array("pass" => "db94ebdcc1327f904db565e33f9cd5fb", "id" => 1));
session_start();

if(isset($_SESSION['ong']))
{
	?>
	<center>
	Doaçoes recebidas
	<br></br>
	<table border=1>
	<tr>
		<th>Data</th>
		<th>Código</th>
		<th>Status</th>
	</tr>	
	<?php
	$donations = DonationManager::getInstance()->getDonationsToOng($_SESSION['ong']);
	
	foreach($donations as $donation)
	{
		echo "<tr><td>".date("d-m-Y G:i:s", $donation->getDate())."</td><td>".$donation->getCode()."</td><td>".$donation->getStatus()."</td></tr>";
	}
	
	echo "</table></center>";	
}
else
{
	if(!isset($_POST['username']))
	{
	?>
		<center>
		<form method="post" action="">
		Login:
		</br>
		<input type='textfield' name='username'/>
		</br>		
		Senha:
		</br>
		<input type='password' name='password'/>
		</br></br>
		<input type='submit' name='loginBtn' value='Logar'/>
		</form>
		</center>
	<?php
	}
	else
	{
		if($_POST['username'] == "" || $_POST['password'] == "" || !array_key_exists($_POST['username'], $users) || $users[$_POST['username']]["pass"] != md5($_POST['password']))
		{
			echo "<center>Login/senha incorretos!<br></br><a href='index.php'>Voltar</a></center>";
		}
		else
		{	
			$_SESSION['ong'] = $users[$_POST['username']]["id"];
			$_SESSION['username'] = $_POST['username'];
			header('Location: index.php');			
		}
		
	}
}
?>