<?php

class StaticData {

    private static $instance;

    var $terms;
    var $units;
    var $buildings;
    var $technologies;
    var $players;
    var $sectors;
    var $resources;

// A private constructor; prevents object creation trough new.
private function __construct() {
    return null;
}

public static function singleton(){

if (!isset(self::$instance))
    {
    $c = __CLASS__;
    self::$instance = new $c;
    }

    return self::$instance;
}

public function __clone() {
    trigger_error('Clone not allowed.', E_USER_ERROR);
}

public function getTerms(){
    return $this->terms;
}

public function setTerms($value){
    $this->terms = $value;
}

public function getUnits(){
    return $this->units;
}

public function setUnits($value){
    $this->units = $value;
}

public function getTechnologies(){
    return $this->technologies;
}

public function setTechnologies($value){
    $this->technologies = $value;
}

public function getPlayers(){
    return $this->players;
}

public function setPlayers($value){
    $this->players = $value;
}

public function getBuildings(){
    return $this->buildings;
}

public function setBuildings($value){
    $this->buildings = $value;
}

public function getSectors(){
    return $this->sectors;
}

public function setSectors($value){
    $this->sectors = $value;

}

public function getResources(){
    return $this->resources;
}

public function setResources($value){
    $this->resources = $value;

}

}
?>