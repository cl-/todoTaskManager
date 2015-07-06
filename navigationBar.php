<nav>

	<a href="home.php">TODO</a>
	<a href="news.php">NEWS</a>
	<a href="contactus.php">Contact US</a>
	<?php 
	if(isset($_SESSION['isLoggedOn']) && $_SESSION['isLoggedOn'] == 'yes'){
		echo('<a href="logout.php">Log out</a>');
	}?>
	<?php 
	if(!isset($_SESSION['isLoggedOn']) || $_SESSION['isLoggedOn'] == 'no'){
		echo('<a href="loginpage.php">Login</a>');
	}?>
	<?php 
	if(!isset($_SESSION['isLoggedOn']) || $_SESSION['isLoggedOn'] == 'no'){
		echo('<a href="registerpage.php">Register</a>');
	}?>	
	<?php 
	if(isset($_SESSION['isLoggedOn']) || $_SESSION['isLoggedOn'] == 'yes'){
		echo('<a href="accounts.php">Accounts</a>');
	}?>	
	
	
</nav>
