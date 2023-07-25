<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <div id="embeddedReport"></div>
</body>
</html>

<?php
require "includes/classes/AzureAuth/Scope.php";
require "includes/classes/AzureAuth/AzureAuth.php";
require "includes/classes/PowerBI/PowerBIReport.php";
use Khoelck\PhpAzureAuth\AzureAuth;
use Khoelck\PhpAzureAuth\Scope;
use Khoelck\PhpPowerBI\PowerBIReport;

$azureAuth = new AzureAuth("kevin.hoelck@nlcinsurance.com", password, Scope::$PowerBI);

// Verify the token was received
// var_dump($azureAuth->Token);

// Create a report now

$report = new PowerBIReport("8200d459-b289-40a8-9e1b-a889fd0a7186", "9467ec41-dcab-40ef-b2c3-7a4f41f835a8","MainPageEmbed.json", $azureAuth->Token);
$report->ShowReport("embeddedReport");

?>
