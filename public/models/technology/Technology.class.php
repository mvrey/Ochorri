<?php
  
class Technology {

        var $id;
	var $name;
	var $startAge;
	var $endAge;
	var $visibleAge;

        var $costs;
	var $increments;

	var $upgradable;
	var $time;
	var $incrementTime;
	var $picture;
        var $description;

        var $level;
        var $progress;
        var $dateStartProgress;
        var $dateEndProgress;

        var $isAge;


function Technology ($id=0, $name="", $startAge=0, $endAge=0, $visibleAge=0, $costs=array(), $increments=array(), $upgradable=0, $time=0, $incrementTime=0, $picture="", $description="", $isAge=0, $level=0, $progress=0, $dateStartProgress=0, $dateEndProgress=0) {

        $this->id = $id;
	$this->name=$name;
	$this->startAge=$startAge;
	$this->endAge=$endAge;
	$this->visibleAge=$visibleAge;

        $this->costs=$costs;
        $this->increments=$increments;

	$this->upgradable= $upgradable;
	$this->time= $time;
	$this->incrementTime= $incrementTime;
	$this->picture= $picture;
        $this->description= $description;
        $this->isAge = $isAge;
        
        $this->level = $level;
        $this->progress = $progress;
        $this->dateStartProgress = $dateStartProgress;
        $this->dateEndProgress = $dateEndProgress;
}


public function getId() {
    return ($this->id);
}

public function getName() {
    return ($this->name);
}

public function setName($value) {
    $this->name = $value;
}

public function getStartAge() {
    return ($this->startAge);
}

public function getEndAge() {
    return ($this->endAge);
}

public function getVisibleAge() {
    return ($this->visibleAge);
}

public function getCosts() {
    return ($this->costs);
}

public function setCosts($value) {
    $this->costs = $value;
}

public function getIncrements() {
    return ($this->increments);
}

public function setIncrements($value) {
    $this->increments = $value;
}

public function getUpgradable() {
    return ($this->upgradable);
}

public function getTime() {
    return ($this->time);
}

public function getIncrementTime() {
    return ($this->incrementTime);
}

public function getPicture() {
    return ($this->picture);
}

public function getDescription() {
    return ($this->description);
}

public function getLevel() {
    return ($this->level);
}

public function setLevel($value) {
    $this->level = $value;
}

public function getProgress() {
    return ($this->progress);
}

public function setProgress($value) {
    $this->progress = $value;
}

public function getDateStartProgress() {
    return ($this->dateStartProgress);
}

public function setDateStartProgress($value) {
    $this->dateStartProgress = $value;
}

public function getDateEndProgress() {
    return ($this->dateEndProgress);
}

public function setDateEndProgress($value) {
    $this->dateEndProgress = $value;
}

public function getIsAge() {
    return ($this->isAge);
}

}
?>