<?php
	session_start();
	include("../settings/connect_datebase.php");
	
	
	function decryptAES($endryptedData, $key) {
		$data = base64_decode($endryptedData);
		if($data === false || strlen($data) < 17) {
			error_log("Invalid data or too short");
			return false;
		}
		$iv = substr($data, 0, 16);
		$endrypted = substr($data, 16);
		$keyHash = md5($key);
		$keyBytes = hex2bin($keyHash);

		$decrypted = openssl_decrypt(
			$endrypted,
			'aes-128-cbc',
			$keyBytes,
			OPENSSL_RAW_DATA,
			$iv
		);
		return $decrypted;
	}

	$secretKey = "qazxswedcvfrtgbn";
	$login_encrypted = $_POST['login'] ?? '';
	
	
	$login = decryptAES($login_encrypted, $secretKey);
	
	
	

	
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$mysqli->real_escape_string($login)."';");
	
	$id = -1;
	if($user_read = $query_user->fetch_row()) {
		$id = $user_read[0];
	}
	
	function PasswordGeneration() {
		$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
		$max=10;
		$size=strlen($chars)-1;
		$password="";
		while($max--) {
			$password.=$chars[rand(0,$size)];
		}
		return $password;
	}
	
	// Если пользователь найден ($id != -1)
	if($id != -1) {
		$password = PasswordGeneration();
		
		// Проверяем уникальность хэша нового пароля
		$query_password = $mysqli->query("SELECT * FROM `users` WHERE `password`= '".md5($password)."';");
		while($password_read = $query_password->fetch_row()) {
			$password = PasswordGeneration();
		}
		
		// Обновляем пароль в базе данных
		$mysqli->query("UPDATE `users` SET `password`='".md5($password)."' WHERE `id` = '".$id."'");
		
		// Здесь может быть отправка письма: mail($login, ...);
	}
	
	echo $id;
?>