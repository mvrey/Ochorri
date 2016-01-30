 <?php
require_once ('../../config/paths.php');
require_once ('../../lib/inclusion.php');
require_once_model ('Resource');
require_once_model ('Sector');
require_once_model ('Player');
require_once_model ('StaticData');
session_start();

$coordinateX = $_POST['coordinateX'];
$coordinateY = $_POST['coordinateY'];

$player = $_SESSION["player"];
$staticData = $_SESSION["staticData"];
$resources = $staticData->getResources();
$allSectors = $staticData->getSectors();
$availableResources = $player->getAvailableResources();

$sector = $allSectors[$coordinateX.",".$coordinateY];
$productions = $sector->getProductions();
$spends = $sector->getSpends();
$productionBases = $sector->getProductionBases();

$detailType = 'production';
require ("../../views/detailBox/detailBoxView.php");
require ('../../views/detailBox/productionDetailsView.php');
?>