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

	require "expirationChecker.php";
	$expirationChecker = new expirationChecker();
	if($expirationChecker->check_verificationId() ==false){
		//unset($_REQUEST); //not working
		header("Location: home.php");
	}
	$expirationChecker->set_verificationId();


	$display_message = "";
	$tasklistMessage = "";
	
	if(isset($_REQUEST['submit']) && $_REQUEST['submit']== 'incrementProgress'){
		$log->incrementProgress($_REQUEST['taskname']);
	}
	
	//Form logic controller
	if(isset($_REQUEST['taskname']) && isset($_REQUEST['hours'])){
		if($_REQUEST['taskname'] =="" || $_REQUEST['hours'] ==""){
			$display_message = "Task name or number of hours is empty";
		}
		else{
				$display_message = "TaskName: " . $_REQUEST['taskname'] . '</br>';
				$display_message .= "Number of hours: " . $_REQUEST['hours']. '</br>';
				if($log->insertTask($_REQUEST['taskname'], $_REQUEST['hours'], 0)){
					$display_message .= "Successful";
				}else{
					$display_message .= "failed";
				}
		}
	}
	

	
	
	//Retrieving the data
	$taskList = $log->getTasklist();
	$progressStats = getOverallProgressStats($taskList);
	foreach ($taskList as &$task) {
		//$tasklistMessage .= $task[0].$task[1].$task[2].$task[3].$task[4].'</br>';
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

					<div id="add_task" class="centerDiv50pc bordered margin-tb20">
						<form method="post" action="home.php">
							<div id="task_form">
								<label for="taskname"></label>
								<input name="taskname" id="taskname" type="text" placeholder="Add Task">
								<input type="number" name="hours" min="1" max="20" placeholder="Hours">
								<input type="submit" style="visibility: hidden;" />
								<?php $expirationChecker->create_formSession_verificationInput();?>
								<br>Press the 'Enter' button to Add Task.
							</div>
						</form>
					</div>

					<div id="task_table_div" class="centerDiv80pc bordered margin-tb20">
						<table id="task_table">
							<thead><tr>
								<th class="taskTableColm
								<?php if(count($taskList)==0) echo "tableBorder";?>
								"></th>
								<th class="taskTableColm tableBorder">Task name</th>
								<th class="taskTableColm2 tableBorder">Progress</th>
								<th class="taskTableColm3 tableBorder">Notes</th>
							</tr></thead>
							
							<?php foreach($taskList as &$task): ?>
								<!-- Use the alternative colon-syntax when embedding HTML.-->
								<?php // include "templates/html_task.php"; //Doesnt work in loop it seems ?>
								<tr>
									<td>
										<form method='post' action='modify.php'>
											<button name='submit' type='submit' value='editpage'>Edit</button>
											<!-- <button name='autoProgTrack' type='submit' value='autoProgTrack'>AutoTracking</button> -->
											<input name='taskname' type='text'
												<?php echo "value='" . $task[0] ."'"; ?>
												style='width: 0px; visibility: hidden;'>
											<?php $expirationChecker->create_formSession_verificationInput();?>
										</form>

										<form method='post' action='home.php'>
											<button name='autoProgressTrack' type='submit' value='autoProgressTrack'>AutoTracking</button>
											<input name='taskname' type='text'
												<?php echo "value='" . $task[0] ."'"; ?>
												style='width: 0px; visibility: hidden;'>
											<?php $expirationChecker->create_formSession_verificationInput();?>
										</form>

									</td>

									<td class="tableBorder"><?php echo $task[0]; ?></td>
									
									<td class="tableBorder"><table >
									<?php
										for ($i = 0; $i < $task[1]; $i++) {
											if($i < $task[2]){
												echo("<td><span class='greenSquareBlock greenFont'>_</span></td>");
											}
											elseif($i == $task[2]){
												echo("<td><form class='incrementProgress' method='post' action='home.php'>
												<input name='taskname' type='text'");
												echo "value='" . $task[0] ."'";
												echo("style='width: 0px; visibility: hidden;'><button class='graySquareBlock' name='submit' type='submit' value='incrementProgress'>X</button>");
												$expirationChecker->create_formSession_verificationInput();
												echo("</form></td>");
											}
											else{
												echo("<td><span class='squareBlock whiteFont'>_</span></td>");
											}
											
										}//endof forloop to draw the progressIcons
									?>
									</table></td>
									
									<td class="tableBorder">
									<?php echo $task[5]; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							<?php if(count($taskList)==0)
							?>
						</table>
							<?php if(count($taskList)==0)
								echo ("
									<div id='noTaskMsg' class='centerDiv50pc tableBorder'>
										<p class='centerText'><strong>No tasks yet.</strong></p>
										<p class='centerText'>Add a Task to begin!</p>
									</div>
								");
							?>
					</div>

          <div id="progressStats" class="centerDiv50pc">
            <div id="progressStats-rate" class="centerLeftDiv50pc floatLeft bordered">
              <strong>Rate:</strong> <span class="graySquareBlock"><?php echo $progressStats["rate"];?></span> /day
              <br></br>
              <strong>Remaining:</strong> <span class="squareBlock"><?php echo $progressStats["workUnitsRemaining"];?></span>
              =>
              <?php $remainingDays = round($progressStats["workUnitsRemaining"]/$progressStats["rate"]);
              	if($progressStats["rate"]==0) $remainingDays="Many"; ?>
              <strong><?php echo $remainingDays; ?></strong>
              days work
	            </div>

            <div id="progressStats-legend" class="centerRightDiv50pc bordered">
              <strong>Legend</strong>:
              <br><span class="squareBlock whiteFont">_</span> &nbsp;&nbsp; = 30 minutes of work
              <br><span class="squareBlock">X</span> &nbsp;&nbsp; = click to complete
              <br><span class="greenSquareBlock greenFont">_</span> &nbsp;&nbsp; = completed work
            </div>

            <div class="floatClear"></div>
          </div>
					
					<?php 
						echo($display_message);
						echo($tasklistMessage);
						//require $view

					?>
				</div><!--endof home_container-->
			</div>
		</body>
	</html>

	
	
