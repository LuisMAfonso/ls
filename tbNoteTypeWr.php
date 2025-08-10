<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

//print_r($post[avatar]);

if ( $t == 'f' && $post[Id] == 0 ) {
		$sql = "INSERT INTO tblNotesTypes ( notesTypeName, notesTypeIcon )
				VALUES ('".$post[descript]."', '".$post[icon]."' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $post[Id] != 0 ) {
        $sql = "UPDATE tblNotesTypes 
                SET notesTypeName = '".$post[descript]."', 
                    notesTypeIcon = '".$post[icon]."'
                WHERE notesTypeId = ".$post[Id];
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

