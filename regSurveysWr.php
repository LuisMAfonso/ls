<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();

$user = $_SESSION['userId'];

if ( $t == 'surv' ) {

    if ( $post[Id] == 0 ) {
        $createDate = date("Y-m-d H:i:s");

        $sql = "INSERT INTO projectsSurveys (survProj, survDate, isDeleted, createStamp, createUser)
                VALUES ('".$post[survProj]."', '".$post[survDate]."', 0, '$createDate', '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

        $sql = "SELECT survId 
                FROM projectsSurveys 
                WHERE createStamp = '$createDate' AND survProj = '".$post[survProj]."' ";
        $rows_emps = $db->Execute($sql);

        $survId = $rows_emps->fields[0];
    } else {
        $survId = $post[Id];
    }

    $sql = "UPDATE projectsSurveys SET
               [sizeArea]      = '".$post[sizeArea]."'
              ,[locAddress]    = '".$post[locAddress]."'
              ,[projType]      = '".$post[projType]."'
              ,[street]        = '".$post[street]."'
              ,[territory]     = '".$post[territory]."'
              ,[site]          = '".$post[site]."'
              ,[stairs]        = '".$post[stairs]."'
              ,[access]        = '".$post[access]."'
              ,[equipment]     = '".$post[equipment]."'
              ,[existingSoil]  = '".$post[existingSoil]."'
              ,[newSoil]       = '".$post[newSoil]."'
              ,[rubbishRemove] = '".$post[rubbishRemove]."'
              ,[fiskar]        = '".$post[fiskar]."'
              ,[toilets]       = '".$post[toilets]."'
              ,[duration]      = '".$post[duration]."'
              ,[irrigation]    = '".$post[irrigation]."'
              ,[waterConn]     = '".$post[waterConn]."'
              ,[transpOld]     = '".$post[transpOld]."'
              ,[pruning]       = '".$post[pruning]."'
              ,[moveTerritory] = '".$post[moveTerritory]."'
              ,[hourRestr]     = '".$post[hourRestr]."'
              ,[parkPaid]      = '".$post[parkPaid]."'
              ,[robot]         = '".$post[robot]."'
              ,[lightCables]   = '".$post[lightCables]."'
              ,[barrier]       = '".$post[barrier]."'
              ,[fence]         = '".$post[fence]."'
              ,[notes]         = '".$post[notes]."'
              WHERE survId = ".$survId;
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

