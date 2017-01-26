<?
	if($messageType == 100) { $sms_username = "VMC-SERVIS-DYN"; $sms_password = "s3ppm355"; }
	$soap = curl_init("http://etwar.ru/SMSSender/newReport.php");
	curl_setopt($soap, CURLOPT_POST, 1);
	curl_setopt($soap, CURLOPT_RETURNTRANSFER, 1);

	$request = '<?xml version="1.0" encoding="utf-8"?><request><state id_sms="617136283" time="2016-08-23 16:28:35">DELIVRD</state></request>';

	curl_setopt($soap, CURLOPT_HTTPHEADER, 
			array('Content-Type: text/xml; charset=utf-8', 
				  'Content-Length: '.strlen($request)));

	curl_setopt($soap, CURLOPT_POSTFIELDS, $request);
	$serverresponse = curl_exec($soap);
/* 			echo $serverresponse."<br/>";
	echo curl_errno($soap)."<br/>";
	echo curl_error($soap)."<br/>";
	echo curl_getinfo($soap)."<br/>"; */
	curl_close($soap);
	echo $serverresponse;
?>