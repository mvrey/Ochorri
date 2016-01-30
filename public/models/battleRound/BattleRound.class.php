<?php

class BattleRound {

var $id;
var $battleId;
var $roundId;
var $attackLog;
var $defendLog;

function BattleRound($id=0, $battleId=0, $roundId=0, $attackLog='', $defendLog='') {

    $this->id=$id;
    $this->battleId=$battleId;
    $this->roundId=$roundId;
    $this->attackLog=$attackLog;
    $this->defendLog=$defendLog;

    return $this;
}

public function getId() {
    return ($this->id);
}

public function getBattleId() {
    return ($this->battleId);
}

public function getRoundId() {
    return ($this->roundId);
}

public function getAttackLog() {
    return ($this->attackLog);
}

public function setAttackLog($value) {
    $this->attackLog = $value;
}

public function getDefendLog() {
    return ($this->defendLog);
}

public function setDefendLog($value) {
    $this->defendLog = $value;
}

}
?>
