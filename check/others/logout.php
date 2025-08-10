<?php
include( 'include.php' );
require_once('header.php');

if( isset( $_SESSION['userCode'] )  ) {
	unset($_SESSION["userCode"]);
	session_unset();
	//session_destroy();
}
/*echo '<script>
setTimeout(function(){
	window.location = "/birent3/index.php";
}, 500);</script>';*/
echo '<script>
	window.location = "/birent3/";
</script>';


?> 