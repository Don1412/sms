<?
require_once("sms/Logger.php");

$logger = new Logger("routeKey.txt");

$str = 'Country = '.$_GET['country'].' | Price = '.$_GET['price'].' | Currency = '.$_GET['currency'].' | frmprice = '.$_GET['frmprice'].' | frmcurrency = '.$_GET['frmcurrency'].' | method = '.$_GET['method'].' | transaction_id = '.$_GET['transaction_id'];

$logger->log($str);

$logger->dispose();
?>