<?php

require_once "includes/auth.php";
require_once "config/database.php";
require_once "includes/csrf.php";

$result = $conn->query("SELECT * FROM questions ORDER BY id");

$categories = [];

while($row = $result->fetch_assoc()){
    $categories[$row["category"]][] = $row;
}

$totalSteps = count($categories);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>CyberRisk Assessment</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

<h1>CyberRisk Assessment</h1>

<div class="card">

<h2>Security Awareness Check</h2>

<p class="section-desc">

Answer all six sections to view your CyberRisk profile.

</p>

<div class="status-row">

<span class="status-badge">24 questions</span>

<span class="status-badge">3–4 minutes</span>

<span class="status-badge">Secure assessment</span>

</div>

<div class="assessment-progress">

<div class="assessment-progress-fill" id="progressFill"></div>

</div>

<p style="text-align:center;margin-top:10px;">

Step

<span id="stepNumber">1</span>

of

<?=$totalSteps?>

</p>

</div>

<form method="POST" action="result.php" id="assessmentForm">

<input
type="hidden"
name="csrf_token"
value="<?=htmlspecialchars(csrf_token())?>">

<?php
$step=0;
foreach($categories as $category=>$questions):
$step++;
?>

<div class="step" <?= $step==1 ? '' : 'style="display:none;"' ?>>

<div class="card">

<h2>

<?=htmlspecialchars($category)?>

Security

</h2>

<p class="section-desc">

Section

<?=$step?>

of

<?=$totalSteps?>

</p>

</div>

<?php foreach($questions as $i=>$q): ?>

<div class="question">

<div class="question-number">

Question <?=$i+1?>

</div>

<h3>

<?=htmlspecialchars($q["question"])?>

</h3>

<label>

<input
type="radio"
name="q_<?=$q["id"]?>"
value="a"
required>

<?=htmlspecialchars($q["option_a"])?>

</label>

<label>

<input
type="radio"
name="q_<?=$q["id"]?>"
value="b">

<?=htmlspecialchars($q["option_b"])?>

</label>

<label>

<input
type="radio"
name="q_<?=$q["id"]?>"
value="c">

<?=htmlspecialchars($q["option_c"])?>

</label>

<label>

<input
type="radio"
name="q_<?=$q["id"]?>"
value="d">

<?=htmlspecialchars($q["option_d"])?>

</label>

</div>

<?php endforeach; ?>

<div class="nav-buttons">

<?php if($step>1): ?>

<button type="button" class="prevBtn">

Previous

</button>

<?php endif; ?>

<?php if($step<$totalSteps): ?>

<button type="button" class="nextBtn">

Next

</button>

<?php else: ?>

<button type="submit">

View profile

</button>

<?php endif; ?>

</div>

</div>

<?php endforeach; ?>

</form>

</div>

<script>

const steps=document.querySelectorAll(".step");

const nextBtns=document.querySelectorAll(".nextBtn");

const prevBtns=document.querySelectorAll(".prevBtn");

const progress=document.getElementById("progressFill");

const stepText=document.getElementById("stepNumber");

let current=0;

function updateProgress(){

const percent=((current+1)/steps.length)*100;

progress.style.width=percent+"%";

stepText.innerHTML=current+1;

}

function validateStep(){

const radios=steps[current].querySelectorAll('input[type="radio"]');

const groups={};

radios.forEach(r=>{

groups[r.name]=groups[r.name]||false;

if(r.checked) groups[r.name]=true;

});

return Object.values(groups).every(v=>v);

}

nextBtns.forEach(btn=>{

btn.addEventListener("click",()=>{

if(!validateStep()){

alert("Please answer all questions in this section.");

return;

}

steps[current].style.display="none";

current++;

steps[current].style.display="block";

window.scrollTo({top:0,behavior:"smooth"});

updateProgress();

});

});

prevBtns.forEach(btn=>{

btn.addEventListener("click",()=>{

steps[current].style.display="none";

current--;

steps[current].style.display="block";

window.scrollTo({top:0,behavior:"smooth"});

updateProgress();

});

});

updateProgress();

</script>

</body>

</html>