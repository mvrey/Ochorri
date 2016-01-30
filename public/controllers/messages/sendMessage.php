<?php
require_once ("../../lib/inclusion.php");
require_once_model ('Message');
require_once_model ('Player');

$messageConn = new MessageDAO();
session_start();

$player = $_SESSION["player"];
$from = $player->getId();
$to = $_POST["recipient"];
$subject = addslashes($_POST["subject"]);
$content = addslashes($_POST["content"]);

$result = $messageConn->insertMessage($from, $to, $subject, $content);

if ($result)
    echo "Mensaje enviado.";
else
    echo "Error al enviar el mensaje";
?>