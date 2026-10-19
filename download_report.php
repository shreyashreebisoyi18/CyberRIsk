<?php

require_once "includes/auth.php";
require_once "config/database.php";
require_once __DIR__ . "/fpdf/fpdf.php";

$user_id = $_SESSION["user_id"];

/* Latest Assessment */

$stmt = $conn->prepare("
SELECT *
FROM assessments
WHERE user_id=?
ORDER BY created_at DESC
LIMIT 1
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$data=$stmt->get_result()->fetch_assoc();

if(!$data){
    exit("No assessment found.");
}

/* Categories */

$categories=[
"Password"=>$data["password_score"],
"Device"=>$data["device_score"],
"Privacy"=>$data["privacy_score"],
"Phishing"=>$data["phishing_score"],
"Network"=>$data["network_score"],
"Social"=>$data["social_score"]
];

/* Cyber Twin */

$weakest=array_keys($categories,min($categories))[0];

switch($weakest){

case "Password":
$twin="Guardian Lock";
$summary="Your biggest opportunity is strengthening passwords and enabling Two-Factor Authentication.";
break;

case "Device":
$twin="Device Defender";
$summary="Keeping devices updated will significantly improve your protection.";
break;

case "Privacy":
$twin="Privacy Shield";
$summary="Reducing public exposure online will improve your security.";
break;

case "Phishing":
$twin="Link Detective";
$summary="Learning to verify suspicious links before clicking will strengthen your score.";
break;

case "Network":
$twin="Safe Navigator";
$summary="Avoiding risky public Wi-Fi will improve your cyber safety.";
break;

default:
$twin="Social Sentinel";
$summary="Being cautious with unknown profiles will strengthen your online safety.";

}

/* ---------- PDF ---------- */

$pdf=new FPDF();
$pdf->SetAutoPageBreak(true,20);

/* PAGE 1 */

$pdf->AddPage();

/* Header */

$pdf->SetFillColor(15,23,42);
$pdf->Rect(0,0,210,50,'F');

$pdf->SetTextColor(255,255,255);
$pdf->SetFont("Arial","B",24);
$pdf->SetY(15);
$pdf->Cell(0,12,"CYBERRISK AUDIT REPORT",0,1,"C");

$pdf->SetFont("Arial","",12);
$pdf->Cell(0,8,"Personal Cybersecurity Assessment",0,1,"C");

/* Student Card */

$pdf->SetFillColor(245,248,252);
$pdf->Rect(20,65,170,45,'F');

$pdf->SetTextColor(20,20,20);
$pdf->SetFont("Arial","B",13);

$pdf->SetXY(30,75);
$pdf->Cell(0,8,"Student: ".$_SESSION["user_name"]);

$pdf->SetXY(30,85);
$pdf->Cell(0,8,"Date: ".date("d M Y",strtotime($data["created_at"])));

$pdf->SetXY(30,95);
$pdf->Cell(0,8,"Assessment ID: CR-".str_pad($user_id,4,"0",STR_PAD_LEFT));

/* Score Box */

$pdf->SetFillColor(37,99,235);
$pdf->Rect(55,125,100,35,'F');

$pdf->SetTextColor(255,255,255);
$pdf->SetFont("Arial","B",22);

$pdf->SetXY(55,135);
$pdf->Cell(100,10,$data["overall_score"]."/100",0,1,"C");

$pdf->SetFont("Arial","",11);

$pdf->SetXY(55,147);
$pdf->Cell(100,8,"Overall CyberRisk Score",0,1,"C");

/* Risk Badge */

$pdf->SetY(175);

switch($data["risk_level"]){

case "Low Risk":
$pdf->SetFillColor(34,197,94);
break;

case "Moderate Risk":
$pdf->SetFillColor(59,130,246);
break;

case "High Risk":
$pdf->SetFillColor(249,115,22);
break;

default:
$pdf->SetFillColor(239,68,68);

}

$pdf->SetFont("Arial","B",15);
$pdf->Cell(0,12,"Risk Level: ".$data["risk_level"],0,1,"C",true);

/* Cyber Twin */

$pdf->Ln(12);

$pdf->SetTextColor(20);
$pdf->SetFont("Arial","B",16);
$pdf->Cell(0,10,"Cyber Twin Profile",0,1);

$pdf->SetFillColor(235,245,255);
$pdf->Rect(20,$pdf->GetY(),170,28,'F');

$pdf->SetXY(25,$pdf->GetY()+4);

$pdf->SetFont("Arial","B",13);
$pdf->Cell(0,8,$twin);

$pdf->Ln(8);

$pdf->SetX(25);

$pdf->SetFont("Arial","",11);
$pdf->MultiCell(155,6,$summary);

/* PAGE 2 */

$pdf->AddPage();

/* Security Breakdown */

$pdf->SetFont("Arial","B",16);
$pdf->Cell(0,10,"Security Breakdown",0,1);

foreach($categories as $cat=>$score){

$pdf->SetFont("Arial","B",11);
$pdf->Cell(45,8,$cat);

$pdf->SetFillColor(220,226,235);
$pdf->Rect(60,$pdf->GetY()+2,100,5,'F');

$pdf->SetFillColor(37,99,235);
$pdf->Rect(60,$pdf->GetY()+2,$score,5,'F');

$pdf->Cell(25,8,$score."%",0,1);

}

/* Attack Path */

$pdf->Ln(8);

$pdf->SetFont("Arial","B",15);
$pdf->Cell(0,10,"Attack Path Simulation",0,1);

$pdf->SetFont("Arial","",11);

if($data["phishing_score"]<60){

$pdf->MultiCell(0,7,"Suspicious message -> Fake login page -> Credentials could be exposed.");

}elseif($data["network_score"]<60){

$pdf->MultiCell(0,7,"Public Wi-Fi -> Sensitive activity -> Untrusted network risk increases.");

}else{

$pdf->MultiCell(0,7,"Your strongest security habits reduce common cyber threats.");

}

/* Recommendations */

$pdf->Ln(6);

$pdf->SetFont("Arial","B",15);
$pdf->Cell(0,10,"Personalized Recommendations",0,1);

$pdf->SetFont("Arial","",11);

foreach($categories as $cat=>$score){

if($score<60){

$pdf->MultiCell(0,7,"• Improve ".$cat." security habits.");

}

}

$pdf->Ln(5);

/* Footer */

$pdf->SetY(-18);

$pdf->SetDrawColor(180);
$pdf->Line(20,279,190,279);

$pdf->SetFont("Arial","I",9);
$pdf->SetTextColor(120);

$pdf->Cell(0,10,"Generated securely by CyberRisk Platform | Confidential",0,0,"C");

$pdf->Output("D","CyberRisk_Audit_Report.pdf");
exit;