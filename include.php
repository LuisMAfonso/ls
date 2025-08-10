<?php

if( !isset( $_SESSION['userId'] )  ) {
	session_start();
    ini_set('memory_limit', '2048M');
    set_time_limit(0);
}
error_reporting('E_ALL');
setlocale(LC_ALL, 'lt_LT');

date_default_timezone_set('Europe/Lisbon');
$DEBUG = 0;

loadEnv(__DIR__ . '/.env');


$portalPATH = getenv('PORTAL_PATH');

//Dados do Servidor
$DB_SERVER = "WEBSERVER";
$DB_USER   = getenv('DB_USER');
$DB_PASS   = getenv('DB_PASS');
$DB_NAME   = "ls_li";

include 'adodb5/adodb.inc.php';
$db = ADOnewConnection('pdo');
$db->connect('sqlsrv:server='.$DB_SERVER.';database='.$DB_NAME.';',$DB_USER,$DB_PASS);

function loadEnv($filePath) {
    if (file_exists($filePath)) {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

function checkAccess($userID, $pageName) {
    echo "entrei";

    $idTempPage = '';
    // GET ID PAGINA
    $sql = "SELECT ID FROM UsersMenus WHERE Prog = '".$actual_link."' ";
    $db->debug=1;
    $rows_emps = $db->Execute($sql);
    if ($rows_emps->rowCount() > 0) {
        $idTempPage = trim($rows_emps->fields[0]);
    }
    $rows_emps->Close();

    $acessoTemp = 0;
    $sql1 = "SELECT COUNT(*) FROM UsersGroupsMenu ugm
              INNER JOIN UsersGroups ug on ug.UserID = '$userID' and ug.Id = ugm.UGroupID
              WHERE OptId = $idTempPage and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1  )";
    //$db->debug=1;
    $rows_emps1 = $db->Execute($sql1);
    if($rows_emps1->rowCount() > 0){
        $acessoTemp = trim($rows_emps1->fields[0]);
    }
    $rows_emps1->Close();
    if($acessoTemp >= 1){
        return true;
    }
    else {
        $sqlI = "INSERT INTO UsersLogsDenied (LogUser, LogMenu, LogProg, LogDate) VALUES ('".$userID."', ".$idTempPage.", '".$actual_link."', getdate() )";
        $rows_empsI = $db->Execute($sqlI);
        echo "<script>window.location.href = '".$portalPATH."/accessNotAllowed.php';</script>";
        exit;
    }
}

?>