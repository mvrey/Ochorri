<?php
require_once("../../../public/lib/inclusion.php");
require ('../../../public/config/sector.cfg.php');
require_once_model('Global');

function soMuchWin ($success) {
    if ($success)
        echo "<img src='../../../public/img/buttons/done.gif'><br/>";
    else
        {
        echo "<img src='../../../public/img/buttons/delete.png' style='width:20px; height: 20px; margin-left:10px;'><br/>Abortado.";
        die();
        }
}

//error_reporting (E_STRICT);
define ('MODEL_ROUTE', '../../../public/models/');
require_once_model('Player', MODEL_ROUTE);
$globalConn = new GlobalDAO();
$playerConn = new PlayerDAO();

echo "Vaciando tablas";

soMuchWin($globalConn->truncateNonStaticData());

echo "Creando mapa";

$MAP_INCREMENTX = 10;
$MAP_INCREMENTY = 5;

require('../../controllers/global/seed_map_generator.php');

echo "<img src='../../../public/img/buttons/done.gif'><br/>";

$MAP_INCREMENTX = 2;
$MAP_INCREMENTY = 1;

echo "Reiniciando datos de Jugadores";

soMuchWin($globalConn->restartPlayerData());

echo "Asignando sectores iniciales";
$playerIdsArr = $globalConn->getAllPlayerIds();
$success = true;
foreach ($playerIdsArr as $playerIdArr)
    {
    $playerId = $playerIdArr[0];
    if ($playerId)
        {
        //require_once ("../test/register_functions.php");
        require ("../../controllers/global/set_initial_sector.php");
        while (!$inserted)
            {
            require ("../../controllers/global/seed_map_generator.php");
            require ("../../controllers/global/set_initial_sector.php");
            }
        $startView = explode (",", $startCoordinates);
        $startX = $startView[0];
        $startY = $startView[1];
        $startX = max($startX-5, 0);
        $startY = max($startY-2, 0);
        $startView = $startX.",".$startY;
        if (!$playerConn->setLastMapView ($playerId, $startView, 5))
            $success = false;
        }
    }
soMuchWin($success);

echo "Reinicio Completado";
soMuchWin(true);
?>
