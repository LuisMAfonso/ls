<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['o'] )) { $o = $_GET['o'];  } else { $o = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $r == 0 ) {
		$sql = "INSERT INTO contacts (contName, contAddress, contZipcode, contCity, contCountry, contEmail, contPhone, contFrom, isDeleted, createStamp, createUser)
				VALUES ('".$post[contName]."', '".$post[contAddress]."', '".$post[contZipcode]."', '".$post[contCity]."', '".$post[contCountry]."', '".$post[contEmail]."', '".$post[contPhone]."', 'ct', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE contacts 
                    SET contName    = '".$post[contName]."', 
                        contAddress = '".$post[contAddress]."', 
                        contZipcode = '".$post[contZipcode]."', 
                        contCity    = '".$post[contCity]."', 
                        contCountry = '".$post[contCountry]."', 
                        contEmail   = '".$post[contEmail]."', 
                        contPhone   = '".$post[contPhone]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE contId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'cnt' && $post[Id] != 0 ) {
        $sql = "UPDATE contactsFrom 
                SET contId = '".$post[contId]."', 
                    roleId = '".$post[roleId]."'
                WHERE cfId =  $post[Id]";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'cnt' && $post[Id] == 0 ) {
        $sql = "INSERT INTO contactsFrom (contId, contFrom, fromId, roleId, isDeleted, createStamp, createUser)
                VALUES ('".$post[contId]."', '".$o."', '".$s."', '".$post[roleId]."', 0, getdate(), '$user' ) ";
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

