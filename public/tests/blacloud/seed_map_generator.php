<?
require_once ("../test/register_functions.php");
require_once ('../DAO/DAO.class.php');
require_once ('../config/sector.cfg.php');
session_start();

$connection = new DAO();
$connection->connect();

$rs = $connection->getMapDimensions();

if ($rs->fields[0] && $rs->fields[1])
    {
    $startX = ($rs->fields[0])+1;
    $startY = ($rs->fields[1])+1;
    }
else
    {
    $startX = 0;
    $startY = 0;
    }

$inc_x = $MAP_INCREMENTX;
$inc_y = $MAP_INCREMENTY;

for ($i=0;$i<$startY+$inc_y;$i++)
    {
    for ($j=$startX;$j<$startX+$inc_x;$j++)
        {
        insertRandomSector ($j, $i);
        }
    }

for ($i=$startY;$i<$startY+$inc_y;$i++)
    {
    for ($j=0;$j<$startX;$j++)
        {
        insertRandomSector ($j, $i);
        }
    }



?>