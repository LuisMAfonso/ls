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
		$sql = "INSERT INTO customers (custName, custAddress, custZipcode, custCity, custCountry, custEmail, custPhone, custTypeId, custBusActId, vatNumber, isDeleted, createStamp, createUser)
				VALUES ('".$post[custName]."', '".$post[custAddress]."', '".$post[custZipcode]."', '".$post[custCity]."', '".$post[custCountry]."', '".$post[custEmail]."', '".$post[custPhone]."', '".$post[custTypeId]."', '".$post[custBusActId]."', '".$post[vatNumber]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE customers 
                    SET custName     = '".$post[custName]."', 
                        custAddress  = '".$post[custAddress]."', 
                        custZipcode  = '".$post[custZipcode]."', 
                        custCity     = '".$post[custCity]."', 
                        custCountry  = '".$post[custCountry]."', 
                        custEmail    = '".$post[custEmail]."', 
                        custPhone    = '".$post[custPhone]."', 
                        custTypeId   = '".$post[custTypeId]."',
                        custBusActId = '".$post[custBusActId]."',
                        vatNumber    = '".$post[vatNumber]."',
                        modifyStamp  = getdate(),
                        modifyUser   = '$user'
                WHERE custId = ".$r;
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

