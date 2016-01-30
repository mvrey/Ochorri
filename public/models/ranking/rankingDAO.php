<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class RankingDAO extends DAO {

public function getRanking() {
    $connection = $this->getConnection();
    $query = "select * from ranking";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }

}
?>