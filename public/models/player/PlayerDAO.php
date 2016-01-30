<?php

if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class PlayerDAO extends DAO {

public function getAllPlayers() {
    $connection = $this->getConnection();
    $query = "SELECT * FROM Player ORDER BY player_id";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }

public function getPlayerExists ($nick,$password="") {

    $connection = $this->getConnection();
    $query = "SELECT player_nick FROM Player where player_nick='$nick'"." AND player_password="."'$password'";
    $result = $connection->Execute ($query);

    if ($result->RecordCount())
        return true;
    else
        return false;
    }

public function getPlayerByNick($nick) {
    $connection = $this->getConnection();
    $query = "SELECT * FROM Player WHERE player_nick='".$nick."'";
    $result = $connection->Execute ($query);
    return ($result->fields);
    }


public function getAvailableResources($playerAge) {
    $connection = $this->getConnection();
    $query = "SELECT * FROM Resource WHERE resource_startAge<=".$playerAge." AND resource_endAge>".$playerAge;
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }


public function getAvailableUnits ($player) {

    $connection = $this->getConnection();
    $query = "SELECT *
            FROM Unit
            WHERE unit_startAge<=".$player->getAge()."
                AND unit_endAge>".$player->getAge()."
                AND unit_id NOT IN (
                    SELECT requirement_targetId FROM Requirement WHERE requirement_targetClassId='Unit')
            UNION
            SELECT unit_id, unit_nameId, unit_attack, unit_health, unit_speed, unit_startAge, unit_endAge, unit_pictureURL, unit_classId, unit_productionCost, unit_manteinanceCost, unit_advanceCost, unit_time, unit_descriptionId, unit_upgradesTo, unit_autoUpgrade
            FROM (
                SELECT a.*, b.requirement_requirementId, b.requirement_requirementClass, b.requirement_level
                    FROM Unit a INNER JOIN Requirement b ON ((b.requirement_targetId=a.unit_id) AND (b.requirement_targetClassId='Unit'))) e
            INNER JOIN
            (SELECT d.technologyLink_technologyId, d.technologyLink_level
                FROM Player c INNER JOIN TechnologyLink d ON (c.player_id=d.technologyLink_playerId)
                WHERE c.player_id=".$player->getId().") f
            ON ((e.requirement_requirementId=f.technologyLink_technologyId) AND (e.requirement_level=f.technologyLink_level))
            ORDER BY 1";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function getAvailableTechnologies ($playerId, $playerAge) {

    $connection = $this->getConnection();
    
    $query = "SELECT x.*, y.technologyLink_level, y.technologyLink_progress, y.technologyLink_dateStartProgress, y.technologyLink_dateEndProgress FROM (
                SELECT *
                FROM Technology
                WHERE technology_startAge<=".$playerAge."
                    AND technology_endAge>".$playerAge."
                    AND technology_id NOT IN (
                        SELECT requirement_targetId FROM Requirement WHERE requirement_targetClassId='Technology')
                UNION
                SELECT technology_id, technology_nameId, technology_startAge, technology_endAge, technology_visibleAge, technology_cost, technology_incrementCost, technology_upgradable, technology_time, technology_incrementTime, technology_pictureURL, technology_descriptionId, technology_isAge
                FROM (
                    SELECT a.*, b.requirement_requirementId, b.requirement_requirementClass, b.requirement_level
                        FROM Technology a INNER JOIN Requirement b ON ((b.requirement_targetId=a.technology_id) AND (b.requirement_targetClassId='Technology'))) e
                INNER JOIN
                    (SELECT d.technologyLink_technologyId, d.technologyLink_level
                        FROM Player c INNER JOIN TechnologyLink d ON (c.player_id=d.technologyLink_playerId)
                        WHERE c.player_id=".$playerId.") f
                    ON ((e.requirement_requirementId=f.technologyLink_technologyId) AND (e.requirement_level=f.technologyLink_level))) x
                LEFT JOIN
                    (SELECT *
                    FROM TechnologyLink WHERE technologyLink_playerId=".$playerId.") y
                ON x.technology_id=y.technologyLink_technologyId
            ORDER BY 1";
    $result = $connection->Execute ($query);
    return ($result);
}

public function getLastMapView ($playerId) {

    $connection = $this->getConnection();
    $query = "SELECT player_lastMapOrigin, player_lastMapHeight FROM Player WHERE player_id=".$playerId;
    $result = $connection->Execute ($query);
    return ($result->fields);
}

