<?php

require_once "includes/auth.php";
require_once "config/database.php";
require_once "includes/csrf.php";

/* ---------- Security ---------- */

if (
    !isset($_POST["csrf_token"]) ||
    !verify_csrf_token($_POST["csrf_token"])
) {
    http_response_code(403);
    exit("Invalid request.");
}

$user_id = $_SESSION["user_id"];

/* ---------- Initialize Categories ---------- */

$categoryScores = [
    "Password" => 0,
    "Device" => 0,
    "Privacy" => 0,
    "Phishing" => 0,
    "Network" => 0,
    "Social" => 0
];

$categoryCounts = [
    "Password" => 0,
    "Device" => 0,
    "Privacy" => 0,
    "Phishing" => 0,
    "Network" => 0,
    "Social" => 0
];

/* ---------- Read Questions ---------- */

$result = $conn->query("SELECT * FROM questions");

while ($q = $result->fetch_assoc()) {

    $id = $q["id"];
    $answer = $_POST["q_" . $id] ?? null;

    if (!$answer) continue;

    $score = $q["score_" . $answer];
    $category = $q["category"];

    $categoryScores[$category] += $score;
    $categoryCounts[$category]++;
}

/* ---------- Convert to Percent ---------- */

$final = [];

foreach ($categoryScores as $cat => $score) {

    $max = $categoryCounts[$cat] * 10;
    $final[$cat] = round(($score / $max) * 100);
}

/* ---------- Overall ---------- */

$overall = round(array_sum($final) / count($final));

if ($overall >= 80) {

    $risk = "Low Risk";

} elseif ($overall >= 60) {

    $risk = "Moderate Risk";

} elseif ($overall >= 40) {

    $risk = "High Risk";

} else {

    $risk = "Critical Risk";
}

/* ---------- Save ---------- */

$stmt = $conn->prepare(
"INSERT INTO assessments
(user_id,password_score,device_score,privacy_score,
phishing_score,network_score,social_score,
overall_score,risk_level)
VALUES (?,?,?,?,?,?,?,?,?)"
);

$stmt->bind_param(
"iiiiiiiis",
$user_id,
$final["Password"],
$final["Device"],
$final["Privacy"],
$final["Phishing"],
$final["Network"],
$final["Social"],
$overall,
$risk
);

$stmt->execute();

/* ---------- Attack Path ---------- */

$attackPath=[];

if($final["Phishing"]<60 && $final["Password"]<60){

$attackPath=[
"Suspicious message appears",
"User clicks the link",
"Fake login page opens",
"Credentials could be exposed",
"Password reused elsewhere",
"Multiple accounts become vulnerable"
];

}
elseif($final["Privacy"]<60){

$attackPath=[
"Public profile information",
"Information aggregation",
"Targeted social-engineering attempt",
"Higher exposure to scams"
];

}
elseif($final["Network"]<60){

$attackPath=[
"Public Wi-Fi network",
"Sensitive activity performed",
"Traffic interception risk increases",
"Account exposure becomes more likely"
];

}
else{

$attackPath=[
"Your strongest security habits reduce common cyber risks."
];

}

/* ---------- History ---------- */

$historyStmt=$conn->prepare(
"SELECT overall_score,risk_level,created_at
FROM assessments
WHERE user_id=?
ORDER BY created_at DESC
LIMIT 5"
);

$historyStmt->bind_param("i",$user_id);
$historyStmt->execute();
$history=$historyStmt->get_result();

/* ---------- Icons ---------- */

$icons=[
"Password"=>"key",
"Device"=>"device",
"Privacy"=>"globe",
"Phishing"=>"mail",
"Network"=>"wifi",
"Social"=>"users"
];

?>
<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>CyberRisk Profile</title>

<link rel="stylesheet" href="css/style.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="container">

<h1>CyberRisk Profile</h1>

<div class="status-row">

<span class="status-badge">Assessment complete</span>

<span class="status-badge"><?= date("d M Y") ?></span>

<span class="status-badge"><?= htmlspecialchars($_SESSION["user_name"]) ?></span>

</div>

<div class="dashboard-grid">

<!-- SCORE CARD -->

<div class="card score-card">

<h2>Overall Score</h2>

<div class="score-circle">

