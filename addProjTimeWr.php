<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $post[Id] == 0 ) {
    $timeFrom  = $post[date].' '.$post[TmFrom];
    $timeTo    = $post[date].' '.$post[TmTo];
    $timeBreak = $post[TmBreak];

		$sql = "INSERT INTO staffTime (StaffId, projId, stfTmFrom, stfTmTo, timeBreak, isDeleted, createStamp, createUser)
				VALUES ('".$post[staffId]."', '".$p."', '".$timeFrom."', '".$timeTo."', '".$timeBreak."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $post[Id] != 0 ) {
    $timeFrom  = $post[date].' '.$post[TmFrom];
    $timeTo    = $post[date].' '.$post[TmTo];
    $timeBreak = $post[TmBreak];

        $sql = "UPDATE staffTime 
                    SET staffId        = '".$post[staffId]."', 
                        stfTmFrom      = '".$timeFrom."', 
                        stfTmTo        = '".$timeTo."', 
                        timeBreak      = '".$timeBreak."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE stfTmId = ".$post[Id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'mWr' ) {

//    echo print_r($post);
//    var_dump(json_decode($post));
    for ( $i=0;$i<count($post);$i++) {

        $timeFrom  = $d.' '.$post[$i]['tmFrom'];
        $timeTo    = $d.' '.$post[$i]['tmTo'];
        $timeBreak = $post[$i]['tmBreak'];
        $workDone  = $post[$i]['wd'];

//        echo $post[$i]['id']." - ".$post[$i]['tmFrom']." - ".$post[$i]['tmTo'].'<br>';
        $sql = "INSERT INTO staffTime (StaffId, projId, stfTmFrom, stfTmTo, timeBreak, workDone, isDeleted, createStamp, createUser)
                VALUES ('".$post[$i]['id']."', '".$p."', '".$timeFrom."', '".$timeTo."', '".$timeBreak."', '".$workDone."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }

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

