<?php
mysql_pconnect($TheServer,$TheUser,$ThePassword)
or die(mysql_error());
mysql_select_db($TheDatabase)
or die (mysql_error());

?>
