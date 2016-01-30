<?php

Class BattleMod {

    var $id;
    var $name;
    var $operation;
    var $value;
    var $targetClassId;
    var $operator2;


function BattleMod ($id=0, $name='', $operation='*', $value=0, $targetClassId=0, $operator2=null) {
    
    $this->id = $id;
    $this->name = $name;
    $this->operation = $operation;
    $this->value = $value;
    $this->targetClassId = $targetClassId;
    $this->operator2 = $operator2;

    return $this;
}

public function getId() {
    return $this->id;
}

public function getName() {
    return $this->name;
}

public function getOperation() {
    return $this->operation;
}

public function getValue() {
    return $this->value;
}

public function getTargetClassId() {
    return $this->targetClassId;
}

public function getOperator2() {
    return $this->operator2;
}

}
?>