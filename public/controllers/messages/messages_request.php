<?php
require_once("../../lib/inclusion.php");
require_once ("../../config/paths.php");
require_once_model("StaticData");
require_once_model("Player");
require_once_model("Message");
session_start();

$messageConn = new MessageDAO();

$player = $_SESSION["player"];
$staticData = $_SESSION['staticData'];
$allPlayers = $staticData->getPlayers();

$messagesArr = $messageConn->getMessages($player->getId());
$messages = array();
foreach ($messagesArr as $messageArr)
    {
    $msg = new Message ($messageArr[0], $messageArr[1], $messageArr[2], $messageArr[3], $messageArr[4], $messageArr[5], $messageArr[6]);
    $messages[] = $msg;
    }

require ("../../views/message/messageView.php")
?>