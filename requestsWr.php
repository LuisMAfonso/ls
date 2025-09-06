<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $r == 0 ) {

    $createDt = date("Y-m-d H:i:s");

		$sql = "INSERT INTO Request (reqDate, projId, suppId, reqType, subfamId, reqStatus, reqNotes, createStamp, createUser)
				VALUES ('".$post[reqDate]."', '".$post[projId]."', '".$post[suppId]."', '".$post[reqType]."', '".$post[sfamId]."', '1', '".$post[reqNote]."', '".$createDt."', '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);

        $sql = "INSERT INTO RequestStatusLog (reqId, rslStatusId, rslDate, rslNotes, isDeleted, createStamp, createUser) 
                SELECT reqId, reqStatus, reqdate, '', 0, createStamp, createUser
                FROM Request rq
                WHERE rq.createStamp = '$createDt'";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE Request 
                    SET reqDate     = '".$post[reqDate]."', 
                        projId      = '".$post[projId]."', 
                        suppId      = '".$post[suppId]."', 
                        reqType     = '".$post[reqType]."', 
                        subfamId    = '".$post[sfamId]."', 
                        reqNotes     = '".$post[reqNote]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE reqId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'rqDetS' && $post[reqsId] == 0 ) {

    $createDt = date("Y-m-d H:i:s");

        $sql = "INSERT INTO RequestServices (reqId, reqsCode, reqsName, reqsQuant, reqsPrice, reqsNotes, reqsSfamId, createStamp, createUser)
                VALUES ('".$r."', '".$post[reqsCode]."', '".$post[reqsName]."', '".$post[reqsQuant]."', '".$post[reqsPrice]."', '".$post[reqsNotes]."', '".$post[reqsSfamId]."', '".$createDt."', '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

}
if ( $t == 'rqDetS' && $post[reqsId] != 0 ) {
        $sql = "UPDATE RequestServices 
                    SET reqsCode    = '".$post[reqsCode]."', 
                        reqsName    = '".$post[reqsName]."', 
                        reqsQuant   = '".$post[reqsQuant]."', 
                        reqsPrice   = '".$post[reqsPrice]."', 
                        reqsSfamId  = '".$post[reqsSfamId]."', 
                        reqsNotes   = '".$post[reqsNotes]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE reqId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'rqDetP' && $post[reqpId] == 0 ) {

    $createDt = date("Y-m-d H:i:s");

        $sql = "INSERT INTO RequestProducts (reqId, reqpArticle, reqpName, reqpQuant, reqpPrice, reqpNotes, reqpSfamId, createStamp, createUser)
                VALUES ('".$r."', '".$post[reqpArticle]."', '".$post[reqpName]."', '".$post[reqpQuant]."', '".$post[reqpPrice]."', '".$post[reqpNotes]."', '".$post[reqpSfamId]."', '".$createDt."', '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

}
if ( $t == 'rqDetP' && $post[reqpId] != 0 ) {
        $sql = "UPDATE RequestProducts 
                    SET reqpArticle    = '".$post[reqpArticle]."', 
                        reqpName    = '".$post[reqpName]."', 
                        reqpQuant   = '".$post[reqpQuant]."', 
                        reqpPrice   = '".$post[reqpPrice]."', 
                        reqpSfamId  = '".$post[reqpSfamId]."', 
                        reqpNotes   = '".$post[reqpNotes]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE reqId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'del' ) {
        $sql = "UPDATE Request 
                SET isDeleted = 1, deleteStamp = getdate(), deleteUser = '$user'
                WHERE reqId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'dels' ) {
        $sql = "UPDATE RequestServices 
                SET isDeleted = 1, deleteStamp = getdate(), deleteUser = '$user'
                WHERE reqsId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'delp' ) {
        $sql = "UPDATE RequestProducts 
                SET isDeleted = 1, deleteStamp = getdate(), deleteUser = '$user'
                WHERE reqpId = ".$r;
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

