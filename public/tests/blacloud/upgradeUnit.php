<?php

require_once ('../DAO/DAO.class.php');
require_once ('../class/Technology.class.php');
require_once ('../class/Building.class.php');
require_once ('../class/Unit.class.php');
require_once ('../class/Resource.class.php');
require_once ('../config/paths.php');
require_once ('../class/Player.class.php');
require_once ('../class/StaticData.class.php');
session_start();

$player = $_SESSION["player"];
$resources = $_SESSION["resources"];

$connection = new DAO();
$connection->connect();

$connection->upgradeUnit (2, 3);

?>
