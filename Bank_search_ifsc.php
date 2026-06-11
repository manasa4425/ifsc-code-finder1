<?php
include "db.php";

/* Fetch all banks with IFSC for suggestions */
$banks = [];
$res = mysqli_query($conn, "SELECT name, ifsc FROM bank ORDER BY name ASC");
while($row = mysqli_fetch_assoc($res)){
    $banks[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Search IFSC | Bank IFSC</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{box-sizing:border-box}
body{
  font-family:'Nunito',sans-serif;
  background:linear-gradient(135deg,#0b2b5b,#0f4d8b,#0a7cff);
  min-height:100vh;
}
.header{
  display:flex; align-items:center; padding:15px 20px; background:#06283D; position:relative;
}
.three-dots{ font-size:22px; cursor:pointer; margin-right:15px; }
.logo{ font-size:22px; font-weight:bold; color:#fff; }
.dropdown{ position:absolute; top:60px; left:20px; width:230px;
  background:#0f4c75; border-radius:8px; display:none;
  box-shadow:0 8px 20px rgba(0,0,0,0.3); overflow:hidden; z-index:1000;
}
.dropdown a{ display:flex; align-items:center; gap:10px;
  padding:14px 16px; color:#fff; text-decoration:none;
  border-bottom:1px solid rgba(255,255,255,0.15);
}
.dropdown a:hover{ background:#3282b8; }
.dropdown a:last-child{ border-bottom:none; }

.container{
  width:95%; max-width:800px; margin:60px auto;
  background:rgba(255,255,255,0.10); padding:30px; border-radius:16px;
  backdrop-filter:blur(10px); box-shadow:0 25px 60px rgba(0,0,0,.45);
  position:relative;
}
.title{ color:#fff; text-align:center; margin-bottom:20px; }

.form{ display:flex; gap:10px; position:relative; }
.search-box{ width:75%; position:relative; }
.search-box input{
  width:100%; padding:14px 40px 14px 14px;
  border-radius:12px; border:1px solid rgba(255,255,255,.25);
  background:rgba(255,255,255,.08); color:#fff; outline:none;
}
.arrow{ position:absolute; right:12px; top:50%; transform:translateY(-50%);
  font-size:14px; cursor:pointer; color:#ccc;
}
.arrow:hover{ color:#fff; }

input[type=submit]{
  width:25%; padding:14px; border-radius:12px; border:none;
  background:linear-gradient(90deg,#00d4ff,#1a9bff);
  color:#fff; font-weight:700; cursor:pointer;
}

.suggestions{
  position:absolute; top:105%; left:0; width:100%;
  background:#0f3d73; border-radius:10px; max-height:180px;
  overflow-y:auto; display:none; z-index:10;
}
.suggestions div{ padding:10px; cursor:pointer; color:#fff; }
.suggestions div:hover{ background:#1a6cff; }

.result{
  margin-top:20px;
  background:rgba(255,255,255,.08);
  padding:15px; border-radius:12px; color:#fff;
  text-align:center; font-weight:600;
}
</style>
</head>

<body>
<?php 
include "banktabs.html";
?>
<!-- HEADER -->



<div class="container">
  <div class="title"><h1>Search IFSC</h1></div>
  <!-- Back Button -->


  <form method="post" class="form">
    <div class="search-box">
      <input type="text" name="bankname" id="bankInput" placeholder="Enter Bank Name" autocomplete="off" required>
      <div class="arrow" onClick="nextBank()">▼</div>
      <div class="suggestions" id="suggestions"></div>
    </div>
    <input type="submit" name="search" value="Search">
  </form>

<?php
if(isset($_POST['search'])){
    $name = mysqli_real_escape_string($conn, $_POST['bankname']);
    $query = mysqli_query($conn, "SELECT ifsc FROM bank WHERE name='$name'");
    
    if(mysqli_num_rows($query) > 0){
        $row = mysqli_fetch_assoc($query);
        echo "<div class='result'>IFSC Code: <b>{$row['ifsc']}</b></div>";
    } else {
        echo "<div class='result'>Bank not found!</div>";
    }
}
?>

</div>

<script>
function toggleMenu(){
    const menu = document.getElementById("menu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}
document.addEventListener("click", function(e){
    const menu = document.getElementById("menu");
    const dots = document.querySelector(".three-dots");
    if(!menu.contains(e.target) && !dots.contains(e.target)){
        menu.style.display = "none";
    }
});

/* Suggestions & next button */
const banks = <?php echo json_encode($banks); ?>;
let index = -1;

const input = document.getElementById("bankInput");
const sug = document.getElementById("suggestions");

input.addEventListener("input", ()=>{
  const v = input.value.toLowerCase();
  sug.innerHTML="";
  if(v.length < 1){ sug.style.display="none"; return; }
  banks.filter(b=>b.name.toLowerCase().startsWith(v)).forEach(b=>{
    let d = document.createElement("div");
    d.innerText = b.name;
    d.onclick = ()=>{ input.value=b.name; sug.style.display="none"; };
    sug.appendChild(d);
  });
  sug.style.display="block";
});

function nextBank(){
  if(banks.length===0) return;
  index = (index + 1) % banks.length;
  input.value = banks[index].name;
  sug.style.display="none";
}

document.addEventListener("click", e=>{
  if(!e.target.closest(".search-box")) sug.style.display="none";
});
</script>

</body>
</html>
