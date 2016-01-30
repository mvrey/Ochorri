<?php
if (isset($absolute_path) && ($absolute_path))
    require_once ($path."DAO/DAO.class.php");
else
    require_once("../../models/DAO/DAO.class.php");

class MessageDAO extends DAO {

public function insertMessage ($from, $to, $subject, $content) {

    $connection = $this->getConnection();
    $query = "INSERT INTO Message(message_from, message_to, message_subject, message_content, message_date, message_read)
        VALUES (".$from.", ".$to.", '".$subject."', '".$content."', '".date("d-M-Y  H:i")."', 0)";
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
    return ($result);
}

public function setMessageRead ($msgId) {

    $connection = $this->getConnection();
    $query = "UPDATE Message SET message_read=true WHERE message_id=".$msgId;
    $result = $connection->Execute ($query);
    return ($result);
}

public function setMessageDeleted ($msgId) {

    $connection = $this->getConnection();
    $query = "UPDATE Message SET message_deleted=true WHERE message_id=".$msgId;
    $result = $connection->Execute ($query);
    return ($result);
}

}
?>