<?php
$cabeceras = 'From: webmaster@example.com' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/';
$myvar = mail("mrkvr84@gmail.com", "Tus datos del O'chorri", "hola blablabkabla", $cabeceras);
var_dump($myvar);
?>