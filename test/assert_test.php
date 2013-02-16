<?php

require_once('assert.php');

// assertEquals pass
assertEquals(1, 1);
assertEquals(true, true);
assertEquals(false, false);
assertEquals("biz", "biz");
assertEquals('biz', "biz");
assertEquals(array(1, 2), array(1, 2));

// assertEquals fail
try { assertEquals(1, 2); 
  throw new Exception("*** Shouldn't see me");
} catch(Exception $ex) {};

try { assertEquals(true, false); 
  throw new Exception("*** Shouldn't see me");
} catch(Exception $ex) {};

try { assertEquals("foo", "bar"); 
  throw new Exception("*** Shouldn't see me");
} catch(Exception $ex) {};

try { assertEquals(array(1, 2), array(3, 4)); 
  throw new Exception("*** Shouldn't see me");
} catch(Exception $ex) {};

// assertTrue pass
assertTrue(is_string('foo'));
assertTrue(true);
assertTrue(gettype("foo") === "string");


