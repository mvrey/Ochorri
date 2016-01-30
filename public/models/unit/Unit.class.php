<?php

Class Unit {

    var $id;
    var $name;
    var $attack;
    var $health;
    var $speed;
    var $startAge;
    var $endAge;
    var $image;
    var $class;
    var $productionCost;
    var $manteinanceCost;
    var $advanceCost;
    var $time;  //Efective training time on the specific sector
    var $description;

    var $upgradesTo;
    var $autoUpgrade;

    var $battleMods = array();


function Unit ($id=0, $name='', $attack=0, $health=0, $speed=0, $startAge=0, $endAge=0, $image='', $class=0, $productionCost='0,0,0,0,0', $manteinanceCost='0,0,0,0,0', $advanceCost='0,0,0,0,0', $time=0, $description='', $upgradesTo=NULL, $autoUpgrade=0) {
    
    $this->id = $id;
    $this->name = $name;
    $this->attack = $attack;
    $this->health = $health;
    $this->speed = $speed;
    $this->startAge = $startAge;
    $this->endAge = $endAge;
    $this->image = $image;
    $this->class = $class;
    $this->productionCost = $productionCost;
    $this->manteinanceCost = $manteinanceCost;
    $this->advanceCost = $advanceCost;
    $this->time = $time;
    $this->description = $description;

    $this->upgradesTo = $upgradesTo;
    $this->autoUpgrade = $autoUpgrade;

    return $this;

}

public function getId() {
    return $this->id;
}

public function getName() {
    return $this->name;
}

public function getAttack() {
    return $this->attack;
}

public function getHealth() {
    return $this->health;
}

public function getSpeed() {
    return $this->speed;
}

public function getStartAge() {
    return $this->startAge;
}

public function getEndAge() {
    return $this->endAge;
}

public function getImage() {
    return $this->image;
}

public function getClass() {
    return $this->class;
}

public function getproductionCost() {
    return $this->productionCost;
}

public function getmanteinanceCost() {
    return $this->manteinanceCost;
}

public function getadvanceCost() {
    return $this->advanceCost;
}

public function getTime() {
    return $this->time;
}

public function getDescription() {
    return $this->description;
}

public function getUpgradesTo() {
    return $this->upgradesTo;
}

public function setUpgradesTo($value) {
    $this->upgradesTo = $value;
}

public function getAutoUpgrade() {
    return $this->autoUpgrade;
}

public function setAutoUpgrade($value) {
    $this->autoUpgrade = $value;
}

public function getBattleMods() {
    return $this->battleMods;
}

public function setBattleMods($value) {
    $this->battleMods = $value;
}

public function getEfectiveManteinanceCosts($distanceFromCapitol) {

    //$unitCosts = explode(",", $this->getManteinanceCost());
    $unitCosts = $this->getManteinanceCost();
    for ($j=0; $j<count($unitCosts); $j++)
        {
        if ($unitCosts[$j])
            $unitCosts[$j] += $unitCosts[$j]*$distanceFromCapitol;
        }
    $unitCosts[4] += $distanceFromCapitol;

    return($unitCosts);
}

}
?>