public function setLastMapView ($playerId, $origin, $height) {

    $connection = $this->getConnection();
    $query = "UPDATE Player set player_lastMapOrigin='".$origin."', player_lastMapHeight=".$height."
            WHERE player_id=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function getMessages ($playerId, $received=true, $read=true) {
    //$received is either you get received or sent messages
    //$read is if you get already read messages
    $connection = $this->getConnection();
    $query = "SELECT * FROM Message
        WHERE message_to=".$playerId." AND message_deleted=false";
    if (!$read)
        $query .= " AND message_read=false";
    $query .= " ORDER BY message_date DESC";

    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function getBattleCosts ($playerId, $sectorId=false) {

    $connection = $this->getConnection();
    $query = "SELECT * FROM BattleCosts a INNER JOIN Battle b ON a.battleCosts_battleId=b.battle_id
WHERE a.battleCosts_ownerId=".$playerId;
    if ($sectorId)
        $query .= " AND b.battle_sectorId=".$sectorId." ORDER BY battle_isOver";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function insertBattleCosts ($battleId, $playerId, $costs) {

    $connection = $this->getConnection();
    $query = "INSERT INTO BattleCosts (battleCosts_battleId, battleCosts_ownerId, battleCosts_costs)
        VALUES (".$battleId.", ".$playerId." ,'".$costs."')";
    $result = $connection->Execute ($query);
    return ($result);
}

public function updateBattleCosts ($battleId, $playerId, $newCosts = array(), $mode=1, $operator='+')
{
    //mode=0 substitute; mode=1 operate with existent
    $connection = $this->getConnection();
    if ($mode==1)
        {
        $query = "SELECT battleCosts_costs FROM BattleCosts
            WHERE battleCosts_battleId=".$battleId." AND battleCosts_ownerId=".$playerId;
        $result = $connection->Execute ($query);
        $costs = explode(",", $result->fields[0]);

        if ($operator=='+')
            {
            for($i=0; $i<count($costs); $i++)
                {
                $costs[$i] = $costs[$i]+$newCosts[$i];
                }
            }
        elseif ($operator=='-')
            {
            for($i=0; $i<count($costs); $i++)
                {
                $costs[$i] = $costs[$i]-$newCosts[$i];
                }
            }
        }
    else
        $costs = $newCosts;

    $query = "UPDATE BattleCosts SET battleCosts_costs='".implode(",", $costs)."'
        WHERE battleCosts_battleId=".$battleId." AND battleCosts_ownerId=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function updatePlayerResources ($playerId, $resources, $time) {

    $resources = explode(",", $resources);
    foreach ($resources as $resource)
        $resource = round($resource, 6);
    $resources = implode(",", $resources);

    $connection = $this->getConnection();
    $query = "UPDATE Player SET player_resources='".$resources."', player_lastUpdate=".$time." WHERE player_id=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function getNickExists ($nick) {

    $connection = $this->getConnection();
    $query = "SELECT player_nick FROM Player where player_nick='$nick'";
    $result = $connection->Execute ($query);

    if ($result->RecordCount())
        return true;
    else
        return false;
    }

public function InsertNewPlayer($nick, $pass, $email, $flag, $avatar, $civName) {

    $connection = $this->getConnection();
    $query = "INSERT INTO Player (player_nick,player_password,player_email,player_age,player_flag,player_avatar,player_civName,player_resources, player_lastUpdate)
        VALUES ('".$nick."','".md5($pass)."', '".$email."', 1, '".$flag."', '".$avatar."','".$civName."','100,1000,1000,1000,0',".$_SERVER['REQUEST_TIME'].")";
    $result = $connection->Execute ($query);
    return ($result);
    }

public function deleteCapitolBuilding($playerId, $sectorId=false) {

    if (defined("HOME"))
        require_once (HOME."config/buildings.cfg.php");
    else
        require_once ("../../config/buildings.cfg.php");

    $connection = $this->getConnection();
    $query = "SELECT a.building_id FROM Building a INNER JOIN Sector b
                ON b.sector_ownerId=".$playerId." WHERE a.building_buildingClassId=".CAPITOL_ID."
                    AND a.building_level>0 AND a.building_sectorId=b.sector_id";
    if ($sectorId)
        $query .= " AND b.sector_id=".$sectorId;
    
    $listRS = $connection->Execute ($query);
    $result = false;
    while (!$listRS->EOF)
        {
        $query = "DELETE FROM Building
            WHERE building_id=".$listRS->fields["building_id"];
        $result = $connection->Execute ($query);
        $listRS->MoveNext();
        }

    return ($listRS->RecordCount());
}

public function getAgeAdvanceCosts ($playerId) {

    $connection = $this->getConnection();
    $query = "SELECT a.unit_advanceCost AS cost, b.division_quantity AS multiplier
        FROM Unit a INNER JOIN Division b ON a.unit_id=b.division_unitId
        WHERE division_ownerId=".$playerId."
        UNION
        SELECT a.buildingClass_advanceCost AS cost, b.building_level AS multiplier
        FROM BuildingClass a INNER JOIN Building b
        ON a.buildingClass_id=b.building_BuildingClassId
        WHERE b.building_sectorId IN
        (SELECT sector_id FROM Sector WHERE sector_ownerId=".$playerId.")";

    $rs = $connection->Execute ($query);

    $totalCosts = array();

    while (!$rs->EOF)
        {
        $i=0;
        $costs = explode(",", $rs->fields[0]);
        $multiplier = $rs->fields[1];
        for ($i=0; $i<count($costs); $i++)
            {
            if (!isset($totalCosts[$i]))
                $totalCosts[$i] = 0;
            $totalCosts[$i] += $costs[$i]*$multiplier;
            }

        $rs->MoveNext();
        }

    return ($totalCosts);
}

public function updatePlayerAge ($playerId, $ageId) {

    $connection = $this->getConnection();
    $query = "UPDATE Player set player_age=".$ageId." WHERE player_id=".$playerId;
    $result = $connection->Execute ($query);
    return ($result);
}

}
?>