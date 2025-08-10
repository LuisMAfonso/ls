<?php

include( "include.php" );

//$db->debug=1;
if (isset($_GET['p'])) {$p = $_GET['p'];} else {$p = '';};

// Include and instantiate the class.
require_once('Mobile-Detect/Mobile_Detect.php');
$detect = new Mobile_Detect;

$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer'); 
$scriptVersion = 'other'; 
if ( $detect->version('iPad') ) { $scriptVersion = 'iPad'; }
if ( $detect->version('iPhone') ) { $scriptVersion = 'iPhone'; }
if ( $detect->version('Android') ) { $scriptVersion = 'Android'; }
if ( $detect->version('iPad') ) { $scriptVersion = 'iPad'; }
if ( $detect->version('Windows NT') ) { $scriptVersion = 'Windows NT'; }
if ( $detect->version('Windows Phone') ) { $scriptVersion = 'Windows Phone'; }

$_SESSION['userCode'] = '';
$_SESSION['user_id'] = '';
$_SESSION['user_name'] = '';
$_SESSION['user_emps'] = '';
$_SESSION['Employee'] = '';
$_SESSION['user_admin'] = '';
$_SESSION['user_pos'] = '';
$_SESSION['userStation'] = '';
$_SESSION['user_alert'] = 0;
$_SESSION['firstYear'] = 0;
$_SESSION['mobile'] = '';
$_SESSION['deskColor'] = '';
$_SESSION['notification'] = 0;
$reset_password = '';



if ( $detect->isMobile() || $detect->isTablet() ) {
	$_SESSION['mobile'] = 'y'; 
}

//print_r($_POST);
// Verifica login
$sql = "SELECT UserID, UserPass, UserName, IsAdmin, IsBalcony, rtrim(Station), isnull(deskColor,'skyblue') , reset_pass,Employee
		from Users 
		where UserID=? and UserPass=? and Locked <> 'Y'";
//$db->debug=1;
$rows = $db->Execute($sql,[$_POST['usrname'], $_POST['pass']]);


while(!$rows->EOF) 
{
	$_SESSION['userCode'] = $rows->fields[0];
	$_SESSION['user_id'] = $rows->fields[0];
	$_SESSION['user_name'] = utf8($rows->fields[2]);
	$_SESSION['user_admin'] = $rows->fields[3];
	$_SESSION['userStation'] = $rows->fields[5];
	$_SESSION['deskColor'] = $rows->fields[6];
	$_SESSION['Employee'] = $rows->fields[8];
	$reset_password = $rows->fields[7];
	$_SESSION['user_pos'] = 'A';
	if ($rows->fields[4] == 'Y') {
		$_SESSION['user_pos'] = 'R';
	}
	if ($rows->fields[4] == 'S' || $rows->fields[4] == 'P' || $rows->fields[4] == 'X'  || $rows->fields[4] == 'F' || $rows->fields[4] == 'C') {
		$_SESSION['user_pos'] = $rows->fields[4];
	}

	$rows->MoveNext();
}

$rows->Close();

$sql = "SELECT FirstYear FROM [Parameters]";
$rows = $db->Execute($sql);

if (!$rows->EOF) 
{
	$_SESSION['firstYear'] = $rows->fields[0];
}
$rows->Close();

