--TEST--
Bug #68077 (LOAD DATA LOCAL INFILE / open_basedir restriction)
--SKIPIF--
<?php
require_once('skipif.inc');
require_once('skipifconnectfailure.inc');
if (!$IS_MYSQLND) {
	die("skip: test applies only to mysqlnd");
}
?>
--INI--
open_basedir={PWD}
--FILE--
<?php
	require_once("connect.inc");

	if (!$link = my_mysqli_connect($host, $user, $passwd, $db, $port, $socket)) {
		printf("[001] Connect failed, [%d] %s\n", mysqli_connect_errno(), mysqli_connect_error());
	}

	if (!$link->query("DROP TABLE IF EXISTS test")) {
		printf("[002] [%d] %s\n", $link->errno, $link->error);
	}

	if (!$link->query("CREATE TABLE test (dump1 INT UNSIGNED NOT NULL PRIMARY KEY) ENGINE=" . $engine)) {
		printf("[003] [%d] %s\n", $link->errno, $link->error);
	}

	if (FALSE == file_put_contents(__DIR__ . '/bug53503.data', "1\n2\n3\n"))
		printf("[004] Failed to create CVS file\n");

	if (!$link->query("SELECT 1 FROM DUAL"))
		printf("[005] [%d] %s\n", $link->errno, $link->error);

	if (!$link->query("LOAD DATA LOCAL INFILE '" . __DIR__  . "/bug53503.data' INTO TABLE test")) {
		printf("[006] [%d] %s\n", $link->errno, $link->error);
		echo "bug\n";
	} else {
		echo "done\n";
	}

	if (!$link->query("LOAD DATA LOCAL INFILE '../../bug53503.data' INTO TABLE test")) {
		printf("[006] [%d] %s\n", $link->errno, $link->error);
		echo "done\n";
	} else {
		echo "bug\n";
	}
	$link->close();
?>
--CLEAN--
<?php
require_once('connect.inc');

if (!$link = my_mysqli_connect($host, $user, $passwd, $db, $port, $socket)) {
	printf("[clean] Cannot connect to the server using host=%s, user=%s, passwd=***, dbname=%s, port=%s, socket=%s\n",
		$host, $user, $db, $port, $socket);
}

if (!$link->query($link, 'DROP TABLE IF EXISTS test')) {
	printf("[clean] Failed to drop old test table: [%d] %s\n", mysqli_errno($link), mysqli_error($link));
}

$link->close();

unlink('bug53503.data');
?>
--EXPECTF--
done
[006] [2000] open_basedir restriction in effect. Unable to open file
done
