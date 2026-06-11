<?php
session_start();
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
echo $otp; // this will return OTP to JS
?>