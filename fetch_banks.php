<?php

include "db.php";

/* Fetch all bank names into array */
$banks = [];
$res = mysqli_query($conn, "SELECT name FROM bank ORDER BY name ASC");
while($r = mysqli_fetch_assoc($res)){
    $banks[] = $r['name'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Search IFSC</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700&display=swap" rel="stylesheet">

<style>
body{
  font-family: 'Nunito', sans-serif;
  background: linear-gradient(135deg,#0b2b5b,#0f4d8b,#0a7cff);
  min-height:100vh;
}

.container{
  max-width:700px;
  margin:60px auto;
  background:rgba(255,255,255,0.12);
  padding:30px;
  border-radius:16px;
  color:#fff;
  box-shadow:0 20px 50px rgba(0,0,0,.4);
}

h2{text-align:center;margin-bottom:20px;}

.search-wrap{
  position:relative;
}

.search-wrap input{
  width:100%;
  padding:14px 90px 14px 14px;
  border-radius:12px;
  border:none;
  outline:none;
  background:rgba(255,255,255,0.15);
  color:#fff;
  font-size:16px;
}

.arrows{
  position:absolute;
  right:50px;
  top:50%;
  transform:translateY(-50%);
  display:flex;
  flex-direction:column;
}

.arrows button{
  width:30px;
  height:22px;
  border:none;
  background:rgba(255,255,255,0.25);
  color:#fff;
  cursor:pointer;
  border-radius:4px;
  margin:1px 0;
}

.search-btn{
  position:absolute;
  right:10px;
  top:50%;
  transform:translateY(-50%);
  padding:8px 14px;
  border:none;
  border-radius:8px;
  background:#00d4ff;
  color:#000;
  font-weight:700;
  cursor:pointer;
}

.result{
  margin-top:25px;
  padding:15px;
  background:rgba(255,255,255,0.1);
  border-radius:12px;
}
</style>
</head>

<body>

<div class="container">
<h2>Search IFSC</h2>

<form method="post">
  <div class="search-wrap">
    <input type="text" id="bankname" name="bankname"
           placeholder="Enter bank name or use arrows"
           autocomplete="off" required>

    <div class="arrows">
      <button type="button" onclick="prevBank()">▲</button>
      <button type="button" onclick="nextBank()">▼</button>
    </div>

    <button class="search-btn" type="submit" name="search">Search</button>
  </div>
</form>

<?php
if(isset($_POST['search'])){
  $name = $_POST['bankname'];
  $q = mysqli_query($conn,"SELECT * FROM bank WHERE name='$name'");
  if(mysqli_num_rows($q)>0){
    echo "<div class='result'><b>IFSC Codes:</b><br>";
    while($r=mysqli_fetch_assoc($q)){
      echo $r['ifsc']."<br>";
    }
    echo "</div>";
  } else {
    echo "<div class='result'>No bank found</div>";
  }
}
?>
</div>

<script>
let banks = <?php echo json_encode($banks); ?>;
let index = -1;

function nextBank(){
  if(banks.length===0) return;
  index++;
  if(index >= banks.length) index = 0;
  document.getElementById("bankname").value = banks[index];
}

function prevBank(){
  if(banks.length===0) return;
  index--;
  if(index < 0) index = banks.length - 1;
  document.getElementById("bankname").value = banks[index];
}
</script>

</body>
</html>