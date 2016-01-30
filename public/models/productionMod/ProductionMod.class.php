<?php

Class ProductionMod {

    var $id;
    var $targetClassId;
    var $targetId;
    var $resourceId;
    var $operation;
    var $value;
    var $operator2;


function ProductionMod ($id=0, $targetClassId='', $targetId=0, $resourceId=0, $operation='*', $value=0, $operator2=null) {
    
    $this->id = $id;
    $this->targetClassId = $targetClassId;
    $this->targetId = $targetId;
    $this->resourceId = $resourceId;
    $this->operation = $operation;
    $this->value = $value;
    $this->operator2 = $operator2;

    return $this;
}

public function getId() {
    return $this->id;
}

public function getTargetClassId() {
    return $this->targetClassId;
}

public function getTargetId() {
    return $this->targetId;
}

public function getResourceId() {
    return $this->resourceId;
}

public function getOperation() {
    return $this->operation;
}

public function getValue() {
    return $this->value;
}

public function getOperator2() {
    return $this->operator2;
}

}
?>