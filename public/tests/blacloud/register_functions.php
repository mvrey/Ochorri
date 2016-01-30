<?php

function insertRandomSector ($x, $y) {

    global $SECTOR_NAMES, $WATER_PROBABILITY, $MIN_PRODUCTIONS, $MAX_PRODUCTIONS;
    global $connection;

    $num = rand(0,count($SECTOR_NAMES)-1);
    $name = $SECTOR_NAMES[$num];
    $num = rand(0,100);
    if ($num>$WATER_PROBABILITY)
        $isLand = 'true';
    else
        $isLand='false';

    $productions = array();
    for ($k=0; $k<count($MIN_PRODUCTIONS); $k++)
        {
        $num = rand ($MIN_PRODUCTIONS[$k], $MAX_PRODUCTIONS[$k]);
        $productions[$k] = $num;
        }
    $productionString = implode(",", $productions);


    $connection->insertSector($x, $y, $name, $isLand, $productionString, '0,0,0,0,0', $productionString);

}

function getForbidden($sectors) {
    //Probemos de nuevo, esta vez con todos los sectores, eliminando los marítimos u ocupados y colindantes
    global $minDistance, $origins;
    $forbidden = array();

    foreach ($sectors as $index=>$sector)
        {
        if (($sector->getOwner()) || ($sector->getOccupant()))
            {
            getAdjacentsByMaxDistance ($sector->getCoordinateX(), $sector->getCoordinateY(), $minDistance);
            $adjacents = $origins;
            
            foreach ($adjacents as $adjacent)
                {
                //var_dump($adjacents);
                //var_dump($sectors);
                if (isset($sectors[$adjacent]))
                    $forbidden[$adjacent] =
                    $sectors[$adjacent];
                }
            $forbidden[$sector->getCoordinateX().",".$sector->getCoordinateY()] = $sector;
            }
        elseif (!($sector->getIsLand()))
            $forbidden[$sector->getCoordinateX().",".$sector->getCoordinateY()] =
                $sectors[$sector->getCoordinateX().",".$sector->getCoordinateY()];
        }
    return ($forbidden);
}

function getAdjacentsByMaxDistance ($x, $y, $distance) {

    global $origins;

    if ($distance>0)
        {
        $adjacents = getAdjacents ($x, $y);
        $origins = array_merge($origins, $adjacents);
        $origins = array_unique($origins);
        if ($distance>1)
            {
            foreach ($adjacents as $adjacent)
                {
                $adjacent = explode(",", $adjacent);
                $x = $adjacent[0];
                $y = $adjacent[1];
                getAdjacentsByMaxDistance ($x, $y, $distance-1);
                }
            }
        }
}

function getAdjacents ($x, $y) {

    global $MAX_WIDTH, $MAX_HEIGHT;

    $i = 1;

    if ($x%2==0)
        $adjacents = array($x.",".($y-$i), ($x+$i).",".($y-$i), ($x+$i).",".$y, $x.",".($y+$i), ($x-$i).",".$y, ($x-$i).",".($y-$i));
    else
        $adjacents = array($x.",".($y-$i), ($x+$i).",".$y, ($x+$i).",".($y+$i), $x.",".($y+$i), ($x-$i).",".($y+$i), ($x-$i).",".$y);

    foreach ($adjacents as $index=>$adjacent)
        {
        $adjacent = explode(",", $adjacent);
        if (($adjacent[0]>$MAX_WIDTH) || ($adjacent[1]>$MAX_HEIGHT) || ($adjacent[0]<0) || ($adjacent[1]<0))
            unset($adjacents[$index]);
        }

    return ($adjacents);
}

?>
