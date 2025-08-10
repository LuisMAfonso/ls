<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

//print_r($post[avatar]);

if ( $t == 'f' ) {

    $hash = password_hash(trim($post[password]), PASSWORD_DEFAULT); 

    if ( $post[id] == 0 ) {

    	$sql = "INSERT INTO users (UserID, Pass, hashPass, UserName, EMail, birthday, avatarId, staffId, DateOpen)
    			VALUES ('".$post[userId]."', '".$post[password]."', '".$hash."', '".$post[name]."', '".$post[email]."', '".$post[birthday]."', '".$post[avatar][id]."', '".$post[staffId]."', getdate() ) ";
    	$db->debug=1;
    	$rows_emps = $db->Execute($sql);
    } else {

        $sql = "UPDATE users 
                SET UserName = '".$post[name]."', 
                    EMail    = '".$post[email]."', 
                    birthday = '".$post[birthday]."', 
                    staffId  = '".$post[staffId]."', 
                    avatarId = '".$post[avatar][id]."' 
                WHERE Id = ".$post[id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);    
    }
}
if ( $t == 'pw' ) {

    $sql = "SELECT Id, hashPass
            FROM Users 
            where DateClose is null and UserId=? ";
//    $db->debug=1;
    $rows = $db->Execute($sql, [$_SESSION['userId']]);

    $verify = password_verify(trim($post[actualPW]), $rows->fields[1]); 
//  echo $verify.' : '.$post[actualPW].' -> '.$rows->fields[1];

    $dataJson = '';
    $dataJson->id = 0;

    if ( $verify ) {
        $dataJson->id = $rows->fields[0];
        $dataJson->origOK = 1;
        $dataJson->newMatch = 0;

        if ( $post[newPW1] == $post[newPW2] ) {
            $dataJson->newMatch = 1;

            $hash = password_hash(trim($post[newPW1]), PASSWORD_DEFAULT); 
            $sql2 = "UPDATE users 
                     SET hashPass = '".$hash."' 
                     WHERE Id = ".$rows->fields[0];
            $rows_emps2 = $db->Execute($sql2);    
        } 
    } else {
        $dataJson->id = $rows->fields[0];        
        $dataJson->origOK = 0;
        $dataJson->newMatch = 0;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
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

