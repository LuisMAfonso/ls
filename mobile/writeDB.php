<?php
include( '../include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = 0; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = 0; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

$staffId = 0;
$sql = "SELECT staffId FROM Users WHERE userId = '$user' ";
$rows_emps = $db->Execute($sql);
if (!$rows_emps->EOF) $staffId = $rows_emps->fields[0];


if ( $t == 'exp' && $r == 0 ) {
        $sql = "INSERT INTO expenses (staffId, expDate, expType, expProj, expValue, expNotes, avatarId, isDeleted, createStamp, createUser)
                VALUES ('".$staffId."', '".$post[expDate]."', '".$post[expId]."', '".$post[projId]."', '".$post[expValue]."', '".$post[wordDone]."', '".$post[avatar][id]."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fi' && $r != 0 ) {
        $sql = "UPDATE staff 
                    SET staffNumber        = '".$post[staffNumber]."', 
                        staffName          = '".$post[staffName]."', 
                        staffAddress       = '".$post[staffAddress]."', 
                        staffZipcode       = '".$post[staffZipcode]."', 
                        staffCity          = '".$post[staffCity]."', 
                        staffCountry       = '".$post[staffCountryId]."', 
                        staffEmail         = '".$post[staffEmail]."', 
                        staffPhone         = '".$post[staffPhone]."', 
                        staffVatNumber     = '".$post[staffVatNumber]."',
                        staffIDnumber      = '".$post[staffIDnumber]."',
                        staffDriveLicense  = '".$post[staffDriveLicense]."',
                        staffPosition      = '".$post[staffPositionId]."',
                        staffAvatarId      = '".$post[staffAvatar][id]."',
                        staffReportTo      = '".$post[staffReportToId]."',
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE staffId = ".$r;
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

