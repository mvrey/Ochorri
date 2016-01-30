<?php

class Player {

var $id;
var $nick;
var $password;
var $email;
var $age;
var $flag;
var $avatar;
var $civName;

var $resources=array();
var $balances=array();
var $lastUpdate=array();
var $lastMapOrigin=array();
var $lastMapHeight;

var $availableUnits=array();
var $availableBuildings=array();
var $availableTechnologies=array();
var $availableResources=array();

var $sectors=array();
var $visibleSectors=array();
var $reachableSectors=array();
var $battles=array();

static $players;


function Player($id=0, $nick="", $password="", $email="", $age=0, $flag='', $avatar='', $civName='', $resources=array(), $lastUpdate=0, $availableUnits=array()) {

    $this->id=$id;
    $this->nick=$nick;
    $this->password=$password;
    $this->email=$email;
    $this->age=$age;
    $this->flag=$flag;
    $this->avatar=$avatar;
    $this->civName=$civName;
    $this->resources=$resources;
    $this->availableUnits=$availableUnits;
    $this->lastUpdate= $lastUpdate;

    return $this;
}

public function getId() {
    return ($this->id);
}

public function getNick() {
    return ($this->nick);
}

public function getAge() {
    return ($this->age);
}

public function setAge($value) {
    $this->age = $value;
}

public function getFlag() {
    return ($this->flag);
}

public function getAvatar() {
    return ($this->avatar);
}

public function getcivName() {
    return ($this->civName);
}

public function getAvailableUnits() {
    return ($this->availableUnits);
}

public function setAvailableUnits($value) {
    $this->availableUnits = $value;
}

public function getAvailableBuildings() {
    return ($this->availableBuildings);
}

public function setAvailableBuildings($value) {
    $this->availableBuildings = $value;
}

public function getAvailableTechnologies() {
    return ($this->availableTechnologies);
}

public function setAvailableTechnologies($value) {
    $this->availableTechnologies = $value;
}

public function getAvailableResources() {
    return ($this->availableResources);
}

public function setAvailableResources($value) {
    $this->availableResources = $value;
}

public function getSectors() {
    return ($this->sectors);
}

public function setSectors($value) {
    $this->sectors = $value;
}

public function getVisibleSectors() {
    return ($this->visibleSectors);
}

public function setVisibleSectors($value) {
    $this->visibleSectors = $value;
}

public function getReachableSectors() {
    return ($this->reachableSectors);
}

public function setReachableSectors($value) {
    $this->reachableSectors = $value;
}

public function setResources($value) {
    $this->resources = $value;
}

public function getResources() {
    return ($this->resources);
}

public function getBalances() {
    return ($this->balances);
}

public function setBalances($value) {
    $this->balances = $value;
}

public function getLastUpdate() {
    return ($this->lastUpdate);
}

public function setLastUpdate($value) {
    $this->lastUpdate = $value;
}

public function getLastMapOrigin() {
    return ($this->lastMapOrigin);
}

public function setLastMapOrigin($value) {
    $this->lastMapOrigin = $value;
}

public function getLastMapHeight() {
    return ($this->lastMapHeight);
}

public function setLastMapHeight($value) {
    $this->lastMapHeight = $value;
}

function updateResources($resources = Array()) {

    require_once ('../DAO/DAO.class.php');
    $connection = new DAO();
    $connection->connect();

    $connection->updateSectorCosts($this->getId(), $newCosts, 1, 0);
}

}
?>
<?php
/*Jugador::init_jugadores();
$asdf=Jugador::extraer_jugador('prueba');
echo $asdf->max_pob;*/
?>