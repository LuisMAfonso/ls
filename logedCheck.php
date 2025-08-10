<?php

if(!isset($_SESSION['userId']) || isset($_SESSION['userId']) == '')
{   
    $actual_link = "$_SERVER[REQUEST_URI]";
    $actual_link = str_replace($portalPATH.'/','',$actual_link);
    header("Location: ".$portalPATH."/index.php?p=".$actual_link);
//    header("Location: ".$portalPATH."/index.php");
    exit;
} else {
//        checkAccess($_SESSION['userId'],$_SERVER[REQUEST_URI]);
}

//if ( $_SESSION['userId'] == '' ) {
//     header("Location: ".$portalPATH."/index.php");
//     exit;
//}

?>