<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

//print_r($post[avatar]);

if ( $t == 'f' && $post[Id] == 0 ) {
        $sql = "INSERT INTO tblProdSize ( sizeCode, sizeDesig )
                VALUES ('".$post[code]."', '".$post[descript]."' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $post[Id] != 0 ) {
        $sql = "UPDATE tblProdSize 
                SET sizeCode  = '".$post[code]."', 
                    sizeDesig = '".$post[descript]."'
                WHERE sizeId = ".$post[Id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'fp' && $post[pspId] == 0 ) {
        $sql = "INSERT INTO tblProdSizePrices ( sizeId, dtFrom, dtTo, pspAmount, pspQuant, pspUnit )
                VALUES ('$v', '".$post[dtFrom]."', '".$post[dtTo]."', '".$post[pspAmount]."', '".$post[pspQuant]."', '".$post[pspUnit]."' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fp' && $post[pspId] != 0 ) {
        $sql = "UPDATE tblProdSizePrices 
                SET dtFrom    = '".$post[dtFrom]."', 
                    dtTo      = '".$post[dtTo]."', 
                    pspAmount = '".$post[pspAmount]."', 
                    pspQuant  = '".$post[pspQuant]."', 
                    pspUnit   = '".$post[pspUnit]."'
                WHERE pspId  = ".$post[pspId];
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

