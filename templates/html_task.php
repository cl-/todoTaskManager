<?php
//PARAMETER REQUIREMENT: Needs $task defined!
  if(isset($task)){
    //We can proceed
    toodaloo("Creating task");
  } else {
    require "utilities.php"; ?>
    toodaloo("Warning: $task not defined");
  }
?>

  <tr>
    <td>
      <form method='post' action='modify.php'>
        <button name='submit' type='submit' value='editpage'>Edit</button>
        <input name='taskname' type='text'
          <?php echo "value='" . $task[0] ."'"; ?>
          style='width: 0px; visibility: hidden;'>
      </form>
    </td>

    <td><?php echo $task[0]; ?></td>
    
    <td><table >
    <?php
      for ($i = 0; $i < $task[1]; $i++) {
        if($i < $task[2]){
          echo("<td>Y</td>");
        }
        elseif($i == $task[2]){
          echo("<td><form id='incrementProgress' method='post' action='home.php'>
          <button id='incrementProgress' name='submit' type='submit' value='incrementProgress'>X</button>
          </form></td>");
        }
        else{
          echo("<td>X</td>");
        }
        
      }//endof forloop to draw the progressIcons
    ?>
    </table></td>
  
  </tr>

