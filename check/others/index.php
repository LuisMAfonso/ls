<?php 
if( !isset( $_SESSION['checkLogin'] )  ) {
	$_SESSION["checkLogin"] = '';
}

if (isset($_GET['p'])) {$p = $_GET['p'];} else {$p = '';};
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<title>BI Rent v3 - DEV</title>

<link rel="shortcut icon" href="images/favicon.ico" />
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->

<script language="javascript" type="text/javascript">
  function setFocus() {
    document.loginForm.usrname.select();
    document.loginForm.usrname.focus();
  }

  function validaForm(){
    var valueTemp = document.loginForm.pass.value;
    document.loginForm.pass.value = encodeToHex(base64_encode(valueTemp));
  }

    function base64_encode(data) {
        var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, enc = "", tmp_arr = [];
        if (!data) {return data;}
        do { // pack three octets into four hexets
            o1 = data.charCodeAt(i++);
            o2 = data.charCodeAt(i++);
            o3 = data.charCodeAt(i++);
            bits = o1 << 16 | o2 << 8 | o3;
            h1 = bits >> 18 & 0x3f;
            h2 = bits >> 12 & 0x3f;
            h3 = bits >> 6 & 0x3f;
            h4 = bits & 0x3f;
            tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
        } while (i < data.length);
        enc = tmp_arr.join('');
        var r = data.length % 3;
        return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
    };

    function encodeToHex(str) {
        var hex = '';
        for(var i=0; i < str.length;i++) {
            hex += '' +str.charCodeAt(i).toString(16);
        }
        return hex;
    }

</script>


<link rel="shortcut icon" href="images/favicon.ico" />


</head>
<body>


	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-5 p-b-20">
				<?php
					if($p !== ""){
						echo '<form class="login100-form validate-form" action="login?p='.$p.'" method="post" name="loginForm" id="loginForm" onsubmit = "validaForm()">';
					}
					else{
						echo '<form class="login100-form validate-form" action="login" method="post" name="loginForm" id="loginForm" onsubmit = "validaForm()">';
					}
				?>
					<span class="login100-form-title p-b-60">
						Welcome
					</span>
					<span class="login100-form-avatar">
						<img src="images/Assinatura_Imagem.jpg" alt="LOGO">
					</span>

					<div class="wrap-input100 validate-input m-t-50 m-b-35" data-validate = "Enter username">
						<input class="input100" type="text" name="usrname">
						<span class="focus-input100" data-placeholder="Username"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-50" data-validate="Enter password">
						<input class="input100" type="password" name="pass">
						<span class="focus-input100" data-placeholder="Password"></span>
					</div>
            <ul class="login-more p-b-2">
            <span class="txt2">
              <center>
                   <?php
                          if(isset($_SESSION["checkLogin"])){
                              $checkLogin = $_SESSION["checkLogin"];
                              echo '<div class="inputlabel" style="margin-top: 0px;">'.$checkLogin.'</div>';
                          }
                      ?> 
              </center>
            </span>
          </ul>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login 
						</button>
					</div>

          <ul class="login-more p-t-80">
            <span class="txt1">
              <center>
                BI Rent <b>v3.0</b> - Drive on Holidays
              </center>
            </span>
          </ul>
				</form>
			</div>
		</div>
	</div>
<?php
    unset($_SESSION["checkLogin"]);
?>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>