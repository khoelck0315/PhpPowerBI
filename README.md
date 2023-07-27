# PhpPowerBI
PhpPowerBI is a simple library for working with PowerBI embedded reports directly from PHP.  It leverages the [microsoft/PowerBI-Javascript](https://github.com/microsoft/PowerBI-JavaScript) powerbi.js file for the actual rendering of the reports.  It has been included in this repo for convenience, but can also be grabbed from the dist folder of the above repo.

# Dependencies
PhpPowerBI relies on [PhpAzureAuth](https://github.com/khoelck0315/PhpAzureAuth/tree/main) to generate auth tokens for Azure.  If you install via Composer, it will automatically install this as a dependency.
The above mentioned powerbi.js file is also required.

# Installation
The recommended way to install is via composer

### Composer install
```
composer require khoelck/phppowerbi
```

### Manual install
Copy the contents of the src folder to your include_path, and include the libraries in your authentication script as below:

```
require "includes/classes/PowerBI/PowerBIReport.php";
use Khoelck\PhpPowerBI\PowerBIReport;
```

# Initial Configuration
Before using this in your code, navigate to the install folder (vendor/khoelck/phppowerbi/src for composer installs) and copy the PowerBIConfig.php file to your include_path.

The following values will need to be configured for your app:
- Location of the powerbi.js file

Other options that can be changed in this file, depending on your scenario
- The PowerBI root API URL
- AzureConfig.php file location
- Directory to look in for reportsettings.json files

Each report you embed will need it's own JSON configuration file, based off of [these parameters](https://learn.microsoft.com/en-us/javascript/api/overview/powerbi/configure-report-settings) provided by Microsoft for calling the PowerBI API.  This should be placed in your include_path along with the PowerBIConfig.php file.  See EmbedExample.json for an example, but note, this will not be "well formed" JSON, as it is simply imported as a single string in the code for the API call.

# Use Case
You want to embed a PowerBI report in your PHP application - but you don't want to use the secure embed iframe option because it requires users to login.  It would be better to obtain an Azure Token at time of login, then call the PowerBI API to display the report.

Leverage the AzureAuth API in your authentication script, which stores the token in your $_SESSION variable.  Then, when calling the PowerBIRepot constructor, pass in the $_SESSION['Token'] variable to access and embed your report on the page.

See Example.php for proper usage.

# Report Styling
You will need to add CSS for the report container, however there are additional options that should be configured in the JSON file.  Many of the report options can be configured in the JSON configuration file for the report.

# Related packages
[PhpAzureAuth](https://github.com/khoelck0315/PhpAzureAuth/tree/main)
