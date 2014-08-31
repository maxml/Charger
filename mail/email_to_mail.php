<?php

$email = htmlspecialchars($_POST['email']);

$json = array();
//$json['error'] = 0;

if (!$email) { 
	$json['error'] = 'Вы заполнили не все поля!';
	$json['email'] = $email;
	echo json_encode($json);  
	die();
}

if(!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email)) { 
	$json['error'] = 'Не верный формат email!'; 
	$json["email"] = $email;
	echo json_encode($json); 
	die();
}

function mime_header_encode($str, $data_charset, $send_charset) { 
	if($data_charset != $send_charset)
		$str=iconv($data_charset,$send_charset.'//IGNORE',$str);
	return ('=?'.$send_charset.'?B?'.base64_encode($str).'?=');
}

class TEmail {
	public $from_email;
	public $from_name;
	public $to_email;
	public $to_name;
	public $subject;
	public $data_charset='UTF-8';
	public $send_charset='windows-1251';
	public $body='';
	public $type='text/plain';

	function send(){
		$dc=$this->data_charset;
		$sc=$this->send_charset;

		$enc_to=mime_header_encode($this->to_name,$dc,$sc).' <'.$this->to_email.'>';
		$enc_subject=mime_header_encode($this->subject,$dc,$sc);
		$enc_from=mime_header_encode($this->from_name,$dc,$sc).' <'.$this->from_email.'>';
		$enc_body=$dc==$sc?$this->body:iconv($dc,$sc.'//IGNORE',$this->body);

		$headers='';
		$headers.="Mime-Version: 1.0\r\n";
		$headers.="Content-type: ".$this->type."; charset=".$sc."\r\n";
		$headers.="From: ".$enc_from."\r\n";

		return mail($enc_to, $enc_subject, $enc_body, $headers);
	}

}

$emailgo= new TEmail(); 
$emailgo->from_email= 'office@z4charge.ru';
$emailgo->from_name= 'Tech Support';
$emailgo->to_email= 'office@z4charge.ru';
$emailgo->to_name= 'Tech Support';
$emailgo->subject= 'New user add';
$emailgo->body= "Новый пользователь хочет добавиться к Вам в друзья - ".$email;

$emailgo->send(); 

$json['error'] = 0; 
echo json_encode($json);
