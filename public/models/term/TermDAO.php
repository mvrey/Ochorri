<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");


class TermDAO extends DAO {

public function getAllTerms ($language='spanish') {
    $connection = $this->getConnection();
    $query = "SELECT term_id, term_".$language." FROM Term";
    $result = $connection->Execute ($query);
    return ($result->GetArray());
}

public function getTranslation ($id, $language) {
    $connection = $this->getConnection();
    $query = "SELECT term_".$language." FROM Term WHERE term_id=".$id;
    $result = $connection->GetOne ($query);
    return ($result);
}

}
?>