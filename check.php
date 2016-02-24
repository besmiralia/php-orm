<?php
function is_loged(){
	if(isset($_SESSION['userid'])){return 1;}
	
	return 0;
}

if(!is_loged())Header("Location:index.php");

?>