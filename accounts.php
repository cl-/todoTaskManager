<?php 
//*******************Below this part is included in all php files*****************//
	require "config.inc";
	include("databaseclass.php");
	
	session_save_path($session_save_path);
	session_start(); // must be first thing in the php file
	
	//First thing to Check if already logged on
	if(isset($_SESSION['isLoggedOn']) && $_SESSION['isLoggedOn'] == 'yes'){
		//header("Location: home.php");
	} else {
		header("Location: loginpage.php");
	}
	
	$log = new database();     //Instentiate the class
	$log->dbconnect($hostname_logon, $hostport_logon, $database_logon, $username_logon, $password_logon);        //Connect to the database
	
//*******************Above this part is included in all php files*****************//
	
//*******************Start of Controller for current PHP file******************************//

	$display_message = "";
	$Username = "";
	$Birthday = "";
	$Age = "";
	$Gender = "";
	
	
	
	$UserAccount = $log->getAccountInfo();
	$Username = $UserAccount[0];
	$Birthday = $UserAccount[4];
	$Age = $UserAccount[3];
	$Gender = $UserAccount[2];

	
	
	
	if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Update Account'){
		if($_REQUEST['password']!=""){
			if($log->updateAccountInfo($_REQUEST['birthday'], $_REQUEST['sex'], $_REQUEST['age'], $_REQUEST['password'])){
				$Birthday = $_REQUEST['birthday'];
				$Age = $_REQUEST['age'];
				$Gender = $_REQUEST['sex'];
				$display_message = "Successfully updated account information";
			}else{
				$display_message = "Failed to updated account information";
			}
		}else{
			$display_message = "Password cannot be empty";
		}
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
				<form method="post" action="accounts.php">
					<div>
						<label for="birthday">Birthday</label>
						<input name="birthday" id="birthday" type="date" value="<?php echo($Birthday)?>" name="bday">
					</div>
					<div>
						<label for="gender">Gender</label>
						<?php if($Gender=="male"){?>
						<input type="radio" name="sex" id="gender" value="male" checked>Male
						<input type="radio" name="sex" id="gender" value="female">Female
						<?php } else{?>
						<input type="radio" name="sex" id="gender" value="male" >Male
						<input type="radio" name="sex" id="gender" value="female" checked>Female
						<?php }?>
					</div>
					<div>
						<label for="age">Age</label>
						<input name="age" id="age" type="number" value="<?php echo($Age)?>" min="1" max="120">
					</div>
					<div>
						<label for="password">Password</label>
						<input name="password" id="password" type="password">
					</div>
						<input name="action" id="action" value="login" type="hidden">
					<div>
						<input name="submit" id="submit" value="Update Account" type="submit">
					</div>
				</form>
			
				<?php echo($display_message);?>
			</div>
		</body>
	</html>
