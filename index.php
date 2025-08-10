<?php

include( "include.php" );
error_reporting('0');

require_once('header.php');
require_once('vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php');
$detect = new Mobile_Detect;

if ( $detect->isMobile() ) {
	 echo '<script>window.location.href = "mobile/index.php";</script>';
}

?>

<script language="javascript" type="text/javascript">
	function setFocus() {
		document.loginForm.usrname.select();
		document.loginForm.usrname.focus();
	}

<?php
	if ( isset( $_GET['message'] )) { $message = $_GET['message'];  } else { $message = ''; };
	if( $message != '' ) {
		echo "alert( 'Utilizador ou palavra-chave erradas' );";
	}
?>
</script>
<link rel="shortcut icon" href="images/favicon.ico" />
</head>
<body onload="setFocus();">
<div class="login_page" align="center">
	<form action="login.php" method="post" name="loginForm" id="loginForm">
		<div class="login_logo">
			<img class="logo_img" src="images/logo_img.png">
			<img class="logo_name" src="images/logo_name.png">
		</div>
		<div class="wrap-input100 validate-input m-t-50 m-b-35" data-validate = "Enter username">
			<input class="input100" type="text" name="usrname">
			<span class="focus-input100" data-placeholder="Username"></span>
		</div>
		<div class="wrap-input100 validate-input m-b-50" data-validate="Enter password">
			<input class="input100" type="password" name="pass">
			<span class="focus-input100" data-placeholder="Password"></span>
		</div>
		<div class="container-login100-form-btn">
			<button class="login100-form-btn">Login</button>
		</div>
		<div class="login-more">
            <span class="txt1">
              <center>landScaper v1.0</center>
            </span>
        </div>
	</form>
</div>
<div id="break"></div>
<noscript>
!Warning! Javascript must be enabled for proper operation
</noscript>
</body>
</html>