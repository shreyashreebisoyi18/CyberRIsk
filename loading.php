<?php
require_once "includes/auth.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CyberRisk Initializing</title>

<link rel="stylesheet" href="css/style.css">

<meta http-equiv="refresh" content="2;url=dashboard.php">

</head>

<body class="loading-body">

<div class="loading-container">

    <div class="loading-shield">
        🛡
    </div>

    <h1>CyberRisk</h1>

    <p id="loadingText">
        Initializing secure environment...
    </p>

    <div class="loading-bar">

        <div class="loading-fill"></div>

    </div>

    <div class="loading-status">

        <span>Security Scan</span>

        <span id="percent">0%</span>

    </div>

</div>

<script>

let percent=0;

const text=document.getElementById("loadingText");

const p=document.getElementById("percent");

const messages=[
"Initializing secure environment...",
"Verifying encrypted session...",
"Loading CyberRisk profile...",
"Scanning security posture...",
"Access granted."
];

let msg=0;

const timer=setInterval(()=>{

percent+=5;

p.innerHTML=percent+"%";

if(percent%25===0 && msg<messages.length-1){

msg++;

text.innerHTML=messages[msg];

}

if(percent>=100){

clearInterval(timer);

}

},100);

</script>

</body>

</html>