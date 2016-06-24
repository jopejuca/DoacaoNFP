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

$router->addMatchTypes(array('nCode' => '([0-9][0-9][0-9][0-9]-){10}[0-9][0-9][0-9][0-9]'));

$router->map( 'GET', '/panel/', function() {		
	require __DIR__ . '/pages/dashboard.php';	
});
$router->map( 'GET', '/panel/receiver', function() {		
	require __DIR__ . '/pages/receiver.php';	
}, 'receiver');
$router->map( 'GET|POST', '/panel/login', function() {
    require __DIR__ . '/pages/login.php';
}, 'login');
$router->map( 'GET|POST', '/panel/register', function() {
    require __DIR__ . '/pages/register.php';
}, 'register');
$router->map( 'GET|POST', '/panel/logout', function() {
    require __DIR__ . '/pages/logout.php';
});
$router->map( 'GET|POST', '/panel/profile', function() {
    require __DIR__ . '/pages/profile.php';
});
$router->map( 'GET|POST', '/panel/settings', function() {
    require __DIR__ . '/pages/settings.php';
});
$router->map( 'GET', '/panel/dashboard', function() {			
    require __DIR__ . '/pages/dashboard.php';
});
$router->map( 'GET|POST', '/panel/invoice/[a:actionType]/[nCode:invoiceId]?', function($actionType,$invoiceId = -1) 
{
	require __DIR__ . '/pages/invoice.php';
});
$match = $router->match();
// call closure or throw 404 status
if( $match && is_callable( $match['target'] ) ) 
{
	if($match["name"] == "receiver")
	{
		call_user_func_array( $match['target'], $match['params'] ); 
		return;
	}
	
	if($match["name"] != "login" && $match["name"] != "register" && !loginCheck())
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