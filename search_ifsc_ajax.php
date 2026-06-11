<?php
include "db.php";

$q = mysqli_real_escape_string($conn,$_GET['q']);

$sql="SELECT DISTINCT name,branch,city,state 
      FROM bank 
      WHERE name LIKE '$q%' 
         OR branch LIKE '$q%'
         OR city LIKE '$q%' 
         OR state LIKE '$q%'
      LIMIT 10";

$res=mysqli_query($conn,$sql);

if(mysqli_num_rows($res)>0){
    while($row=mysqli_fetch_assoc($res)){
        foreach($row as $val){
            if(!empty($val))
                echo "<div onclick=\"fillBox('$val')\">$val</div>";
        }
    }
}else{
    echo "<div>No result</div>";
}
?>
