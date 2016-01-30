<?php
require_once('../../lib/inclusion.php');
require_once_model ('Resource');
require_once_model ('Player');
require_once_model ('StaticData');

session_start();

$player = $_SESSION["player"];
$staticData = $_SESSION["staticData"];
$allResources = $staticData->getResources();

require_once ('../../config/paths.php');
require_once ('../../config/map.cfg.php');

$lastMapOrigin = $player->getLastMapOrigin();
$lastMapHeight = $player->getLastMapHeight();

require_once ('../../views/common/head.php');
require_once ('../../views/common/top.php');
require_once ('../../views/common/topMenu.php'); ?>
<div id="main_container">
<?
require_once ('../../controllers/map/mapController.php');
?>
</div>
<? require_once ('../../views/common/foot.php'); ?>