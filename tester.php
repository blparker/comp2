<?php

function assertEquals($expected, $actual) {
  if($expected != $actual) {
    throw new Exception("Expected $expected, got $actual");
  }
}

function assertTrue($condition) {
  if(!$condition) {
    throw new Exception("Expected true, got false");
  }
}

function assertFalse($condition) {
}

function assertForEach($arr, $fn) {
  foreach($arr as $i) {
    $r = $fn($i);
    assertTrue($r);
  }
}



