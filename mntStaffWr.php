<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $r == 0 ) {
		$sql = "INSERT INTO staff (staffNumber, staffName, staffAddress, staffZipcode, staffCity, staffCountry, staffEmail, staffPhone, staffVatNumber, staffIDnumber, staffDriveLicense, staffPosition, staffAvatarId, staffReportTo, isDeleted, createStamp, createUser)
				VALUES ('".$post[staffNumber]."', '".$post[staffName]."', '".$post[staffAddress]."', '".$post[staffZipcode]."', '".$post[staffCity]."', '".$post[staffCountryId]."', '".$post[staffEmail]."', '".$post[staffPhone]."', '".$post[staffVatNumber]."', '".$post[staffIDnumber]."', '".$post[staffDriveLicense]."', '".$post[staffPositionId]."', '".$post[staffAvatar][id]."', '".$post[staffReportToId]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
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
if ( $t == 'd' ) {
    $sql = "UPDATE staff 
                SET isDeleted = 1, 
                    deleteStamp = getdate(),
                    deleteUser  = '$user'
            WHERE staffId = ".$r;
    $db->debug=1;
    $rows_emps = $db->Execute($sql);
}

if ( $t == 'r' && $r == 0 ) {
        $sql = "UPDATE staffPrices
                SET dtTo = '".$post[dtFrom]."'
                FROM staffPrices
                where staffId = '$s' and '".$post[dtFrom]."' between dtFrom and dtTo";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

        $sql = "INSERT INTO staffPrices (staffId, dtFrom, dtTo, hourCost, hourRate, isDeleted, createStamp, createUser)
                VALUES ('".$s."', '".$post[dtFrom]."', '".$post[dtTo]."', '".$post[hourCost]."', '".$post[hourRate]."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

}
if ( $t == 'r' && $r != 0 ) {
        $sql = "UPDATE staffPrices
                SET dtFrom   = '".$post[dtFrom]."',
                    dtTo     = '".$post[dtTo]."',
                    hourCost = '".$post[hourCost]."',
                    hourRate = '".$post[hourRate]."',
                    modifyStamp = getdate(),
                    modifyUser  = '$user'
                FROM staffPrices
                where rateId = '$r' ";
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

