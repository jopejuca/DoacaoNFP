<?php 
require 'lib/AltoRouter.php';
require 'Session.php';
require ('../core/Config.php');

function loginCheck()
{
	if(!isset($_SESSION['ongId']))
	{
		header("Location: /panel/login");
		return false;
	}
	return true;
}

$router = new AltoRouter();

$router->map( 'GET', '/panel/', function() {		
	require __DIR__ . '/pages/dashboard.php';	
});
$router->map( 'GET|POST', '/panel/login', function() {
    require __DIR__ . '/pages/login.php';
}, 'login');
$router->map( 'GET|POST', '/panel/logout', function() {
    require __DIR__ . '/pages/logout.php';
});
$router->map( 'GET', '/panel/dashboard', function() {			
    require __DIR__ . '/pages/dashboard.php';
});
$router->map( 'GET', '/panel/invoice/[a:t]/[i:id]?', function($t,$id) 
{
	$actionType = $t;
	$invoiceId = $id;
    require __DIR__ . '/pages/invoice.php';
});
$match = $router->match();
// call closure or throw 404 status
if( $match && is_callable( $match['target'] ) ) 
{
	if($match["name"] != "login" && !loginCheck())
		return;
		
	require __DIR__ . '/main/header.php';
	call_user_func_array( $match['target'], $match['params'] ); 
	require __DIR__ . '/main/bottom.php';
} 
else 
{
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');	
	require __DIR__ . '/pages/404.html';	
}
?>