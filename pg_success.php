<?
require_once("sms/Logger.php");

$logger = new Logger("routeKey.txt");

$str = @file_get_contents('php://input');

$logger->log($str);

$logger->dispose();
?>