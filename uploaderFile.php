<?php
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['f'] )) { $f = $_GET['f'];  } else { $f = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = ''; };

/*

HTML5/FLASH MODE

(MODE will detected on client side automaticaly. Working mode will passed to server as GET param "mode")

response format

if upload was good, you need to specify state=true and name - will passed in form.send() as serverName param
{state: 'true', name: 'filename'}

*/
$user = $_SESSION['userId'];

$target_dir = "c:\\data\\ls\\";
if ( $t == 'est' ) $target_dir = "c:\\data\\ls\\estimate\\";

$path_parts = pathinfo(basename($_FILES["file"]["name"]));
$ext = $path_parts['extension'];

//$id = $_POST[file_id];
$target_file = $target_dir . $id.'.'.$ext;

$sql = "INSERT INTO DataImpFiles (origin, [filename], fileext, impDate, impUserID)
		VALUES ('est', '$id', '$ext', getdate(), '$user' )";
//$db->debug=1;
$rows_emps = $db->Execute($sql);		

move_uploaded_file($_FILES['file']['tmp_name'], $target_file);
//$param = $_POST[file_id]. ' -> '.$_FILES['file']['tmp_name'];
//$sql = " INSERT INTO lsMessage (message) VALUES ( '".$param."' ) ";
//$rows_emps = $db->Execute($sql);


if (@$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
	$filename = $_FILES["file"]["name"];

	

	print_r("{state: true, name:'".str_replace("'","\\'",$filename)."'}");

}


?>
