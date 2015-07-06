<?php
//*******************Below this part is included in all php files*****************//
	require "config.inc";
	include("databaseclass.php");
	
	session_save_path($session_save_path);
	session_start(); // must be first thing in the php file
	
	//First thing to Check if already logged on
	if(isset($_SESSION['isLoggedOn']) && $_SESSION['isLoggedOn'] == 'yes'){
		 echo("YES");
		header("Location: home.php");
	}
	
	$log = new database();     //Instentiate the class
	$log->dbconnect($hostname_logon, $hostport_logon, $database_logon, $username_logon, $password_logon);        //Connect to the database
	
//*******************Above this part is included in all php files*****************//
	
	
	
//*******************Start of Controller for current PHP file******************************//

	
//*******************End of Controller for current PHP file******************************//
?>


<!DOCTYPE html>
	<html lang="en">
		<?php require "templates/html_head.php"; ?>
		<body>
			<div class="ApplicationHeader">
				<h1 id="DefaultPageTitle">
					Welcome to TODO
				</h1>
			</div>
			<?php require "navigationBar.php"; ?>
			<div class="container">
			</div>
			
		</body>
	</html>
