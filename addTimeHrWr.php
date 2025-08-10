<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $post[Id] == 0 ) {

    $wTmFrom = $post[TmFrom];
    if ( strlen($post[TmFrom]) == 4 ) {
        $wTmFrom = substr($post[TmFrom],0,2).":".substr($post[TmFrom],2,2);
    }
    $wTmTo = $post[TmTo];
    if ( strlen($post[TmTo]) == 4 ) {
        $wTmTo = substr($post[TmTo],0,2).":".substr($post[TmTo],2,2);
    }
    $wTmBreak = $post[TmBreak];
    if ( strlen($post[TmBreak]) == 4 ) {
        $wTmBreak = substr($post[TmBreak],0,2).":".substr($post[TmBreak],2,2);
    }
    $timeFrom  = $post[date].' '.$wTmFrom;
    $timeTo    = $post[date].' '.$wTmTo;
    $timeBreak = $wTmBreak;

		$sql = "INSERT INTO staffTime (StaffId, projId, stfTmFrom, stfTmTo, timeBreak, workDone, isDeleted, createStamp, createUser)
				VALUES ('".$s."', '".$post[projId]."', '".$timeFrom."', '".$timeTo."', '".$timeBreak."', '".$post[workDone]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $post[Id] > 0 ) {
    $wTmFrom = $post[TmFrom];
    if ( strlen($post[TmFrom]) == 4 ) {
        $wTmFrom = substr($post[TmFrom],0,2).":".substr($post[TmFrom],2,2);
    }
    $wTmTo = $post[TmTo];
    if ( strlen($post[TmTo]) == 4 ) {
        $wTmTo = substr($post[TmTo],0,2).":".substr($post[TmTo],2,2);
    }
    $wTmBreak = $post[TmBreak];
    if ( strlen($post[TmBreak]) == 4 ) {
        $wTmBreak = substr($post[TmBreak],0,2).":".substr($post[TmBreak],2,2);
    }
    $timeFrom  = $post[date].' '.$wTmFrom;
    $timeTo    = $post[date].' '.$wTmTo;
    $timeBreak = $wTmBreak;

        $sql = "UPDATE staffTime 
                    SET projId      = '".$post[projId]."', 
                        stfTmFrom   = '".$timeFrom."', 
                        stfTmTo     = '".$timeTo."', 
                        timeBreak   = '".$timeBreak."', 
                        workDone    = '".$post[workDone]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE stfTmId = ".$post[Id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'd' ) {
    $sql = "UPDATE staffTime 
                SET isDeleted = 1, 
                    deleteStamp = getdate(),
                    deleteUser  = '$user'
            WHERE stfTmId = ".$r;
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

