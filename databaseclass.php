<?php
	require "utilities.php";
	session_save_path("sess");
	session_start();
	
	class database{

		//table fields for table appuser
		var $user_table = 'appuser';          //Users table name
		var $user_column = 'usernames';     //USERNAME column (value MUST be valid email)
		var $pass_column = 'userpasswords';      //PASSWORD column
		var $user_birthday = 'birthdays';
		var $user_gender = 'gender';
		var $user_age = 'age';
		
		
		
		//table fields for table task
		var $task_table_name = 'tasktable';
		var $task_name_column = 'taskname';
		var $task_totalWorkUnits_column = 'totalworkunits';
		var $task_progressedUnits_column = 'progressedunits';
		var $task_username_column = 'usernames';
		var $task_dateCreated_column = 'datecreated';
		var $task_notes_column = 'notes';
		var $task_minutesProgressed_column = 'minutesProgressed';
		
		var $dbconn = '';
		
		//connect to database
		function dbconnect($aHostname_logon, $aHostport_logon, $aDatabase_logon, $username_logon, $aPassword_logon){
			
			$connectionString = "host=$aHostname_logon port=$aHostport_logon dbname=$aDatabase_logon user=$username_logon password=$aPassword_logon";
			$this->dbconn = pg_connect($connectionString) or die ('Unable to connect to the database');
			
			return;
		}
		function login($aUsername, $aPassword){
			
			if(($this->dbconn)  != ''){
				//echo('logging in');
				$login_query="SELECT * FROM $this->user_table where $this->user_column = $1 AND $this->pass_column = $2;";
				$result = pg_prepare($this->dbconn, "my_query", $login_query);
				$result = pg_execute($this->dbconn, "my_query", array($aUsername,$aPassword));
				
				if(pg_num_rows($result)==0 || !($result)){
					//echo "no result";
					return false;
				}else{
					$row = pg_fetch_array($result);
					$_SESSION['databaseclass_isloggedin']=$row[userpasswords];
					$_SESSION['databaseclass_loggedinUsername']=$row[usernames];
					
					return true;
				}
			}
			else{
				//echo ("Cannot log in");
				return false;
			}
		}
		function logout(){
			$_SESSION['databaseclass_isloggedin'] = "";
			$_SESSION['databaseclass_loggedinUsername']="";
		}
		
		function logincheck($aLogincode){
		
			if(($this->dbconn)  != ''){
				$login_query="SELECT * FROM $this->user_table where $this->pass_column = $1;";
				$result = pg_prepare($this->dbconn, "my_query", $login_query);
				$result = pg_execute($this->dbconn, "my_query", array($aLogincode));
				if(pg_num_rows($result)==0 || !($result)){
					//echo "no result";
					return false;
				}else{
					//is logged on
					return true;
				}
				
			}
			return false;
		}

		function register($aUsername, $aPassword, $aBirthday, $aGender, $aAge){
			if(($this->dbconn)  != ''){
				$register_query="INSERT INTO $this->user_table ($this->user_column, $this->pass_column, $this->user_birthday, $this->user_gender, $this->user_age) values($1, $2, $3, $4, $5);";
				$result = pg_prepare($this->dbconn, "my_query", $register_query);
				$result = pg_execute($this->dbconn, "my_query", array($aUsername, $aPassword, $aBirthday, $aGender, $aAge));
				if($result){
					return true;
				} else {
					return false;
				}
			}
		}
		function insertTask($aTaskName, $aTotalWorkUnits, $aProgressUnits, $aNotes){
			if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
				$username = $_SESSION['databaseclass_loggedinUsername'];
				
				if(($this->dbconn)  != ''){
					$insert_query="INSERT INTO $this->task_table_name($this->task_username_column, $this->task_name_column, $this->task_totalWorkUnits_column, $this->task_progressedUnits_column, $this->task_dateCreated_column, $this->task_notes_column, $this->task_minutesProgressed_column) VALUES($1, $2, $3, $4, 'now', $5, 0);";
					$result = pg_prepare($this->dbconn, "my_query", $insert_query);
					$result = pg_execute($this->dbconn, "my_query", array($username, $aTaskName, $aTotalWorkUnits, $aProgressUnits, $aNotes));
					if(!$result){
						return false;
					}else{
						return true;
					}
				}

			} else {
				return false;
			}
		}
		function getTasklist(){
			if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
				$username = $_SESSION['databaseclass_loggedinUsername'];
				
				if(($this->dbconn)  != ''){
					$select_query="SELECT * from $this->task_table_name where $this->task_username_column = $1 order by $this->task_dateCreated_column;";
					$result = pg_prepare($this->dbconn, "my_query2", $select_query);
					$result = pg_execute($this->dbconn, "my_query2", array($username));
					
					//Commenting out. This is causing some php error on undefined variable.
					//echo($aSortOrder);
					$taskList = array();
					if(!$result){
						return pg_last_error($this->dbconn);
					}else{	
					
						while ($row = pg_fetch_array($result)) {
							// Pull out individual columns from the current row
							$taskList[] = array($row[$this->task_name_column] , $row[$this->task_totalWorkUnits_column],$row[$this->task_progressedUnits_column],$row[$this->task_username_column],$row[$this->task_dateCreated_column], $row[$this->task_notes_column]);
							//$taskList[count($taskList)][] = 
							//getTaskProgressStats($row[$this->task_name_column] , $row[$this->task_totalWorkUnits_column],$row[$this->task_progressedUnits_column], $row[$this->task_dateCreated_column]);
						}
						getOverallProgressStats($taskList);
						return $taskList;
					}
				}
				
			}
		}
	function getTask($aTaskName){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				$select_query="SELECT * from $this->task_table_name where $this->task_username_column = $1 AND $this->task_name_column = $2;";
				$result = pg_prepare($this->dbconn, "my_query2", $select_query);
				$result = pg_execute($this->dbconn, "my_query2", array($username, $aTaskName));
				
				$taskList = "";
				if(!$result){
					return pg_last_error($this->dbconn);
				}else{	
				
					while ($row = pg_fetch_array($result)) {
						// Pull out individual columns from the current row
						$taskList = array($row[$this->task_name_column] , $row[$this->task_totalWorkUnits_column],$row[$this->task_progressedUnits_column],$row[$this->task_username_column],$row[$this->task_dateCreated_column], $row[$this->task_notes_column]);
						
					}
					return $taskList;
				}
			}
		}
	}
	function deleteTask($aTaskName){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				$delete_query="DELETE from $this->task_table_name where $this->task_username_column = $1 AND $this->task_name_column =$2;";
				$result = pg_prepare($this->dbconn, "my_query3", $delete_query);
				$result = pg_execute($this->dbconn, "my_query3", array($username, $aTaskName));
				$rows_affected=pg_affected_rows($result);
				//echo("rows_affected=$rows_affected");
				if(!$result){
					return false;
				}else{	
					return true;
				}
			}
		}
	}
	function modifyTask($aOldTaskName, $aNewTaskName, $taskTotalTimeSlots, $taskProgressedTimeSlots, $ataskNotes){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				$update_query="update $this->task_table_name SET $this->task_name_column =$1, $this->task_totalWorkUnits_column = $2, $this->task_progressedUnits_column = $3, $this->task_notes_column = $6 where $this->task_username_column = $4 AND $this->task_name_column =$5;";
				$result = pg_prepare($this->dbconn, "my_query4", $update_query);
				$result = pg_execute($this->dbconn, "my_query4", array($aNewTaskName, $taskTotalTimeSlots, $taskProgressedTimeSlots, $username, $aOldTaskName, $ataskNotes));

				if(!$result){
					return false;
				}else{	
					return true;
				}
			}
		}
	}
	
	function incrementProgress($aTaskName){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				$update_query="update $this->task_table_name SET $this->task_progressedUnits_column = $this->task_progressedUnits_column+1
				 where $this->task_username_column = $1 AND $this->task_name_column =$2;";
				$result = pg_prepare($this->dbconn, "my_query5", $update_query);
				$result = pg_execute($this->dbconn, "my_query5", array($username, $aTaskName));
				echo( pg_last_error($this->dbconn));
				if(!$result){
					return false;
				}else{	
					return true;
				}
			}
		}
	}
	function incrementMinutesProgress($aTaskName){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				$update_query="update $this->task_table_name SET $this->task_minutesProgressed_column = $this->task_minutesProgressed_column+1
				 where $this->task_username_column = $1 AND $this->task_name_column =$2;";
				$result = pg_prepare($this->dbconn, "my_query6", $update_query);
				$result = pg_execute($this->dbconn, "my_query6", array($username, $aTaskName));
				echo( pg_last_error($this->dbconn));
				if(!$result){
					return false;
				}else{	
					return true;
				}
			}
		}
	}

	function getProgressStats($aTaskName){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				//NOTE: This is currently a stub.
				$select_query="SELECT * from $this->task_table_name where $this->task_username_column = $1 order by $this->task_dateCreated_column;";
				$result = pg_prepare($this->dbconn, "my_query2", $select_query);
				$result = pg_execute($this->dbconn, "my_query2", array($username));
				
				//echo($aSortOrder);
				$taskList = array();
				if(!$result){
					return pg_last_error($this->dbconn);
				}else{
					while ($row = pg_fetch_array($result)) {
						// Pull out individual columns from the current row
						$taskList[] = array($row[$this->task_name_column] , $row[$this->task_totalWorkUnits_column],$row[$this->task_progressedUnits_column],$row[$this->task_username_column],$row[$this->task_dateCreated_column]);
						
					}
					return $taskList;
				}

			}//endof dbconn checking
		}//endof if isLoggedIn
	}//endof getProgressStats()


	function getAccountInfo(){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				$select_query="SELECT * FROM $this->user_table WHERE $this->user_column = $1;";

				$result = pg_prepare($this->dbconn, "my_query6", $select_query);
				$result = pg_execute($this->dbconn, "my_query6", array($username));
				$userAccount = "";
				if(!$result){
					return pg_last_error($this->dbconn);
				}else{	
					while ($row = pg_fetch_array($result)) {
						// Pull out individual columns from the current row
						$userAccount = array($row[$this->user_column] , $row[$this->pass_column],$row[$this->user_gender],$row[$this->user_age],$row[$this->user_birthday]);
					}
					return $userAccount;
				}
			}
		}
	}
	
	function updateAccountInfo($aBirthday, $aGender, $aAge, $aPassword){
		if(isset($_SESSION['databaseclass_loggedinUsername']) && $_SESSION['databaseclass_loggedinUsername']!=""){
			$username = $_SESSION['databaseclass_loggedinUsername'];

			if(($this->dbconn)  != ''){
				$update_query="UPDATE $this->user_table SET $this->pass_column = $1, $this->user_birthday = $2, $this->user_gender = $3, $this->user_age = $4 WHERE $this->user_column = $5;";
				
				$result = pg_prepare($this->dbconn, "my_query1", $update_query);
				$result = pg_execute($this->dbconn, "my_query1", array($aPassword, $aBirthday, $aGender, $aAge, $username));
				if(!$result){
					return false;
				}else{	
					return true;
				}
			}
		}
	}
	
		
		
	}//endof class database
