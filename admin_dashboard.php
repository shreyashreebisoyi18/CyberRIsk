<?php

require_once "includes/auth.php";
require_once "config/database.php";

if(!isset($_SESSION["role"]) || $_SESSION["role"]!="admin"){
    header("Location: dashboard.php");
    exit;
}

/* ------------------ METRICS ------------------ */

$totalStudents=$conn->query(
"SELECT COUNT(*) total FROM users WHERE role='student'"
)->fetch_assoc()["total"] ?? 0;

$totalAssessments=$conn->query(
"SELECT COUNT(*) total FROM assessments"
)->fetch_assoc()["total"] ?? 0;

$avgScore=$conn->query(
"SELECT ROUND(AVG(overall_score)) avg FROM assessments"
)->fetch_assoc()["avg"] ?? 0;

/* ---------- Average Category Scores ---------- */

$categoryResult=$conn->query(
"SELECT
ROUND(AVG(password_score)) Password,
ROUND(AVG(device_score)) Device,
ROUND(AVG(privacy_score)) Privacy,
ROUND(AVG(phishing_score)) Phishing,
ROUND(AVG(network_score)) Network,
ROUND(AVG(social_score)) Social
FROM assessments"
);

$category=$categoryResult->fetch_assoc();

if(!$category){
$category=[
"Password"=>0,
"Device"=>0,
"Privacy"=>0,
"Phishing"=>0,
"Network"=>0,
"Social"=>0
];
}

$weakest="Password";
$lowest=101;

foreach($category as $key=>$value){
$value=(int)$value;
if($value<$lowest){
$lowest=$value;
$weakest=$key;
}
}

/* ---------- Risk Distribution ---------- */

$riskData=[
"Low Risk"=>0,
"Moderate Risk"=>0,
"High Risk"=>0,
"Critical Risk"=>0
];

$riskQuery=$conn->query(
"SELECT risk_level,COUNT(*) total
FROM assessments
GROUP BY risk_level"
);

while($r=$riskQuery->fetch_assoc()){
$riskData[$r["risk_level"]]=$r["total"];
}

/* ---------- Trend ---------- */

$trendLabels=[];
$trendScores=[];

$trendQuery=$conn->query(
"SELECT DATE(created_at) day,
ROUND(AVG(overall_score)) avg
FROM assessments
GROUP BY DATE(created_at)
ORDER BY day"
);

while($t=$trendQuery->fetch_assoc()){
$trendLabels[]=$t["day"];
$trendScores[]=(int)$t["avg"];
}

/* ---------- SOC ALERT ---------- */

$alertQuery = $conn->query(
"SELECT COUNT(DISTINCT user_id) total
 FROM assessments
 WHERE risk_level IN ('High Risk','Critical Risk')"
);

$socAlert = $alertQuery->fetch_assoc()["total"] ?? 0;

/* ---------- Student Table ---------- */

