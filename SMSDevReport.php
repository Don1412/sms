<?
$productID = '510013';
$secretKey = '2eepr0';
$price = $_POST['pd_price'];
$auth = md5($price.$secretKey);
if($_POST['pd_price']) { header('Location: https://checkout.paddle.com/checkout/product/'.$productID.'?price='.$price.'&auth='.$auth); exit; }
?>
<html>
<head>
<meta charset="utf-8">
<title>SMS Report</title>
</head>
<body>
<!--<iframe src="https://api.paymentwall.com/api/subscription/?key=6cc9de2700b26af7b6d204f50ed062b0&uid=1&widget=p4_1" width="371" height="450" frameborder="0"></iframe>-->
<form name="pg_frm" method="post" action="https://www.paygol.com/pay" >
   <input type="hidden" name="pg_serviceid" value="363075">
   <input type="hidden" name="pg_currency" value="RUB">
   <input type="hidden" name="pg_name" value="Casino">
   <input type="hidden" name="pg_custom" value="">
   <input type="text" name="pg_price" value="50">
   <input type="hidden" name="pg_return_url" value="http://your-express.ru/SMS/pg_success.php">
   <input type="hidden" name="pg_cancel_url" value="http://your-express.ru/SMS/pg_cancel.php">
   <br>
   <input type="image" name="pg_button" src="https://www.paygol.com/webapps/buttons/en/black.png" border="0" alt="Make payments with PayGol: the easiest way!" title="Make payments with PayGol: the easiest way!" > 
</form>
<button onclick = "location.href='https://pay.paddle.com/checkout/510013'">Pay 2</button>
<br><br>
<form name="pd_frm" method="post" action="http://your-express.ru/SMS/SMSDevReport.php" >
   <input type="text" name="pd_price" value="50">
   <br>
   <input type="submit" name="pd_button" title="Pay 3" > 
</form>
</body>
</html>