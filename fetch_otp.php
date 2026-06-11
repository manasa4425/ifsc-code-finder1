<?php
session_start();
echo isset($_SESSION['otp']) ? $_SESSION['otp'] : "";
?>