<?php
require_once ('../../config/paths.php');

if (isset($_POST['continuar']))
   {
   require_once('../../models/index/indexDAO.class.php');
   $nick=$_POST['nick'];
   $pass=$_POST['pass'];

   $connection = new indexDAO();

   if ($connection->getPlayerExists($nick,md5($pass)))
        {
        session_start();
        $_SESSION['nick'] = $nick;
        $_SESSION['language'] = 'spanish';
        require_once('../../controllers/StaticData/initStaticData.php');
        require_once ("../../controllers/StaticData/getSessionPlayer.php");
        header ("Location: ../../controllers/main/mainController.php");
        }
    }

require ("../../views/index/indexView.php");
?>