// Verifica se login existe ou n�o
//print($_SESSION['userCode']);
if( $_SESSION['userCode'] == '' ) {
	$_SESSION["checkLogin"] = "O login esta&#769; incorrecto. Tente novamente.";
	unset($_POST);
	//$message = '<p>O login est� incorrecto.</p><p><a href="index.php">Tente novamente.</a></p>';
	include( "index.php" );
}
else{

	// VALIDA DE � NECESS�RIO ALTERAR A PASSWORD
	if ($reset_password == 1 || $reset_password == ''){
		echo '<script>
		alert("E necessario alterar a sua password."); 
		window.location = "resetPassword.php";</script>';
	}
	else{
		//GET NOTIFICA��ES
		$sql1 = "SELECT notificacao FROM [BIrent].[dbo].[notifications] WHERE UserId = '".$_SESSION['userCode']."' AND estado = 1";
		//$db->debug=1;
		$rows_emps1 = $db->Execute($sql1);
		if($rows_emps1->rowCount() > 0){
			$_SESSION['notification'] = 1;
		}
		else{
			$_SESSION['notification'] = 0;
		}


		// Obtem as empresas a que pertence
		$_SESSION['user_emps'] = '';
		$sql = "INSERT INTO UsersLogs ( LogUser, LogDate, LogDevice, LogVersion) VALUES ('".$_SESSION['userCode']."', getdate(), '".$deviceType."', '".$scriptVersion."' ) ";
		$rows = $db->Execute($sql);
		//echo "<b>A redireccionar...</b>";

		echo '<style>
		@import url(css/login.css?version=3.2);
		.loader {
		  color: #919191;
		  font-size: 10px;
		  margin: 80px auto;
		  width: 1em;
		  height: 1em;
		  border-radius: 50%;
		  position: relative;
		  text-indent: -9999em;
		  -webkit-animation: load4 1.3s infinite linear;
		  animation: load4 1.3s infinite linear;
		  -webkit-transform: translateZ(0);
		  -ms-transform: translateZ(0);
		  transform: translateZ(0);
		}
		@-webkit-keyframes load4 {
		  0%,
		  100% {
		    box-shadow: 0 -3em 0 0.2em, 2em -2em 0 0em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 0;
		  }
		  12.5% {
		    box-shadow: 0 -3em 0 0, 2em -2em 0 0.2em, 3em 0 0 0, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
		  }
		  25% {
		    box-shadow: 0 -3em 0 -0.5em, 2em -2em 0 0, 3em 0 0 0.2em, 2em 2em 0 0, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
		  }
		  37.5% {
		    box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 0, 2em 2em 0 0.2em, 0 3em 0 0em, -2em 2em 0 -1em, -3em 0em 0 -1em, -2em -2em 0 -1em;
		  }
		  50% {
		    box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 0em, 0 3em 0 0.2em, -2em 2em 0 0, -3em 0em 0 -1em, -2em -2em 0 -1em;
		  }
		  62.5% {
		    box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 0, -2em 2em 0 0.2em, -3em 0 0 0, -2em -2em 0 -1em;
		  }
		  75% {
		    box-shadow: 0em -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0.2em, -2em -2em 0 0;
		  }
		  87.5% {
		    box-shadow: 0em -3em 0 0, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0, -2em -2em 0 0.2em;
		  }
		}
		@keyframes load4 {
		  0%,
		  100% {
		    box-shadow: 0 -3em 0 0.2em, 2em -2em 0 0em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 0;
		  }
		  12.5% {
		    box-shadow: 0 -3em 0 0, 2em -2em 0 0.2em, 3em 0 0 0, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
		  }
		  25% {
		    box-shadow: 0 -3em 0 -0.5em, 2em -2em 0 0, 3em 0 0 0.2em, 2em 2em 0 0, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;
		  }
		  37.5% {
		    box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 0, 2em 2em 0 0.2em, 0 3em 0 0em, -2em 2em 0 -1em, -3em 0em 0 -1em, -2em -2em 0 -1em;
		  }
		  50% {
		    box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 0em, 0 3em 0 0.2em, -2em 2em 0 0, -3em 0em 0 -1em, -2em -2em 0 -1em;
		  }
		  62.5% {
		    box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 0, -2em 2em 0 0.2em, -3em 0 0 0, -2em -2em 0 -1em;
		  }
		  75% {
		    box-shadow: 0em -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0.2em, -2em -2em 0 0;
		  }
		  87.5% {
		    box-shadow: 0em -3em 0 0, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0, -2em -2em 0 0.2em;
		  }
		}
 
		body {
 
			background: -moz-linear-gradient(to bottom, rgba(255,255,255,0.7) 0%,rgba(255,255,255,0.3) 100%), url("images/background/'.rand(1,12).'.jpg");
			background: -webkit-gradient(to bottom, rgba(255,255,255,0.7) 0%,rgba(255,255,255,0.3) 100%), url("images/background/'.rand(1,12).'.jpg");
			background: -webkit-linear-gradient(to bottom, rgba(255,255,255,0.7) 0%,rgba(255,255,255,0.3) 100%), url("images/background/'.rand(1,12).'.jpg");
			background: -o-linear-gradient(to bottom, rgba(255,255,255,0.7) 0%,rgba(255,255,255,0.3) 100%), url("images/background/'.rand(1,12).'.jpg");
			background: -ms-linear-gradient(to bottom, rgba(255,255,255,0.7) 0%,rgba(255,255,255,0.3) 100%), url("images/background/'.rand(1,12).'.jpg");
			background: linear-gradient(to bottom, rgba(255,255,255,0.7) 0%,rgba(255,255,255,0.3) 100%), url("images/background/'.rand(1,12).'.jpg");s;
 			background-repeat: no-repeat;
			background-attachment: fixed; 
			background-size: 100% auto;
			overflow: hidden; 
  		}
	 
		</style>';
		/*echo '
<div class="pyro">
    <div class="before"></div>
    <div class="after"></div>
</div>
';*/

		echo '<div> </div>';
		//echo '<div style="text-align: center; color: black; font-size: 12px; padding-top: 10%;"><p><b>BI Rent v2.0</b></div>';
		echo '<div style="padding-top: 8%;"> <center><img width="120" height="135" src="./images/Assinatura_Imagem.jpg"></center></div>';
		echo '<div id="page-loader" style="text-align: center; color: black; font-size: 30px; padding-top: 1%;"><p><b>Bem-Vindo(a) '.$_SESSION['user_name'].'</b></div>';
		echo '<div class="loader"></div>';
		if($p !== ''){
			echo '<script>
			setTimeout(function(){
				window.location = "'.$p.'";
			}, 2500);</script>';
		}
		else{
			echo '<script>
			setTimeout(function(){
			 window.location = "files/home";
			}, 2500);</script>';
		}
	}
}

?> 