<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();

$user = $_SESSION['userId'];

if ( $t == 'exp' && $post[Id] == 0 ) {
        $sql = "INSERT INTO expenses (staffId, expDate, expType, expProj, expValue, expNotes, avatarId, isDeleted, createStamp, createUser)
                VALUES ('".$post[staffId]."', '".$post[expDate]."', '".$post[expId]."', '".$post[projId]."', '".$post[expValue]."', '".$post[workDone]."', '".$post[impFile][0][id]."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'exp' && $post[Id] != 0 ) {
        $sql = "UPDATE expenses 
                SET staffId = '".$post[staffId]."', 
                    expDate = '".$post[expDate]."', 
                    expType = '".$post[expId]."', 
                    expProj = '".$post[projId]."', 
                    expValue = '".$post[expValue]."', 
                    expNotes = '".$post[workDone]."', 
                    avatarId = '".$post[impFile][0][id]."', 
                    modifyStamp = getdate(), 
                    modifyUser = '$user' 
                WHERE expId = ".$post[Id];
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

