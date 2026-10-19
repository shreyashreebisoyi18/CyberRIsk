<?php

require_once "includes/auth.php";
require_once "config/database.php";

if($_SESSION["role"]!="admin"){
exit("Unauthorized");
}

header("Content-Type:text/csv");
header("Content-Disposition:attachment; filename=CyberRisk_Report.csv");

$output=fopen("php://output","w");

fputcsv($output,[
"Student",
"Email",
"Score",
"Risk",
"Date"
]);

$result=$conn->query(

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

while($row=$result->fetch_assoc()){

fputcsv($output,$row);

}

fclose($output);
exit;