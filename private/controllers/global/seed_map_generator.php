<?
require_once ('../../../public/lib/inclusion.php');
require_once ('../../../public/config/paths.php');
require_once ('../../../public/config/sector.cfg.php');
require_once_model ('Sector', MODEL_ROUTE);
//session_start();

if (!isset($sectorConn))
    $sectorConn = new SectorDAO();

$mapDimensionsArr = $sectorConn->getMapDimensions();

if ($mapDimensionsArr[0] && $mapDimensionsArr[1])
    {
    $startX = ($mapDimensionsArr[0])+1;
    $startY = ($mapDimensionsArr[1])+1;
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
        Sector::insertRandomSector ($j, $i);
        }
    }

for ($i=$startY;$i<$startY+$inc_y;$i++)
    {
    for ($j=0;$j<$startX;$j++)
        {
        Sector::insertRandomSector ($j, $i);
        }
    }



?>