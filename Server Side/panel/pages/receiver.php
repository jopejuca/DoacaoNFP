<?php
if(!isset($_GET['qr']) || !isset($_GET['id']) || !isset($_GET['msg']) || $_GET['qr'] == "" || $_GET['id'] == "" || !is_numeric($_GET['id']) || !is_numeric($_GET['qr']) || strlen($_GET['qr']) != 44)
	return;

require_once('../core/Config.php');
require_once('../core/Donation.php');
require_once('../core/Ong.php');
require_once('../core/OngManager.php');
require_once('../core/DonationManager.php');

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function formatQr($data)
{
	$qr = "";
	for($i = 0;$i < 44;$i+=4)
	{
		$qr .= substr($data, $i, 4)."-";		
	}
	return rtrim($qr, "-");
}
$ong = OngManager::getInstance()->getOngFromId($_GET['id']);

if(!$ong || !$ong->getValid())
	return;

$donation = new Donation(htmlentities($_GET['id']), formatQr(htmlentities($_GET['qr'])), time(), 0, -1, getRealIpAddr(), htmlentities($_GET['msg']));

DonationManager::getInstance()->insertDonationToOng($donation);
?>