<?php

//$file = 'log.txt';
//$current = file_get_contents($file);
//$current .= "Start\n";
//file_put_contents($file, $current);

	$buff = file_get_contents("php://input");
	
	//$buff2 = $_POST['email']; ///doesn't work

	$form = json_decode($buff, true);
	
	//$buff = var_dump($form);
	//$current .= print_r($form,true);
	//file_put_contents($file, $current);
	
	$name = htmlspecialchars($form['name']); // пишем данные в переменные и экранируем спецсимволы
    $email = htmlspecialchars($form['email']);
    $subject = htmlspecialchars($form['subject']);
    $message = htmlspecialchars($form['message']);
	
	//$current .= $name.$email.$subject.$message."\n";
	//file_put_contents($file, $current);
	
    $json = array(); // подготовим массив ответа
    
	if (!$name or !$email or !$subject or !$message) { // если хоть одно поле оказалось пустым
        $json['error'] = 'Вы заполнили не все поля!'; // пишем ошибку в массив
        echo json_encode($json); // выводим массив ответа 
        die(); // умираем
    }
    if(!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email)) { // проверим email на валидность
        $json['error'] = 'Не верный формат email!'; // пишем ошибку в массив
        echo json_encode($json); // выводим массив ответа
        die(); // умираем
    }

    function mime_header_encode($str, $data_charset, $send_charset) { // функция преобразования заголовков в верную кодировку 
        if($data_charset != $send_charset)
        $str=iconv($data_charset,$send_charset.'//IGNORE',$str);
        return ('=?'.$send_charset.'?B?'.base64_encode($str).'?=');
    }
	
    /* супер класс для отправки письма в нужной кодировке */
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

    $emailgo= new TEmail(); // инициализируем супер класс отправки
    $emailgo->from_email= 'office@z4charge.ru'; // от кого
    $emailgo->from_name= 'Tech Support';
    $emailgo->to_email= $email; // кому
    $emailgo->to_name= $name;
    $emailgo->subject= 'User action: '.$subject; // тема
    $emailgo->body= $message; // сообщение
	
    $emailgo->send(); // отправляем

    $json['error'] = 0; // ошибок не было

	echo json_encode($json);

?>