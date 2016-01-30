<?php

class Building {

    var $id;
    var $name;
    var $image;
    var $health;
    var $startAge;
    var $endAge;

    var $upgradable;	/*1=Si 0=No*/

    var $productionCost = array();
    var $incrementCost = array();
    var $manteinanceCost = array();
    var $advanceCost = array();

    var $time;		/* de construcción, en segundos*/
    var $incrementTime;	/* incremento del tiempo de construcción por nivel (FLOAT)*/

    var $description;

    var $upgradesTo;
    var $autoUpgrade;
    
    var $level;
    var $percent;
    var $dateStarted;
    var $dateStopped;
    var $remainingHealth;

    var $productionMods;
    

    
function Building ($id=0, $name="", $image="", $health=0, $startAge=0, $endAge=0, $upgradable=0, $productionCost = array(), $incrementCost = array(), $manteinanceCost = array(), $advanceCost = array(), $time=0, $incrementTime=0, $description="", $upgradesTo=NULL, $autoUpgrade=0, $level=0, $dateStarted=0, $dateStopped=0, $remainingHealth=0) {

    $this->id=$id;
    $this->name=$name;
    $this->image=$image;
    $this->health=$health;

    $this->startAge=$startAge;
    $this->endAge=$endAge;
    $this->upgradable=$upgradable;

    $this->productionCost=$productionCost;
    $this->incrementCost=$incrementCost;
    $this->manteinanceCost=$manteinanceCost;
    $this->advanceCost=$advanceCost;

    $this->time=$time;
    $this->incrementTime=$incrementTime;
    $this->description=$description;

    $this->level=$level;
    $this->dateStarted = $dateStarted;
    $this->dateStopped = $dateStopped;
    $this->remainingHealth = $remainingHealth;

    $this->upgradesTo=$upgradesTo;
    $this->autoUpgrade=$autoUpgrade;
}

public function getId(){
    return ($this->id);
}

public function getName(){
    return ($this->name);
}

public function setName($value){
    $this->name = $value;
}

public function getHealth() {
    return $this->health;
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

public function getProductionCost() {
    return $this->productionCost;
}

public function setProductionCost($value) {
    $this->productionCost = $value;
}

public function getIncrementCost() {
    return $this->incrementCost;
}

public function getManteinanceCost() {
    return $this->manteinanceCost;
}

public function setManteinanceCost($value) {
    $this->manteinanceCost = $value;
}

public function getAdvanceCost() {
    return $this->advanceCost;
}

public function getUpgradable(){
    return ($this->upgradable);
}

public function getTime() {
    return $this->time;
}

public function setTime($value) {
    $this->time = $value;
}

public function getIncrementTime() {
    return $this->incrementTime;
}

public function getDescription() {
    return $this->description;
}

public function getLevel() {
    return $this->level;
}

public function setLevel($value) {
    $this->level = $value;
}

public function getDateStarted() {
    return $this->dateStarted;
}

public function setDateStarted($value) {
    $this->dateStarted = $value;
}

public function getDateStopped() {
    return $this->dateStopped;
}

public function setDateStopped($value) {
    $this->dateStopped = $value;
}

public function getPercent() {
    return $this->percent;
}

public function setPercent($value) {
    $this->percent = $value;
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

public function getRemainingHealth() {
    return $this->remainingHealth;
}

public function setRemainingHealth($value) {
    $this->remainingHealth = $value;
}

public function getProductionMods() {
    return $this->productionMods;
}

public function setProductionMods($value) {
    $this->productionMods = $value;
}

public function updateResourceIncrements() {

    if ($this->getUpgradable())
        {
        $this->updateProductionCost();
        $this->updateManteinanceCost();
        }
}


//Hacer Compuestos con las 3 funciones inferiores
private function updateProductionCost() {

    $productionCost = $this->getProductionCost();
    $incrementCost = $this->getIncrementCost();
    $newProductionCost = array();
    for ($i=0; $i<count($productionCost); $i++)
        {
        $actualCost = $productionCost[$i]*(pow(1+$incrementCost[$i],$this->getLevel()));
        array_push($newProductionCost,$actualCost);
        }
    $this->setProductionCost($newProductionCost);
}

//Sets the difference with the previous level for adding it to the Sector field on DB.
//Be careful on using this function.
public function updateManteinanceCost() {
var_dump($this);
    $manteinanceCost = $this->getManteinanceCost();
    $incrementCost = $this->getIncrementCost();
    $newManteinanceCost = array();

    if ($this->getLevel()>2)
        {
        for ($i=0; $i<count($manteinanceCost); $i++)
            {
            $actualCost = $manteinanceCost[$i]*(pow(1+$incrementCost[$i],$this->getLevel()-1));
            $previousCost = $manteinanceCost[$i]*(pow(1+$incrementCost[$i],$this->getLevel()-2));
            array_push($newManteinanceCost, $actualCost-$previousCost);
            }
        }
    elseif (($this->getLevel()==2))
        {
        for ($i=0; $i<count($manteinanceCost); $i++)
            {
            array_push($newManteinanceCost, $manteinanceCost[$i]*$incrementCost[$i]);
            }
        }
    else
        $newManteinanceCost = $manteinanceCost;

    $this->setManteinanceCost($newManteinanceCost);
}

public function updateTime() {

    if ($this->getUpgradable())
        {
        $this->setTime($this->getTime()*(pow(1+$this->getIncrementTime(),$this->getLevel())));
        }
}

}
?>