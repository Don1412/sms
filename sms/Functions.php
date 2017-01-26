<?
function getUrl() {
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
  $url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
  return $url;
}  
function mydie($errstr) {
    die ("Error: " . $errstr . "\n");
}
  function removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
       foreach($objs as $obj) {
         is_dir($obj) ? removeDirectory($obj) : unlink($obj);
       }
    }
    rmdir($dir);
  }
function cp1251_2ucs2($str){
    for ($i=0;$i<strlen($str);$i++){
        if (ord($str[$i]) < 127){
            $results = sprintf("%04X",ord($str[$i]));
        }elseif (ord($str[$i])==184){ //С‘
            $results="0451";
        }elseif (ord($str[$i])==168){ //РЃ
            $results="0401";
        }else{
            $results = sprintf("%04X",(ord($str[$i])-192+1040));
        }
        $ucs2 .= $results;
    }
    return $ucs2;
}

function utfToUCS($text){
  $text=iconv("utf-8", "UCS-2BE", $text);
  $text=bin2hex ($text);
  return $text;
}

function encodeToUnicode($string){
$encodedString = cp1251_2ucs2($string);
return $encodedString;
}

function deleteSpecialChars($string){
  $result = str_replace(array("\r\n", "\r", "\n"), '',  strip_tags($string));
  return $result;
}

?>