<svg width="220" height="220">

<circle cx="110" cy="110" r="90" class="bg-circle"/>

<circle
cx="110"
cy="110"
r="90"
class="progress-circle <?= strtolower(str_replace(' ','-',$risk)) ?>"
style="stroke-dashoffset:<?=565-(565*$overall/100)?>;"
/>

</svg>

<div class="score-text">

<h1><?= $overall ?></h1>

<p>/100</p>

<strong class="risk-text <?= strtolower(str_replace(' ','-',$risk)) ?>">

<?= htmlspecialchars($risk) ?>

</strong>

</div>

</div>

<!-- Security Profile -->

<div class="cyber-avatar">

<div class="shield-container <?= strtolower(str_replace(' ','-',$risk)) ?>">

<div class="shield-core" aria-hidden="true"><svg class="icon icon-large"><use href="assets/cyberrisk-icons.svg#shield"></use></svg></div>

<div class="shield-ring"></div>

</div>

<h3 class="avatar-title">Security Profile</h3>

<p class="avatar-text">

Your score summarizes your current security habits.

</p>

<?php

if($overall>=80){

$threat="Minimal Exposure";
$badge="low-risk";

}
elseif($overall>=60){

$threat="Moderate Exposure";
$badge="moderate-risk";

}
elseif($overall>=40){

$threat="Elevated Exposure";
$badge="high-risk";

}
else{

$threat="Critical Exposure";
$badge="critical-risk";

}

?>

<div class="threat-panel">

<p class="threat-label">Live Threat Level</p>

<div class="threat-badge <?= $badge ?>">

<?= htmlspecialchars($threat) ?>

</div>

</div>

</div>

</div>

<!-- RADAR -->

<div class="card radar-card">

<h2>Your Security Shape</h2>

<canvas id="radarChart"></canvas>

</div>

</div>

<!-- SECURITY STATUS -->

<div class="card">

<h2>Security Status</h2>

<div class="status-grid">

<div class="mini-card">

<h3><?= count($final) ?></h3>

<p>Categories Checked</p>

</div>

<div class="mini-card">

<h3><?= max($final) ?>%</h3>

<p>Strongest Area</p>

</div>

<div class="mini-card">

<h3><?= min($final) ?>%</h3>

<p>Weakest Area</p>

</div>

<div class="mini-card">

<h3><?= $overall ?>%</h3>

<p>Overall Protection</p>

</div>

</div>

</div>

<!-- BREAKDOWN -->

<div class="card">

<h2>Security Breakdown</h2>

<p class="section-desc">

These scores represent your cybersecurity habits across six key areas.

</p>

<?php foreach($final as $cat=>$score): ?>

<div class="progress-item">

<div class="progress-header">

<span><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#<?= $icons[$cat] ?>"></use></svg> <?= htmlspecialchars($cat) ?></span>

<span class="score-label <?= $score<40?'danger':($score<70?'warning':'good') ?>">

<?= $score ?>%

</span>

</div>

<div class="progress-bar">

<div
class="progress-fill"
style="width:<?= $score ?>%;"
></div>

</div>

</div>

<?php endforeach; ?>

</div>

<!-- ATTACK PATH -->

<div class="card">

<h2>Risk Path</h2>

<p class="section-desc">

How gaps in your habits can increase exposure to common threats.

</p>

<div class="attack-flow">

<?php foreach($attackPath as $step): ?>

<div class="attack-step">

<?= htmlspecialchars($step) ?>

</div>

<?php endforeach; ?>

</div>

</div>

<!-- RECOMMENDATIONS -->

<div class="card">

<h2>Personalized Recommendations</h2>

<?php

if($final["Password"]<60)
echo "<p>Enable two-factor authentication and use unique passwords.</p>";

if($final["Privacy"]<60)
echo "<p>Limit personal information visible on public profiles.</p>";

if($final["Phishing"]<60)
echo "<p>Verify suspicious messages before opening links.</p>";

if($final["Network"]<60)
echo "<p>Avoid sensitive activity on public Wi-Fi.</p>";

if($final["Device"]<60)
echo "<p>Install device updates promptly.</p>";

if($final["Social"]<60)
echo "<p>Review your social media privacy settings.</p>";

?>

</div>

