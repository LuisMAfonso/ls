<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };
if ( isset( $_GET['w'] )) { $w = $_GET['w'];  } else { $w = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['g'] )) { $g = $_GET['g'];  } else { $g = ''; };



if ( $t == 'f') {
	$db->debug=1;
	// print_r($data);
	
	$GroupId = $g;
	$GroupName = $c;
 
	$sql = "SELECT Id, UGroup FROM [BIrent].[dbo].UsersUGroup  
	WHERE UGroup = '".$GroupName."' ";
	
	$rows_emps = $db->Execute($sql);

	if( $rows_emps->rowCount() == 0 ){
		$sql2 = "INSERT INTO [BIrent].[dbo].UsersUGroup
			([Ugroup]) VALUES ('".$GroupName."')";

		$rows_emps2 = $db->Execute($sql2);
		$rows_emps2->Close();
		
    } else {
		// $sql3 = "UPDATE [BIrent].[dbo].UsersUGroup,
		// SET	
		// Ugroup = '".$GroupName."' 
		// WHERE Ugroup = '".$GroupName."' ";
	
		// $rows_emps3 = $db->Execute($sql3);
		// $rows_emps3->Close();
	}
	$rows_emps->Close();
 }