//	class calculator{ //Replace with static class later.
		function getOverallProgressStats($aTaskListArr){
			$dateToday = new DateTime("now");//=date("Y-m-d");
			//$dateCreatedStr = date("Y-m-d", strtotime($aDateCreated)); //this conversion is so annoying.
			//$dateCreatedStr = "2014-01-01";
			$dateCreated = new DateTime($dateCreatedStr); //Note a $aDateCreated is a MySQL timestamp. Need to convert to Unix timestamp. ///http://stackoverflow.com/questions/4577794/how-to-convert-mysql-time-to-unix-timestamp-using-php
			$totalDaysElapsed = $dateToday->diff($dateCreated)->days; ///http://docs.php.net/datetime.diff

			//Recall: $taskList[] = array($row[$this->task_name_column] , $row[$this->task_totalWorkUnits_column],$row[$this->task_progressedUnits_column],$row[$this->task_username_column],$row[$this->task_dateCreated_column]);
			$allTasks = array("totalWorkUnits"=>0, "workUnitsDone"=>0, "workUnitsRemaining"=>0, "rate"=>0);

			$totalDaysElapsed=0;
			foreach($aTaskListArr as &$task){
				$totalWorkUnits = $task[1];
				$workUnitsDone = $task[2];
				$workUnitsRemaining = $totalWorkUnits - $workUnitsDone;
				$allTasks["totalWorkUnits"] += $totalWorkUnits;
				toodaloo("wu:".$allTasks["totalWorkUnits"]);
				$allTasks["workUnitsDone"] += $workUnitsDone;
				toodaloo("dd:".$allTasks["workUnitsDone"]);
				$allTasks["workUnitsRemaining"] += $workUnitsRemaining;

				$dateCreatedStr = date("Y-m-d", strtotime($task[4])); //this conversion is so annoying.
				$dateCreated = new DateTime($dateCreatedStr); //Note a $aDateCreated is a MySQL timestamp. Need to convert to Unix timestamp. ///http://stackoverflow.com/questions/4577794/how-to-convert-mysql-time-to-unix-timestamp-using-php
				$daysElapsed = $dateToday->diff($dateCreated)->days; ///http://docs.php.net/datetime.diff
				if($daysElapsed>$totalDaysElapsed){
					$totalDaysElapsed = $daysElapsed;
				}
			}

			if($totalDaysElapsed==0)
				$totalDaysElapsed=1;
			$allTasks["rate"] = $allTasks["workUnitsDone"]/$totalDaysElapsed; //$totalDaysElapsed for this particular task
			if(0<$allTasks["rate"] && $allTasks["rate"]<1){
				$allTasks["rate"] = 1;
				//$allTasks["rate"] = round($allTasks["rate"], 2);
			}
			//require "utilities.php";
			toodaloo("rate:".$allTasks["rate"]);
			toodaloo("days:".$totalDaysElapsed);
			toodaloo("totalW".implode(", ",$allTasks));
			return $allTasks;
		}

		function getTaskProgressStats($aTaskName, $aTotalWorkUnits, $aProgressedUnits, $aDateCreated){
			$dateToday = new DateTime("now");//=date("Y-m-d");
			$dateCreatedStr = date("Y-m-d", strtotime($aDateCreated)); //this conversion is so annoying.
			$dateCreated = new DateTime($dateCreatedStr); //Note a $aDateCreated is a MySQL timestamp. Need to convert to Unix timestamp. ///http://stackoverflow.com/questions/4577794/how-to-convert-mysql-time-to-unix-timestamp-using-php
			$totalDaysElapsed = $dateToday->diff($dateCreated)->days; ///http://docs.php.net/datetime.diff

			$workUnitsDone = $aProgressedUnits;
			$workUnitsRemaining = $aTotalWorkUnits - $workUnitsDone;

			if($totalDaysElapsed==0)
				$totalDaysElapsed=1;
			$rate = $workUnitsDone/$totalDaysElapsed; //$totalDaysElapsed for this particular task
			if(0<$rate && $rate<1){
				$rate = round($rate, 2);
			}
			//require "utilities.php";
			return $workUnitsRemaining;
		}

//	}//endof class calculator

?>