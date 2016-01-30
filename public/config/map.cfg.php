<?php
if (defined("MODEL_ROUTE"))
    require_once_model('Sector', MODEL_ROUTE);
else
    require_once_model('Sector');

$sectorConn = new SectorDAO();
$dimensions = $sectorConn->getMapDimensions();

//js limits on zoom. width=2*height
$MIN_WIDTH = 4;
$MAX_WIDTH = $dimensions[0]+1;
$MIN_HEIGHT = 2;
$MAX_HEIGHT = $dimensions[1]+1;

// --- Define some constants (initial zoom level)
$MAP_WIDTH = 10;
$MAP_HEIGHT = 5;
$HEX_HEIGHT = 80;

// --- Use this to scale the hexes smaller or larger than the actual graphics
$HEX_SCALED_HEIGHT = $HEX_HEIGHT * 1.0;
$HEX_SIDE = $HEX_SCALED_HEIGHT / 2;
?>