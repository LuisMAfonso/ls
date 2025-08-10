<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['l'] )) { $l = $_GET['l'];  } else { $l = ''; };
if ( isset( $_GET['e'] )) { $e = $_GET['e'];  } else { $e = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $r == 0 ) {
		$sql = "INSERT INTO impPacks (ipCode, ipDesig, ipType, ipUnitId, isDeleted, createStamp, createUser)
				VALUES ('".$post[bkCode]."', '".$post[bkDesig]."', '".$post[bkType]."', '".$post[bkUnit]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE impPacks 
                    SET ipCode    = '".$post[bkCode]."', 
                        ipDesig   = '".$post[bkDesig]."', 
                        ipType    = '".$post[bkType]."', 
                        ipUnitId  = '".$post[bkUnit]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE ipId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'd' ) {

    $sql = "SELECT famId FROM tblFamily WHERE famType = '$l' ";
    $rows_emps = $db->Execute($sql);
    $fam = $rows_emps->fields[0];

    if ( $post[id] == 0 ) {

        $sql = "INSERT INTO impPacksDetail ( ipId, ipdFam, ipdCodeId, ipdDesig, ipdQuant )
                VALUES ('".$e."', '".$fam."', '".$post[estReference]."', '".$post[estDesign]."', '".$post[estQuant]."' ) ";        
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE impPacksDetail 
                SET ipdQuant = '".$post[estQuant]."',
                    ipdDesig = '".$post[estDesign]."' 
                WHERE ipdId = ".$post[id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'p' ) {

    $sql = "SELECT famId FROM tblFamily WHERE famType = '$l' ";
    $rows_emps = $db->Execute($sql);
    $fam = $rows_emps->fields[0];

    if ( $post[id] == 0 ) {

        $sql = "INSERT INTO impPacksDetail ( ipId, ipdFam, ipdCodeId, ipdDesig, ipdQuant )
                VALUES ('".$e."', '".$fam."', '".$post[estCode]."', '".$post[estDesign]."', '".$post[estQuant]."' ) ";        
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE impPacksDetail 
                SET ipdQuant = '".$post[estQuant]."',
                    ipdDesig = '".$post[estDesign]."' 
                WHERE ipdId = ".$post[id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'del' ) {
    $sql = "DELETE FROM impPacksDetail WHERE ipdId = $id ";
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

