<?php

Class Division {

    var $id;
    var $ownerId;
    var $unitId;
    var $unit;          //Absurde redundance, but i'm running out of time
    var $quantity;
    var $remainingHealth;    //Used in battles;

    var $isMoving;

function Division ($id=0, $ownerId=0, $unitId=0, $quantity=0, $isMoving=0, $remainingHealth=0) {

    $this->id = $id;
    $this->ownerId = $ownerId;
    $this->unitId = $unitId;
    $this->quantity = $quantity;
    $this->isMoving = $isMoving;
    $this->remainingHealth = $remainingHealth;

    return $this;
}

public function getId () {
    return $this->id;
}

public function getOwnerId () {
    return $this->ownerId;
}

public function getUnitId () {
    return $this->unitId;
}

public function getUnit () {
    return $this->unit;
}

public function setUnit ($value) {
    $this->unit=$value;
}

public function getQuantity () {
    return $this->quantity;
}

public function setQuantity ($value) {
    $this->quantity=$value;
}

public function getIsMoving() {
    return $this->isMoving;
}

public function setIsMoving ($value) {
    $this->isMoving = $value;
}

public function getRemainingHealth() {
    return $this->remainingHealth;
}

public function setRemainingHealth ($value) {
    $this->remainingHealth = $value;
}

}
?>