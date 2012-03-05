<?php
//TODO: Implement username/password system.
//TODO: Set `userid` calls to user's id number when implemented
// Initialize the MySQL database handle

require_once("classes/class.login.php");
$auth = new auth(false,true,"dashboard.php",true);
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>h4x.stats | Log in</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width,initial-scale=1">

	<link rel="stylesheet" href="css/style.css">
	<script src="js/libs/jquery-1.7.1.min.js"></script>
	<script src="js/functions.js"></script>
</head>

<body>
	<div id="container">
		<header>

		</header>
		<div id="main" role="main" style="width: 800px;">
			<div class="title clearfix">
				h4x.stats
				<span class="subtitle">Log in.</span>
			</div>

			<form>
				<input type="submit" value="Log in" id="login" class="bigsubmit button" style="float:right;" />
				<div style="overflow: hidden; padding-right: 30px;">
					<input type="text" name="username" id="username" class="biginput" value="Username" style="color:#ccc;" onfocus="inputClick(this, 'Username');" onblur="inputBlur(this, 'Username');" />
					<input type="password" name="password" id="password" class="biginput" value="Password" style="color:#ccc;" onfocus="inputClick(this, 'Password');" onblur="inputBlur(this, 'Password');" />
				</div>
				<div id="error_container">&nbsp;<label class="error" for="url" id="url_error"></label></div>

			</form>
		</div>
		<footer>
			h4x.stats written by <a href="http://www.h4xful.net/">Justin Swanson</a>.
		</footer>
	</div> <!--! end of #container -->
</body>
</html>