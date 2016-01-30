<?php

class Term {

    public static $lang;
    var $id;
    var $string;


function Term($id=0, $string='') {

    $this->id=$id;
    $this->string=$string;

    return $this;
}

public function getId(){
    return $this->id;
}

public function getLang(){
    return Term::lang;
}

public static function setLang($value){
    Term::$lang = $value;
}

public function getString(){
    return $this->string;
}

}

?>
