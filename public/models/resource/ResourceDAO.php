<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class ResourceDAO extends DAO {

public function getAllResources() {
    $connection = $this->getConnection();
    $query = "select * from Resource";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
    }
    
}
?>