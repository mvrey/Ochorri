<?php

class Message {

var $id;
var $from;
var $to;
var $subject;
var $content;
var $date;
var $read;


function Message ($id, $from, $to, $subject, $content, $date, $read) {

    $this->id=$id;
    $this->from=$from;
    $this->to=$to;
    $this->subject=$subject;
    $this->content=$content;
    $this->date=$date;
    $this->read=$read;

    return $this;
}

public function getId() {
    return ($this->id);
}

public function getFrom() {
    return ($this->from);
}

public function getTo() {
    return ($this->To);
}

public function getSubject() {
    return ($this->subject);
}

public function getContent() {
    return ($this->content);
}

public function getDate() {
    return ($this->date);
}

public function getRead() {
    return ($this->read);
}

public function setRead($value) {
    $this->read = $value;
}
}
?>