<?php
  
class Sector{
    var $id;
    var $coordinateX;
    var $coordinateY;
    var $name;
    var $occupant;
    var $owner;
    var $isLand;
    var $edge;
    var $distance;
    var $isBattle;
    var $isCapitol;

    var $productions= array();
    var $spends= array();
    var $productionBases=array();

    var $buildings;
    var $battle;


    
function Sector ($id=0, $coordinateX=0, $coordinateY=0, $name="",$occupant="",$owner="",$isLand=1,$edge=0,$productions=array(),$spends=array(), $isBattle=0, $productionBases=array(), $isCapitol=false)
{
//require("../config/paths.php");	//Si ponemos require once, casca tras el primer objeto creado

    if (!$occupant)
        $occupant = 0;
    if (!$owner)
        $owner = 0;
    
    $this->id=$id;
    $this->coordinateX=$coordinateX;
    $this->coordinateY=$coordinateY;
    $this->name=$name;
    $this->occupant=$occupant;
    $this->owner=$owner;
    $this->isLand=$isLand;
    $this->edge=$edge;
    
    $this->productions=$productions;
    $this->spends=$spends;
    $this->productionBases = $productionBases;

    $this->isBattle=$isBattle;
    $this->isCapitol=$isCapitol;
}

static function getSector($coord) {
         $sect=Sector::$sectores->get($coord);
         return($sect);
}

function getId(){
    return $this->id;
}

function getCoordinateX(){
    return $this->coordinateX;
}

function getCoordinateY(){
    return $this->coordinateY;
}

function getName() {
         return $this->name;
}

function getIsLand() {
    return $this->isLand;
}

function getOwner() {
    return $this->owner;
}

function getDistance() {
    return $this->distance;
}

function setDistance($value) {
    $this->distance = $value;
}

function getOccupant() {
    return $this->occupant;
}

function getProductions() {
    return $this->productions;
}

function getSpends() {
    return $this->spends;
}

function setSpends($value) {
    $this->spends = $value;
}

function updateCosts($newCosts = Array(), $mode=1, $operator) {
//mode=0 substitute; mode=1 add to existent
    require_once ('../DAO/DAO.class.php');
    $connection = new DAO();
    $connection->connect();

    $connection->updateSectorCosts($this->getId(), $newCosts, $mode, $operator);
}

function updateIncomes($buildingId) {
//mode=0 substitute; mode=1 add to existent
    require_once ('../DAO/DAO.class.php');
    $connection = new DAO();
    $connection->connect();

    $connection->updateSectorProductionsByNewBuilding($buildingId, $this->getId());
}

function getBuildings() {
    return $this->buildings;
}

function setBuildings($value) {
    $this->buildings = $value;
}

function getisBattle() {
    return $this->isBattle;
}

function setisBattle($value) {
    $this->isBattle = $value;
}

function getisCapitol() {
    return $this->isCapitol;
}

function setisCapitol($value) {
    $this->isCapitol = $value;
}

function getBattle() {
    return $this->battle;
}

function setBattle($value) {
    $this->battle = $value;
}

function getProductionBases() {
    return $this->productionBases;
}

function setProductionBases($value) {
    $this->productionBases = $value;
}

static function indexByCoordinate($sectors){
    $newSectors = array();
    foreach($sectors as $sector)
        $newSectors[$sector->getCoordinateX().",".$sector->getCoordinateY()] = $sector;
    return ($newSectors);
}

static function sortByCoordinate($sectors){
    global $MAX_WIDTH, $MAX_HEIGHT;

    $newSectors = array();
    for ($x=0; $x<$MAX_WIDTH; $x++)
        {
        for ($y=0; $y<$MAX_HEIGHT; $y++)
            {
            if (isset($sectors[$x.",".$y]))
                $newSectors[$x.",".$y] = $sectors[$x.",".$y];
            }
        }
    return ($newSectors);
}

static function getReachables($sectors, $x, $y, $distance) {

    global $reachableSectors;
    global $player;
    global $startX, $startY;

    if (array_key_exists($x.",".$y, $sectors))
        {
        $sector = $sectors[$x.",".$y];
        $isHQ = false;
        $sectorBuildings = $sector->getBuildings();
        $sectorBattle = $sector->getBattle();
        if (isset($sectorBattle))
            {
            $attackerId = $sectorBattle->getAttackerId();
            $denfenderId = $sectorBattle->getDefenderId();
            }
        
        if (isset($sectorBuildings[1]))
            {
            $HQ = $sectorBuildings[1];
            if ($HQ->getLevel()>=1)
                $isHQ = true;
            }

        if (($sector!=null) && ($sector->getIsLand()) && (!($sector->getIsBattle()) || ($player->getId()==$attackerId) || ($player->getId()==$denfenderId)))
            {
            if (!array_key_exists($x.",".$y, $reachableSectors) || ($sector->getDistance() > $distance))
                {
                $sector->setDistance($distance);

                if (!(($x==$startX) && ($y==$startY)))
                    $reachableSectors[$x.",".$y] = $sector;

                if (($sector->getOwner()==$player->getId()) && ($isHQ))
                    {
                    if ($x%2==0)
                        $next = array($x.",".($y-1), ($x+1).",".($y-1), ($x+1).",".$y, $x.",".($y+1), ($x-1).",".$y, ($x-1).",".($y-1));
                    else
                        $next = array($x.",".($y-1), ($x+1).",".$y, ($x+1).",".($y+1), $x.",".($y+1), ($x-1).",".($y+1), ($x-1).",".$y);

                    foreach ($next as $nxt)
                        {
                        $nxt = explode(",", $nxt);
                        $y = $nxt[1];
                        $x = $nxt[0];
                        Sector::getReachables($sectors, $x, $y, $distance+1);
                        }
                    }
                }
            }
        }
}

public function getNameString () {

    $nameString = $this->getName()."(".$this->getCoordinateX().",".$this->getCoordinateY().")";
    return ($nameString);
}


public function getDistanceFrom ($prevX, $prevY) {

$endX = $this->getCoordinateX();
$endY = $this->getCoordinateY();
$distance=0;

while (!( ($prevX==$endX)&&($prevY==$endY)) )
    {
    $distance++;
    $x = $prevX;
    $y = $prevY;
    if ($x%2==0)
        {
        $nextX = array($x, ($x+1), ($x+1), $x, ($x-1), ($x-1));
        $nextY = array(($y-1), ($y-1), $y, ($y+1), $y, ($y-1));
        }
    else
        {
        $nextX = array($x, ($x+1), ($x+1), $x, ($x-1), ($x-1));
        $nextY = array(($y-1), $y, ($y+1), ($y+1), ($y+1), $y);
        }


    $validIndexs = array();
    foreach ($nextX as $iX=>$nxtX)
        {
        $maxX = max($endX, $nxtX);
        $minX = min($endX, $nxtX);
        $maxEX = max($endX, $prevX);
        $minEX = min($endX, $prevX);
        if ($maxX-$minX<=$maxEX-$minEX)
            {
            $prevX = $nxtX;
            $validIndexs[]=$iX;
            }
        }

    foreach ($validIndexs as $index)
        {
        $nxtY = $nextY[$index];
        $maxY = max($endY, $nxtY);
        $minY = min($endY, $nxtY);
        $maxEY = max($endY, $prevY);
        $minEY = min($endY, $prevY);
        if ($maxY-$minY<=$maxEY-$minEY)
            {
            if ($nextX[$index]==$prevX)
                {
                $prevY = $nxtY;
                $selectedIndex = $index;
                }
            }
        }

        $prevX = $nextX[$selectedIndex];
        //This would be printing the chosen path
        //echo $prevX." ".$prevY."<br>";
    }

return ($distance);
}

public function getDistanceFromCapitolSector ($playerId, $connection) {

    $capitolSectorArr = $connection->getCapitolSector($playerId);
    if ($capitolSectorArr)
        {
        $capitolSector = new Sector ($capitolSectorArr[0], $capitolSectorArr[1],$capitolSectorArr[2],$capitolSectorArr[3],$capitolSectorArr[4],$capitolSectorArr[5],$capitolSectorArr[6],$capitolSectorArr[7],explode(",",$capitolSectorArr[8]),explode(",",$capitolSectorArr[9]), $capitolSectorArr[10]);
        return ($this->getDistanceFrom($capitolSector->getCoordinateX(), $capitolSector->getCoordinateY()));
        }
    else 
        return (false);
}

public static function insertRandomSector ($x, $y) {

    global $SECTOR_NAMES, $WATER_PROBABILITY, $MIN_PRODUCTIONS, $MAX_PRODUCTIONS;
    global $sectorConn;

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


    $sectorConn->insertSector($x, $y, $name, $isLand, $productionString, '0,0,0,0,0', $productionString);

}

public static function getForbidden($sectors) {
    //Probemos de nuevo, esta vez con todos los sectores, eliminando los marítimos u ocupados y colindantes
    global $minDistance, $origins;
    $forbidden = array();

    foreach ($sectors as $index=>$sector)
        {
        if (($sector->getOwner()) || ($sector->getOccupant()))
            {
            Sector::getAdjacentsByMaxDistance ($sector->getCoordinateX(), $sector->getCoordinateY(), $minDistance);
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

public static function getAdjacentsByMaxDistance ($x, $y, $distance) {

    global $origins;

    if ($distance>0)
        {
        $adjacents = Sector::getAdjacents ($x, $y);
        $origins = array_merge($origins, $adjacents);
        $origins = array_unique($origins);
        if ($distance>1)
            {
            foreach ($adjacents as $adjacent)
                {
                $adjacent = explode(",", $adjacent);
                $x = $adjacent[0];
                $y = $adjacent[1];
                Sector::getAdjacentsByMaxDistance ($x, $y, $distance-1);
                }
            }
        }
}


//returns an array of coordinates adjacent to the ones given
public static function getAdjacents ($x, $y) {

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


//returns max possible distance in a group of sectors
public static function getMaxDistance ($sectors) {
    $checked = array();
    $maxDistance = 0;
    
    for ($i=0; $i<count($sectors); $i++)
        {
        for ($j=0; $j<count($sectors); $j++)
            {
            $sector1 = $sectors[$i];
            $sector2 = $sectors[$j];
            if (!isset($checked[$sector1->getId()][$sector2->getId()]))
                {
                $newDistance = $sector1->getDistanceFrom ($sector2->getCoordinateX(), $sector2->getCoordinateY());
                $checked[$sector1->getId()][$sector2->getId()] = true;
                $checked[$sector2->getId()][$sector1->getId()] = true;
                if ($newDistance>$maxDistance)
                    $maxDistance = $newDistance;
                }
            }
        }
    return ($maxDistance);
}

//Returns a subarray of sectors in $sectors whose owner is $playerId
public static function getOwnedSectors($sectors, $playerId) {

    $ownedSectors = array();
    foreach ($sectors as $sector)
        {
        if ($sector->getOwner()==$playerId)
            $ownedSectors[] = $sector;
        }
    return ($ownedSectors);
}

}
?>