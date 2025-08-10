<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['f'] )) { $f = $_GET['f'];  } else { $f = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

//print_r($post[avatar]);

if ( $t == 'f' && $post[Id] == 0 ) {
		$sql = "INSERT INTO tblSubFamily (famId, sfamName, sfamIcon)
				VALUES ('".$f."', '".$post[sfamName]."', '".$post[sfamIcon]."' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $post[Id] > 0 ) {
        $sql = "UPDATE tblSubFamily 
                SET sfamName = '".$post[sfamName]."',
                    sfamIcon = '".$post[sfamIcon]."'
                WHERE sfamId = ".$post[Id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'del' ) {
       $sql = "UPDATE tblSubFamily 
                SET isDeleted = 1, deleteStamp = getdate()
                WHERE sfamId = ".$r;
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

