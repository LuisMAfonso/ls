<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['g'] )) { $g = $_GET['g'];  } else { $g = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

//print_r($post[avatar]);

$vn = 0;
if ( $v == 'true' ) $vn = 1;

if ( $t == 'opt' ) {

    $sql = "SELECT count(*) 
            FROM UsersGroupsMenu
            WHERE OptId = $r AND UGroupId = $g ";
    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    if ( $rows_emps->fields[0] == 0 ) {
        $sql = "INSERT INTO UsersGroupsMenu ( UGroupId, OptId )
                VALUES ( '$g', '$r') ";
        $rows_emps = $db->Execute($sql);
    }

	$sql = "UPDATE UsersGroupsMenu
            SET $c = '$vn' 
            WHERE OptId = $r AND UGroupId = $g ";

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

