<?php
require "utilities.php";
?>

  <html lang="en">
    <body>
      <div class="ApplicationHeader">
        <h1 id="LoginPageTitle">
          Login Page
        </h1>
      </div>

    </body>
  </html>

<?php
//Checks the time.
error_log("==================================================");
set_time_limit(0);
ob_implicit_flush(1);
for ($i=0; $i<10; $i++){
  toodaloo(date('h:i:s'));
  sleep(20);
  toodaloo(date('h:i:s'));
  if (ob_get_level()>0)
    ob_end_flush();
}
error_log("-------------------------");
?>
