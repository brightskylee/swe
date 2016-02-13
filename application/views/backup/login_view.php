<html>
<head>
<title>My Form</title>
</head>
<body>

<?php echo validation_errors(); ?>
<?php echo @$login_fail_msg; ?>
<?php echo @$errorMsg;?>
<?php echo form_open('authentication/do_login'); ?>

<h5>Username</h5>
<input type="text" name="username" value="" size="50" />

<h5>Password</h5>
<input type="password" name="password" value="" size="50" />

<div><input type="submit" value="Submit" /></div>

</form>

</body>
</html>