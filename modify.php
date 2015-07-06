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

	if(isset($_REQUEST['return'])){
		header("Location: home.php");
	}
	
	$display_message = "";
	$tasklistMessage = "";
	$task ="";
	$taskName = "";
	$taskTotalTimeSlots="";
	$taskProgressedTimeSlots ="";
	$taskNotes = "";
	$deletedState="no";

	if(isset($_REQUEST['taskname']) && $_REQUEST['taskname']!=""){
		$task = $log->getTask($_REQUEST['taskname']);

		$taskName = $task[0];
		$taskTotalTimeSlots = $task[1];
		$taskProgressedTimeSlots = $task[2];
		$taskNotes = $task[5];
		
	}else{
		//This is to redirect invalid access to modify page that is not linked to any task
		header("Location: home.php");
	}
	
	if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="editpage"){

	}else if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="modify"){
		if($log->modifyTask($_REQUEST['taskname'], $_REQUEST['NewTaskname'], $_REQUEST['TotalTimeSlots'], $_REQUEST['ProgressedTimeSlots'], $_REQUEST['taskNotes'])){
			$display_message = "Successful modification";
			$taskName = $_REQUEST['NewTaskname'];
			$taskTotalTimeSlots = $_REQUEST['TotalTimeSlots'];
			$taskProgressedTimeSlots = $_REQUEST['ProgressedTimeSlots'];
			$taskNotes = $_REQUEST['taskNotes'];
			
		}else{
			$display_message = "failed modification";
		}
	}else if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="delete"){
		$deletedState = 'yes';
		if($log->deleteTask($taskName)){
			$display_message = "Successful deletion";
			
		}else{
			$display_message = "Failed to delete";
		}
	}
	
	
//*******************End of Controller for current PHP file******************************//


?>
<!DOCTYPE html>
	<html lang="en">
		<?php require "templates/html_head.php"; ?>
		<body>
			<div class="ApplicationHeader">
				<h1 id="HomeHeader">
					Home page
				</h1>
			</div>
			
			<?php require "navigationBar.php"; ?>
			<div class="container">
				<div id="home_container">
					
					<?php if($deletedState=='no'){ ?>
					<div id="modifyTask_container" class="bordered">
						<form method='post' action='modify.php'>
							Task Name: <input name='NewTaskname' type='text' value="<?php echo($taskName)?>"></input>
							<br>Total TimeSlots Needed: <input type="number" name="TotalTimeSlots" min="1" max="20" value=<?php echo($taskTotalTimeSlots)?>>
							<br>Progressed TimeSlots: <input type="number" name="ProgressedTimeSlots" min="0" max=<?php echo($taskTotalTimeSlots)?> value=<?php echo($taskProgressedTimeSlots)?>>
							<br>Notes: <input type="text" name="taskNotes" value="<?php echo($taskNotes)?>">
							<br><button name='submit' type='submit' value='modify'>Save</button>
							<button name='submit' type='submit' value='delete'>Delete</button>
							<button name='return' type='submit' value='return'>Return to TODO Task List</button>
							<input name='taskname' type='text' value="<?php echo($taskName)?>" style='visibility: hidden;'>
						</form>
					</div>
					<?php } else { ?>
						<form method='post' action='modify.php'>
							<button name='submit' type='submit' value='undo'>Undo</button>
							<button name='return' type='submit' value='return'>Return to TODO Task List</button>
							<!--
							TODO 
							
							<input name='taskname' type='text' value=<?php echo($taskName)?> style='visibility: hidden;'>
							-->

						</form>
					
					<?php } //deletedstate if else case?>
					
					<?php 
						echo($display_message);
						echo($tasklistMessage);
						//require $view

					?>
				</div>
			</div>
		</body>
	</html>

	
	
