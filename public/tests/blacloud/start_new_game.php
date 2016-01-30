<?php
require_once ('../../models/DAO/DAO.class.php');
require ('../../config/sector.cfg.php');

//error_reporting (E_STRICT);

$connection = new DAO();
$connection->connect();
$connection = $connection->getConnection();

echo "Vaciando tablas";

$query = "  TRUNCATE table Sector";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table Building";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table Battle";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table BattleCosts";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table BattleRound";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table Division";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table DivisionMovement";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table Message";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table TechnologyLink";
$rs = $connection->Execute($query);
$query = "  TRUNCATE table TrainingQueue";
$rs = $connection->Execute($query);

echo "<img src='../img/buttons/done.gif'><br/>";

echo "Creando mapa";

$MAP_INCREMENTX = 10;
$MAP_INCREMENTY = 5;

require('../test/seed_map_generator.php');

echo "<img src='../img/buttons/done.gif'><br/>";

$MAP_INCREMENTX = 2;
$MAP_INCREMENTY = 1;

echo "Reiniciando datos de Jugadores";

$connection = $connection->getConnection();
$query = "Update Player set player_lastMapHeight=5, player_lastMapOrigin='0,0', player_resources='100,1000,1000,1000,0', player_age=1, player_isLogged=0, player_lastUpdate=".$_SERVER["REQUEST_TIME"];
$rs = $connection->Execute($query);

$query = "SELECT player_id FROM Player";
$playerIdsRS = $connection->Execute($query);

while (!$playerIdsRS->EOF)
    {
    $playerId = $playerIdsRS->fields[0];
    
    if ($playerId)
        {
        require_once ("../test/register_functions.php");
        require ("../test/set_initial_sector.php");
        while (!$inserted)
            {
            require ("../test/seed_map_generator.php");
            require ("../test/set_initial_sector.php");
            }
        $startView = explode (",", $startCoordinates);
        $startX = $startView[0];
        $startY = $startView[1];
        $startX = max($startX-5, 0);
        $startY = max($startY-2, 0);
        $startView = $startX.",".$startY;
        $connection->setLastMapView ($playerId, $startView, 5);
        }
    $playerIdsRS->MoveNext();
    }

echo "<img src='../img/buttons/done.gif'><br/>";

echo "Reinicio Completado";
echo "<img src='../img/buttons/done.gif'><br/>";
?>