<!-- STRENGTHS -->

<div class="card">

<h2>Security Strengths</h2>

<?php

foreach($final as $cat=>$score){

if($score>=80){

echo "<p>Excellent ".$cat." security practices.</p>";

}

}

?>

</div>

<!-- HISTORY -->

<div class="card">

<h2>Recent Assessments</h2>

<p class="section-desc">

Track how your CyberRisk score changes over time.

</p>

<table class="history-table">

<tr>

<th>Date</th>
<th>Score</th>
<th>Risk</th>

</tr>

<?php while($row=$history->fetch_assoc()): ?>

<tr>

<td><?= date("d M Y",strtotime($row["created_at"])) ?></td>

<td><?= $row["overall_score"] ?>/100</td>

<td><?= htmlspecialchars($row["risk_level"]) ?></td>

</tr>

<?php endwhile; ?>

</table>

</div>

<button onclick="location.href='download_report.php'">
    Download report
</button>

<button onclick="location.href='dashboard.php'">
    Dashboard
</button>

</div>

<!-- ================= REPORT HELP ================= -->

<button id="chatButton" class="chat-button" type="button" aria-label="Open report help">
<svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#message"></use></svg>
</button>

<div id="chatWindow" class="chat-window">

<div class="chat-header">

Report Help

<button id="closeChat" type="button" aria-label="Close report help">Close</button>

</div>

<div class="chat-body" id="chatBody">

<div class="bot-message">

Review your results or choose a topic below.

</div>

</div>

<div class="chat-options">

<button class="chat-option">

Score details

</button>

<button class="chat-option">

Password tips

</button>

<button class="chat-option">

Phishing basics

</button>

<button class="chat-option">

Lower my risk

</button>

</div>

</div>

<script>

Chart.defaults.color="#9c9c9d";
Chart.defaults.borderColor="#363739";

new Chart(

document.getElementById("radarChart"),

{

type:"radar",

data:{

labels:[
"Password",
"Device",
"Privacy",
"Phishing",
"Network",
"Social"
],

datasets:[{

label:"CyberRisk Profile",

data:[

<?= $final["Password"] ?>,

<?= $final["Device"] ?>,

<?= $final["Privacy"] ?>,

<?= $final["Phishing"] ?>,

<?= $final["Network"] ?>,

<?= $final["Social"] ?>

],

borderColor:"#ff6363",

backgroundColor:"rgba(255,99,99,.16)",

pointBackgroundColor:"#e6e6e6",
pointBorderColor:"#ff6363"

}]

},

options:{

responsive:true,

plugins:{
legend:{
labels:{color:"white"}
}
},

scales:{
r:{
min:0,
max:100,
ticks:{display:false},
grid:{color:"#363739"},
angleLines:{color:"#363739"},
pointLabels:{color:"#e6e6e6"}
}
}

}

}

);

</script>

<script>

const weakestCategory="<?=array_keys($final,min($final))[0]?>";

const overallScore=<?=$overall?>;

const answers={

"Score details":

overallScore>=80

?"Your habits are already strong. Keep maintaining them."

:"Your weakest area is "+weakestCategory+". Improving that category first will increase your overall score the fastest.",

"Password tips":

"Use unique passwords, enable 2FA, and consider a password manager.",

"Phishing basics":

"Phishing is when attackers trick people into revealing passwords or personal information through fake emails, messages, or websites.",

"Lower my risk":

"Focus on improving your weakest category, keep devices updated, avoid suspicious links, and use strong passwords."

};

const chatButton=document.getElementById("chatButton");

const chatWindow=document.getElementById("chatWindow");

const closeChat=document.getElementById("closeChat");

const body=document.getElementById("chatBody");

chatButton.onclick=()=>{

chatWindow.style.display="flex";

};

closeChat.onclick=()=>{

chatWindow.style.display="none";

};

document.querySelectorAll(".chat-option").forEach(btn=>{

btn.onclick=()=>{

body.innerHTML+=`<div class="user-message">${btn.innerText}</div>`;

setTimeout(()=>{

body.innerHTML+=`<div class="bot-message">${answers[btn.innerText]}</div>`;

body.scrollTop=body.scrollHeight;

},400);

};

});

</script>

</body>

</html>