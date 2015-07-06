<?php

	//$url_prefix="https://...";
	$pagetitle="ToDo Web App";	//for title field in HTML


	//UTILITY FUNCTIONS for Configurations here
	function echo_htmlHeadTitle(){
    global $pagetitle; //define global here to access global above.
		echo "<title>".$pagetitle."</title>";
	}


///variable scope: http://www.php.net/manual/en/language.variables.scope.php
?>