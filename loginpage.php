<?php 
//*******************Below this part is included in all php files*****************//
	require "config.inc";
	include("databaseclass.php");
	
	session_save_path($session_save_path);
	session_start(); // must be first thing in the php file
	
	//First thing to Check if already logged on
	if(isset($_SESSION['isLoggedOn']) && $_SESSION['isLoggedOn'] == 'yes'){
		header("Location: home.php");
	}
	
	$log = new database();     //Instentiate the class
	$log->dbconnect($hostname_logon, $hostport_logon, $database_logon, $username_logon, $password_logon);        //Connect to the database
	
//*******************Above this part is included in all php files*****************//
	
//*******************Start of Controller for current PHP file******************************//

	$display_message = "";
	if(isset($_REQUEST['username']) && isset($_REQUEST['password']) && $_REQUEST['submit'] == 'Login'){
		if($log->login($_REQUEST['username'], $_REQUEST['password']) == true){
			$_SESSION['isLoggedOn'] = 'yes';
			$_SESSION['state'] = 'home';
			header( 'Location: home.php' ) ;
		}else{
			$display_message = "Wrong password/username";
		}
	}
	if($_REQUEST['submit']=="register"){
		header("Location: register.php");
	}
	
//*******************End of Controller for current PHP file******************************//
?>
<!--*********************************View portion begins below**************************-->
<!DOCTYPE html>
	<html lang="en">
		<?php require "templates/html_head.php"; ?>
		<body>
			<div class="ApplicationHeader">
				<h1 id="LoginPageTitle">
					Login Page
				</h1>
			</div>
			<?php require "navigationBar.php"; ?>

			
			
			<div class="container">
				<form method="post" action="loginpage.php">
				<div><label for="username">Username</label>
				<input name="username" id="username" type="text"></div>
				<div><label for="password">Password</label>
				<input name="password" id="password" type="password"></div>
				<input name="action" id="action" value="login" type="hidden">
				<div>
				<input name="submit" id="submit" value="Login" type="submit"></div>
				</form>
			
				<?php echo($display_message);?>
			</div>
		</body>
	</html>
