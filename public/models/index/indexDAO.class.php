<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class indexDAO extends DAO {

public function getPlayerExists ($nick,$password="") {

    $connection = $this->getConnection();
    $query = "SELECT player_nick FROM Player where player_nick='$nick'"." AND player_password="."'$password'";
    $result = $connection->Execute ($query);

    if ($result->RecordCount())
        return true;
    else
        return false;
    }
}
?>