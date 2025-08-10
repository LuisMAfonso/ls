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
		$sql = "INSERT INTO suppliers (suppName, suppAddress, suppZipcode, suppCity, suppCountry, suppEmail, suppPhone, suppTypeId, suppBusActId, vatNumber, isDeleted, createStamp, createUser)
				VALUES ('".$post[suppName]."', '".$post[suppAddress]."', '".$post[suppZipcode]."', '".$post[suppCity]."', '".$post[suppCountry]."', '".$post[suppEmail]."', '".$post[suppPhone]."', '".$post[suppTypeId]."', '".$post[suppBusActId]."', '".$post[vatNumber]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE suppliers 
                    SET suppName     = '".$post[suppName]."', 
                        suppAddress  = '".$post[suppAddress]."', 
                        suppZipcode  = '".$post[suppZipcode]."', 
                        suppCity     = '".$post[suppCity]."', 
                        suppCountry  = '".$post[suppCountry]."', 
                        suppEmail    = '".$post[suppEmail]."', 
                        suppPhone    = '".$post[suppPhone]."', 
                        suppTypeId   = '".$post[suppTypeId]."',
                        suppBusActId = '".$post[suppBusActId]."',
                        vatNumber    = '".$post[vatNumber]."',
                        modifyStamp  = getdate(),
                        modifyUser   = '$user'
                WHERE suppId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'sup' && $r == 0 ) {
        $sql = "INSERT INTO suppliersSupply (suppId, sfamId)
                VALUES ('".$s."', '".$post[sfamId]."' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'sup' && $r != 0 ) {
        $sql = "UPDATE suppliersSupply 
                    SET sfamId     = '".$post[sfamId]."'
                WHERE Id = ".$r;
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

