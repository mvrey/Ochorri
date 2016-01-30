<?php
if (isset($_POST["continuar"]))
    {
    require_once("../../../public/lib/inclusion.php");
    require_once_model('Global');

    $nick=$_POST['nick'];
    $pass=$_POST['pass'];

    $connection = new GlobalDAO();

    if ($connection->getAdminExists($nick,md5($pass)))
        {
        session_start();
        $_SESSION['nick'] = $nick;
        $_SESSION['language'] = 'spanish';
        $_SESSION['admin'] = true;
        header ("Location: ../../controllers/global/globalController.php");
        }
    }

require("../../views/index/indexView.php");
?>