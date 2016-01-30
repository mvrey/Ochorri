<?php
require_once ("../../lib/inclusion.php");
require_once_model ('Message');

$messageConn = new MessageDAO();

$msgId = $_POST["msgId"];
$mode = $_POST["mode"];

if ($mode=="setRead")
    $messageConn->setMessageRead($msgId);
elseif ($mode=="delete")
    $messageConn->setMessageDeleted($msgId);
?>
