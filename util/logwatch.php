<?php

class LogWatch {
  private $obj;

  public function __construct($class) {
    $this->obj = $class;
  }

  function __call($method, $args) {
  }
}

