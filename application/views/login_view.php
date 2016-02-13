<?php

        function redirectToHTTPS()
        {
            if($_SERVER['HTTPS']!="on")
            {
                $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("Location:$redirect");
            }
        }
        
        redirectToHTTPS();
 ?>
 
<!DOCTYPE html>	
<html>
	<head>
		<title>Login</title>
		
		
		<link rel="stylesheet" href="../../css/metro-bootstrap.css">
		<style>
		div._container {
			margin: 90px;
		}
		
		div._box {
			margin: auto;
			width: 330px;
			min-height: 150px;
			border: 1px solid white;
			padding: 10px;
		}
		
		legend.d {
			color: white;
		}
        
		.error{
            color: red;
        }
		
		</style>
	</head>
	
	<body class = 'metro' style="background:black">
		
		<div class = '_container'>
			<div style="text-align:center">
			<h2><font face="verdana" style="color:white;font-size:79px">Log In</font></h2><br>
			</div>
			<div class = '_box'>

				<?php echo form_open('authentication/do_login'); ?>
					<legend class = 'd'>User Name</legend>
					<div class = 'input-control text'>
						<input type = 'text' name = 'username'>
					</div>
					<legend class = 'd'>Password</legend>
					<div class = 'input-control text'>
						<input type = 'password' name = 'password'>
					</div>
					<div>
						<input type="radio" name="usertype" value="instructor"><a style="color:white">Instructor</a>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="usertype" value="student" checked><a style="color:white">Student</a>&nbsp;&nbsp;&nbsp;
						<input type="radio" name="usertype" value="TA"><a style="color:white">TA</a>
					</div>
					<input type = 'submit' name = 'submit' value = 'Sign in'>
				</form>
<?php echo validation_errors(); ?>
<?php echo '<p style="color:red">'.@$login_fail_msg.'</p>'; ?>
<?php echo '<p style="color:red">'.@$errorMsg.'</p>';?>
			</div>
		</div>
	</body>
</html>