$students=$conn->query(
"SELECT
users.name,
users.email,
assessments.overall_score,
assessments.risk_level,
assessments.created_at
FROM assessments
JOIN users
ON users.id=assessments.user_id
ORDER BY assessments.created_at DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Faculty Analytics Dashboard</title>

<link rel="stylesheet" href="css/style.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="container admin-container">

<h1>Faculty Analytics Dashboard</h1>

<div class="status-row">

<span class="status-badge"><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#shield"></use></svg> Admin access</span>

<span class="status-badge"><?=date("d M Y")?></span>

<span class="status-badge"><?=htmlspecialchars($_SESSION["user_name"])?></span>

</div>

<div class="card soc-alert-card">

<?php if($socAlert>0): ?>

    <div class="soc-warning">

        <div class="soc-icon"><svg class="icon icon-large" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#alert"></use></svg></div>

        <div>

            <h2>SOC Alert</h2>

            <p>

                <strong><?= $socAlert ?></strong>

                student<?= $socAlert>1?"s":"" ?>

                currently have High or Critical Risk profiles and may need cybersecurity awareness training.

            </p>

        </div>

    </div>

<?php else: ?>

    <div class="soc-safe">

        <div class="soc-icon"><svg class="icon icon-large" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#shield"></use></svg></div>

        <div>

            <h2>SOC Status</h2>

            <p>

                No students currently require urgent cybersecurity intervention.

            </p>

        </div>

    </div>

<?php endif; ?>

</div>

<!-- METRICS -->

<div class="status-grid">

<div class="mini-card">
<h3><?=$totalStudents?></h3>
<p>Students</p>
</div>

<div class="mini-card">
<h3><?=$totalAssessments?></h3>
<p>Assessments</p>
</div>

<div class="mini-card">
<h3><?=$avgScore?></h3>
<p>Average Score</p>
</div>

<div class="mini-card">
<h3><?=$weakest?> (<?=$lowest?>%)</h3>
<p>Weakest Area</p>
</div>

</div>

<!-- CHARTS -->

<div class="dashboard-grid">

<div class="card">

<h2>Risk Distribution</h2>

<canvas id="pieChart"></canvas>

</div>

<div class="card">

<h2>Campus Trend</h2>

<canvas id="lineChart"></canvas>

</div>

</div>

<div class="card">

<h2>Average Security Category Scores</h2>

<canvas id="barChart"></canvas>

</div>

<!-- SEARCH -->

<div class="card">

<h2>Student Records</h2>

<input
id="search"
placeholder="Search by name or email..."
onkeyup="searchTable()"
>

<table class="history-table" id="studentTable">

<tr>

<th>Name</th>
<th>Email</th>
<th>Score</th>
<th>Risk</th>
<th>Date</th>

</tr>

<?php while($row=$students->fetch_assoc()): ?>

<tr>

<td><?=htmlspecialchars($row["name"])?></td>

<td><?=htmlspecialchars($row["email"])?></td>

<td><?=$row["overall_score"]?>/100</td>

<td>

<?php

$class=strtolower(str_replace(" ","-",$row["risk_level"]));

?>

<span class="risk-pill <?=$class?>">

<?=htmlspecialchars($row["risk_level"])?>

</span>

</td>

<td><?=date("d M Y",strtotime($row["created_at"]))?></td>

</tr>

<?php endwhile; ?>

</table>

</div>

<button onclick="location.href='export.php'">

Download CSV

</button>

<button onclick="location.href='dashboard.php'">

Dashboard

</button>

</div>

<script>

function searchTable(){

let filter=document.getElementById("search").value.toUpperCase();

let rows=document.getElementById("studentTable").getElementsByTagName("tr");

for(let i=1;i<rows.length;i++){

rows[i].style.display=
rows[i].innerText.toUpperCase().includes(filter)
?"":"none";

}

}

/* PIE */

Chart.defaults.color="#9c9c9d";
Chart.defaults.borderColor="#363739";

new Chart(

document.getElementById("pieChart"),

{

type:"pie",

data:{

labels:["Low","Moderate","High","Critical"],

datasets:[{

data:[
<?=$riskData["Low Risk"]?>,
<?=$riskData["Moderate Risk"]?>,
<?=$riskData["High Risk"]?>,
<?=$riskData["Critical Risk"]?>
],
backgroundColor:["#59d499","#9c9c9d","#f2a36b","#ff6363"],
borderColor:"#07080a",
borderWidth:2

}]

}

}

);

/* LINE */

new Chart(

document.getElementById("lineChart"),

{

type:"line",

data:{

labels:<?=json_encode($trendLabels)?>,

datasets:[{

label:"Average Score",

data:<?=json_encode($trendScores)?>,

fill:true,

tension:.35,
borderColor:"#ff6363",
backgroundColor:"rgba(255,99,99,.12)",
pointBackgroundColor:"#e6e6e6",
pointBorderColor:"#ff6363"

}]

}

}

);

/* BAR */

new Chart(

document.getElementById("barChart"),

{

type:"bar",

data:{

labels:["Password","Device","Privacy","Phishing","Network","Social"],

datasets:[{

label:"Average Score",

data:[
<?=$category["Password"]?:0?>,
<?=$category["Device"]?:0?>,
<?=$category["Privacy"]?:0?>,
<?=$category["Phishing"]?:0?>,
<?=$category["Network"]?:0?>,
<?=$category["Social"]?:0?>
],
backgroundColor:"rgba(255,99,99,.72)",
borderColor:"#ff6363",
borderWidth:1

}]

}

}

);

</script>

</body>

</html>