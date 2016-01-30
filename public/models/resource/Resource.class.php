<?php

class Resource {

var $id;
var $name;
var $startAge;
var $endAge;
var $basic;
var $img;


function Resource($id=0, $name="", $startAge=0, $endAge=0, $basic=1, $img='') {

    $this->id=$id;
    $this->name=$name;
    $this->startAge=$startAge;
    $this->endAge=$endAge;
    $this->basic=$basic;
    $this->img=$img;

    return $this;
}

public function getId() {
    return ($this->id);
}

public function getName() {
    return ($this->name);
}

public function getStartAge() {
    return ($this->startAge);
}

public function getEndAge() {
    return ($this->endAge);
}

public function getBasic() {
    return ($this->basic);
}

public function getImg() {
    return ($this->img);
}
}
?>