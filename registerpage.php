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
	
	
//*******************Start of Model for current PHP file************************************//	
	$isSuccessfulRegistration = "";

//*******************End of Model for current PHP file**************************************//
	

//*******************Start of Controller for current PHP file******************************//

	$display_message = "";
	if(isset($_REQUEST['username']) && isset($_REQUEST['password']) && $_REQUEST['submit']== 'Register'){
		if($log->register($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['birthday'], $_REQUEST['sex'], $_REQUEST['age']) == true){
			$isSuccessfulRegistration = "yes";
			$display_message = "Successful registration with Username: ". $_REQUEST['username'] .'</br>You may click <a href="loginpage.php">here</a> to log in';
		}
		else{
			$display_message = "Username taken or you left out some fields. Try again";
			
		}
	}
	
//*******************End of Controller for current PHP file******************************//
	
?>

<!DOCTYPE html>
	<html lang="en">
		<?php require "templates/html_head.php"; ?>
		<body>
			<div class="ApplicationHeader">
				<h1 id="RegisterPageTitle">
					Registration Page
				</h1>
			</div>
			<?php require "navigationBar.php"; ?>
			<div class="container">
				<?php if($isSuccessfulRegistration != 'yes'){?>
				<form method="post" action="registerpage.php">
					<div>
						<label for="username">Username</label>
						<input name="username" id="username" type="text">
					</div>
					<div>
						<label for="birthday">Birthday</label>
						<input name="birthday" id="birthday" type="date" name="bday">
					</div>
					<div>
						<label for="gender">Gender</label>
						<input type="radio" name="sex" id="gender" value="male">Male
						<input type="radio" name="sex" id="gender" value="female">Female
					</div>
					<div>
						<label for="age">Age</label>
						<input name="age" id="age" type="number" min="1" max="120">
					</div>
					<div>
						<label for="password">Password</label>
						<input name="password" id="password" type="password">
					</div>
						<input name="action" id="action" value="login" type="hidden">
					<div>
						<input name="submit" id="submit" value="Register" type="submit">
					</div>
				</form>
				<?php } // End of if statement?>
				<?php echo($display_message);?>
			</div>
			
		</body>
	</html>