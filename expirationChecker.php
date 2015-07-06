<?php

class expirationChecker{
  function __construct(){
    //$uniqueVerificationId = date('Y-m-d_H-i-s', time());
    $this->uniqueVerificationId = date("Y-m-d_H-i-s", strtotime("now")); //this conversion is so annoying.    
  }

  function set_verificationId(){
    $_SESSION["formSession_verificationId"] = $uniqueVerificationId;
  }
  function create_formSession_verificationInput(){
    echo "<input name='formSession_verificationId' type='text'";
    echo "value='" . $this->uniqueVerificationId ."'";
    echo "style='width: 0px; visibility: hidden;'>";
    //toodaloo($this->uniqueVerificationId);
  }
  function check_verificationId(){
    if ($_SESSION["formSession_verificationId"] == $_REQUEST["formSession_verificationId"])
      return true;
    else{
      //unset($_REQUEST); //not working
      header("Location: home.php");
      return false;
    }
  }
}

?>