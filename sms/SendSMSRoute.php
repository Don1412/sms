<?php
$errstr = "";
$errcode = 0;
class Sender
{
	var $host;
	var $port;
	/*
	* Username that is to be used for submission
	*/
	var $strUserName;
	/*
	* password that is to be used along with username
	*/
	var $strPassword;
	/*
	* Sender Id to be used for submitting the message
	*/
	var $strSender;
	/*
	* Message content that is to be transmitted
	*/
	var $strMessage;
	/*
	* Mobile No is to be transmitted.
	*/
	var $strMobile;
	/*
	* What type of the message that is to be sent
	* <ul>
	* <li>0:means plain text</li>
	* <li>1:means flash</li>
	* <li>2:means Unicode (Message content should be in Hex)</li>
	* <li>6:means Unicode Flash (Message content should be in Hex)</li>
	* </ul>
	*/
	var $strMessageType;
	/*
	* Require DLR ornot
	* <ul>
	* <li>0:means DLR is not Required</li>
	* <li>1:means DLR isRequired</li>
	* </ul>
	*/
	var $strDlr;
	private function sms_unicode($message)
	{
		$hex1='';
		if (function_exists('iconv')) 
		{
			$latin=@iconv('UTF−8','ISO−8859−1', $message);
			if(strcmp($latin,$message))
			{
				$arr=unpack('H*hex',@iconv('UTF−8','UCS−2BE', $message));
				$hex1 = strtoupper($arr['hex']);
			}
			if($hex1 =='')
			{
				$hex2='';
				$hex='';
				for($i=0;$i<strlen($message);$i++)
				{
					$hex = dechex(ord($message[$i]));
					$len = strlen($hex);
					$add = (4-$len);
					if($len < 4)
					{
						for($j=0;$j<$add;$j++)
						{
							$hex="0".$hex;
						}
					}
					$hex2.=$hex;
				}
				return $hex2;
			}
			else
			{
				return $hex1;
			}
		}
		else
		{
			print 'iconv Function Not Exists !';
		}
	}
	//Constructor..
	public function Sender($host,$port,$username,$password,$sender, $message,$mobile,$msgtype,$dlr)
	{
		$this->setHost($host);
		$this−>setPort($port);
		$this−>setUser($username);
		$this−>setPassword($password);
		$this−>setSender($sender);
		$this−>setMessage($message); //URL Encode The Message..
		$this−>setDestination($mobile);
		$this−>setMessageType($msgtype);
		$this−>setDlr($dlr);
	}
	private function setDlr ($dl) 
	{
        global $errstr;
        if ($dl == "") 
		{
            $this->strDlr = "";
            return true;
        } 
		else 
		{
            preg_match("/^[01]$/", $dl, $matches);
            if ($matches[0] != "") 
			{
                $this->strDlr = $dl;
                return true;
            } 
			else 
			{
                $errstr = "Dlr of number must be 0 or 1.";
                return false;
            }
        }
    }
	private function setMessageType ($mt) 
	{
        global $errstr;
        if ($mt == "") 
		{
            $this->strMessageType = "";
            return true;
        } 
		else 
		{
            preg_match("/^[01234567]$/", $mt, $matches);
            if ($matches[0] != "") 
			{
                $this->strMessageType = $mt;
                return true;
            } 
			else 
			{
                $errstr = "Message type of number must be 0 - 7.";
                return false;
            }
        }
    }
	private function setMessage ($msg) 
	{
        $this->strMessage = $msg;
        return true;
    }
	private function setSender ($sa) 
	{
        global $errstr;
        if ($sa == "") 
		{
            $this->strSender = "";
            return true;
        }
        preg_match("/^(\d{1,16}|.{1,11})$/", $sa, $matches);
        if ($matches[1] != "") {
            $this->strSender = urlencode($sa);
            return true;
        } 
		else 
		{
            $errstr = "Source address not recognised.";
            return false;
        }
    }
	private function setPassword ($pw) 
	{
        $this->strPassword = $pw;
        return true;
    }
	private function setUser ($ur) 
	{
        global $errstr;
        if ($ur == "") 
		{
            $this->strUserName = "";
            return true;
        } 
		else 
		{
            preg_match("/^\w{1,16}$/", $ur, $matches);
            if ($matches[0] != "") 
			{
                $this->strUserName = $ur;
                return true;
            }
			else 
			{
                $errstr = "User name invalid. Must be 1-16 chars: " . $ur;
                return false;
            }
        }
    }
	private function setPort ($pr) 
	{
        global $errstr;
        if ($pr == "") 
		{
            $this->port = "";
            return true;
        }
        preg_match("/^(\d{1,16}|.{1,11})$/", $pr, $matches);
        if ($matches[1] != "") 
		{
            $this->port = urlencode($pr);
            return true;
        } 
		else 
		{
            $errstr = "Port not recognised.";
            return false;
        }
    }
	private function setHost ($hs) 
	{
        global $errstr;
        if ($hs == "") 
		{
            $this->host = "";
            return true;
        }
        preg_match("/^(\d{1,16}|.{1,11})$/", $hs, $matches);
        if ($matches[1] != "") 
		{
            $this->host = urlencode($hs);
            return true;
        } 
		else 
		{
            $errstr = "Hosting not recognised.";
            return false;
        }
    }
	private function setDestination($da) 
	{
		global $errstr;
        if ($da == "") 
		{
            $this->strMobile = "";
            return true;
        }
        $das = explode(",", $da);
        $dests = array();
        foreach ($das as $dest) 
		{
            preg_match("/(\+|00)?([1-9]\d{7,15})/", $dest, $matches);
            if ($matches[2] != "") 
			{
                array_push ($dests, $matches[2]);
            } 
			else 
			{
                $this->dest_addr = "";
				$errstr = "Destination not recognised.";
                return false;
            }
        }
        $this->dest_addr = implode(",",$dests);
        return true;
    }
	public function Submit()
	{
		$this−>setMessage($this−>sms_unicode($this−>strMessage));
		//SmpphttpUrltosendsms.
		//$request = "http://sms1.cardboardfish.com:9001/HTTPSMS?S={$systemtype}&UN=${username}&P=${password}&DA={$sms->dest_addr}&SA={$sms->source_addr}&M=${msg}";
		$live_url = "http://smsplus.routesms.com:8080//bulksms/bulksms?username=${this−
		>strUserName}&password=${this−>strPassword}&type=${this−>strMessageType}&dlr=${this−
		>strDlr}&destination=${this−>strMobile}&source=${this−>strSender}&message=${this−>strMessage}";
		$parse_url=file($live_url);
		echo $parse_url[0];
	}
}
// $sms = new Sender("121.241.242.111", "8080", "vmcservis", "vmc321", $source, $message, $to, "6", "1");
	// $sms->Submit();
//$sms = new Sender("121.241.242.111", "8080", "vmcservis", "vmc321", "375298169558", "375298169558", "375298169558", "6", "1");
//$sms->Submit();
//Call TheConstructor.
/*$obj = new Sender("IP","Port","","","Tester"," "'Sç9çyö'"," 919990001245,"2","1");
$obj−>Submit();*/
?>