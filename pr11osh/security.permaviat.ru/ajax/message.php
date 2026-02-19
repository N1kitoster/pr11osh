<?php
    session_start();
    include("../settings/connect_datebase.php");

    function decryptAES($endryptedData, $key) {
        $data = base64_decode($endryptedData);
        if($data === false || strlen($data) < 17) return false;
        
        $iv = substr($data, 0, 16);
        $endrypted = substr($data, 16);
        $keyHash = md5($key);
        $keyBytes = hex2bin($keyHash);

        return openssl_decrypt(
            $endrypted,
            'aes-128-cbc',
            $keyBytes,
            OPENSSL_RAW_DATA,
            $iv
        );
    }

    $secretKey = "qazxswedcvfrtgbn";

    $IdUser = $_SESSION['user'];
  
    
    
    $Message = decryptAES($_POST["Message"], $secretKey);
    $IdPost = decryptAES($_POST["IdPost"], $secretKey);

    if ($Message) {
        $mysqli->query("INSERT INTO `comments`(`IdUser`, `IdPost`, `Messages`) VALUES ({$IdUser}, {$IdPost}, '{$Message}');");
    }
?>