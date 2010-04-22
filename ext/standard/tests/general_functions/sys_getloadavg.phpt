--TEST--
sys_getloadavg() tests
--SKIPIF--
<?php
if (!function_exists("sys_getloadavg")) die("skip");
?>
--FILE--
<?php

var_dump(sys_getloadavg(""));
var_dump(sys_getloadavg());

echo "Done\n";
?>
--EXPECTF--	
array(3) {
  [0]=>
  float(%f)
  [1]=>
  float(%f)
  [2]=>
  float(%f)
}

Warning: sys_getloadavg() expects exactly 0 parameters, %d given in %s 
Done
