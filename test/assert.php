<?php

function assertEquals($str, $expected, $actual = null) {
  if($actual === null) {
    $actual = $expected;
    $expected = $str;
    
    $expectedStr = $expected;
    $actualStr = $actual;
    if(is_bool($expected)) {
      $expectedStr = _boolString($expected);
    }
    if(is_bool($actual)) {
      $actualStr = _boolString($actual);
    }

    $str = "Expected $expectedStr, got $actualStr";

    _assertEquals($str, $expected, $actual);
  }
  else {
    _assertEquals($str, $expected, $actual);
  }
}

function _assertEquals($str, $expected, $actual) {
  if($expected != $actual) {
    throw new Exception($str);
  }
}

function assertTrue($condition) {
  assertEquals("Expected true, got false", true, $condition);
}

function assertFalse($str, $condition) {
  if($condition === null) {
    $condition = $str;
    $str = "Expected false, got true";
  }
  assertEquals($str, true, $condition);
}

function assertForEach($arr, $fn) {
  foreach($arr as $i) {
    $r = $fn($i);
    assertTrue($r);
  }
}

function assertNotNull($obj) {
  assertFalse("Expected not null, got null", !is_null($obj));
}

function _boolString($in) {
  return ($in) ? 'true' : 'false';
}

