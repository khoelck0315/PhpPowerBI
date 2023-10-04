# PhpPowerBI
PhpPowerBI is a simple library for working with PowerBI embedded reports directly from PHP.  It leverages the [microsoft/PowerBI-Javascript](https://github.com/microsoft/PowerBI-JavaScript) powerbi.js file for the actual rendering of the reports.  **It has not been included in this repo** and must be downloaded separately [here](https://github.com/microsoft/PowerBI-JavaScript/tree/master/dist).

# Dependencies
PhpPowerBI relies on [PhpAzureAuth](https://github.com/khoelck0315/PhpAzureAuth/tree/main) to generate auth tokens for Azure.  If you install via Composer, it will automatically install this as a dependency.
The above mentioned *powerbi.js* or *powerbi.min.js* file is also required.

# Installation
The recommended way to install is via composer

### Composer install
```
composer require khoelck/phppowerbi
```

[PhpPowerBI on Packagist](https://packagist.org/packages/khoelck/phppowerbi)

### Manual install
Copy the contents of the src folder to your include_path, and include the libraries in your authentication script as below:

```
require "includes/classes/PowerBI/PowerBIReport.php";
use Khoelck\PhpPowerBI\PowerBIReport;
```

# Initial Configuration
Each report you embed will need it's own JSON configuration file, based off of [these parameters](https://learn.microsoft.com/en-us/javascript/api/overview/powerbi/configure-report-settings) provided by Microsoft for calling the PowerBI API.  This should be placed in your include_path along with the PowerBIConfig.php file.  See EmbedExample.json for an example.

Be sure to include the *powerbi.js* or *powerbi.min.js* file in your page, before you call any methods from PHPPowerBI of course:
```
<script src="path/to/powerbi.js"></script>
```

# Use Case
You want to embed a PowerBI report in your PHP application - but you don't want to use the secure embed iframe option because it requires users to login.  It would be better to obtain an Azure Token at time of login, then call the PowerBI API to display the report.

Leverage the AzureAuth API in your authentication script, which stores the token in your $_SESSION variable.  Then, when calling the PowerBIRepot constructor, pass in the $_SESSION['Token'] variable to access and embed your report on the page.

# Embedding Reports
The constructor for PowerBIReport takes 4 parameters:
- Your Report ID GUID
- Your Workspace ID GUID
- The name of the JSON file containing settings that will be used to render the report
- The Azure token to be passed when accessing the report

```
<?php
use Khoelck\PhpPowerBI\PowerBIReport;

// Create a report, assuming you've already obtained a token and added it to your session variable
$report = new PowerBIReport("eb85896e-f6ce-4303-b357-b3d6e7232ca9", "9467ec51-dcab-40ef-b2c3-7a4f41f835a8", "ModalEmbed.json", $_SESSION['AzureAuth']['Token']);
?>
```

Now, you can call one of two methods to embed the report:

1. ShowReport - this will take one argument which is the element ID you wish to append the report to, and will render it automatically.
```
<div id="embeddedReport"></div>

<?php
  $report->ShowReport("embeddedReport");
?>
```

2. ShowConfig - this will simply create a global constant with your report configuration that contains all of the necessary information to pass directly to the powerbi.embed function.  The word Config will be appended to whatever you pass into this method for an parameter.  You may want to use this if you intend to call powerbi.bootstrap on something first.  For example, if you wanted to embed multiple reports in a modal or slideshow component, you could call powerbi.bootstrap on those elements, then call powerbi.embed when the reports should be shown.  This can add better loading times to your embedded reports.  Below is a very basic example:
   
```
<?php
  $report->ShowConfig("embeddedReport");
?>

<div id="embeddedReport"></div>
<button type="button" id="show">Show Report</button>

<script>
  powerbi.bootstrap(
    document.getElementbyId("embeddedReport"),
    {
      type: 'report',
      embedUrl: embeddedReportConfig.embedUrl
    }
  );

  const btn = document.getElementById('show');
  btn.addEventListener('click', function() {
    powerbi.embed(document.getElementById('embeddedReport'), embeddedReportConfig);
  });
</script>
```

# Report Styling
You will need to add CSS for the report container, however there are additional options that should be configured in the JSON file.  Many of the report options can be configured in the JSON configuration file for the report.

# Related packages
[PhpAzureAuth](https://github.com/khoelck0315/PhpAzureAuth/tree/main)
