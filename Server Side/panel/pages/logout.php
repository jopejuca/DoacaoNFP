<?php 
require_once ('../core/OngManager.php');

if(!isset($_SESSION['ongId']))
{
	?>
	<meta http-equiv="refresh" content="1;url=/panel/login">
	<script type="text/javascript">
		window.location.href = "/panel/login"
	</script>
	<?php
	return;
}

session_unset();
?>
	<meta http-equiv="refresh" content="1;url=/panel/">
	<script type="text/javascript">
		window.location.href = "/panel/"
	</script>
	<?php
?>