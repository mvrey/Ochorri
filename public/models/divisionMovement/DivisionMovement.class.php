<?php

class DivisionMovement {
    
    var $id;
    var $ownerId;
    var $startSector;
    var $endSector;
    var $startDateTime;
    var $time;

    var $divisions = array();

function DivisionMovement ($id, $ownerId, $startSector=0, $endSector=0, $startDateTime=0, $time=0, $divisions=array()) {

    $this->id = $id;
    $this->ownerId = $ownerId;
    $this->startSector = $startSector;
    $this->endSector = $endSector;
    $this->startDateTime = $startDateTime;
    $this->time = $time;
    $this->divisions = $divisions;
}

public function getId() {
    return $this->id;
}

public function getOwnerId() {
    return $this->ownerId;
}

public function getStartSector() {
    return $this->startSector;
}

public function setStartSector ($value) {
    $this->startSector = $value;
}

public function getEndSector() {
    return $this->endSector;
}

public function setEndSector ($value) {
    $this->endSector = $value;
}

public function getStartDateTime() {
    return $this->startDateTime;
}

public function setStartDateTime ($value) {
    $this->startDateTime  = $value;
}

public function getTime() {
    return $this->time;
}

public function setTime ($value) {
    $this->time = $value;
}

public function getDivisions() {
    return $this->divisions;
}

public function setDivisions ($value) {
    $this->divisions = $value;
}

}
?>
