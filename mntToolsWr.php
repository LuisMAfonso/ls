<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $r == 0 ) {
		$sql = "INSERT INTO rsTools (toolName, toolSerialNumber, toolLabelCode, toolEquipmentTypeId, toolTypeId, toolWeight, isDeleted, createStamp, createUser)
				VALUES ('".str_replace("'","''",$post[toolName])."', '".$post[toolSerialNumber]."', '".$post[toolLabelCode]."', '".$post[toolEquipmentTypeId]."', '".$post[toolTypeId]."', '".$post[toolWeight]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE rsTools 
                    SET toolName            = '".str_replace("'","''",$post[toolName])."', 
                        toolSerialNumber    = '".$post[toolSerialNumber]."', 
                        toolLabelCode       = '".$post[toolLabelCode]."', 
                        toolEquipmentTypeId = '".$post[toolEquipmentTypeId]."', 
                        toolTypeId          = '".$post[toolTypeId]."',
                        toolWeight          = '".$post[toolWeight]."',
                        modifyStamp         = getdate(),
                        modifyUser          = '$user'
                WHERE toolId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fp' && $r == 0 ) {
        $sql = "INSERT INTO rsToolsPrices ( toolId, dtFrom, dtTo, amount, isDeleted, createStamp, createUser )
                VALUES ('".$p."', '".$post[dtFrom]."', '".$post[dtTo]."', '".$post[amount]."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fp' && $r != 0 ) {
        $sql = "UPDATE rsToolsPrices 
                    SET dtFrom      = '".$post[dtFrom]."', 
                        dtTo        = '".$post[dtTo]."', 
                        amount      = '".$post[amount]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE toolPrcId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

function getPost()
{
    if(!empty($_POST))
    {
        // when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request
        // NOTE: if this is the case and $_POST is empty, check the variables_order in php.ini! - it must contain the letter P
        return $_POST;
    }

    // when using application/json as the HTTP Content-Type in the request 
    $post = json_decode(file_get_contents('php://input'), true);
    if(json_last_error() == JSON_ERROR_NONE)
    {
        return $post;
    }

    return [];
}
?>

