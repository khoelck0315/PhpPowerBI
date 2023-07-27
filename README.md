# PhpPowerBI
PhpPowerBI is a simple library for working with PowerBI embedded reports directly from PHP.  It leverages the [microsoft/PowerBI-Javascript](https://github.com/microsoft/PowerBI-JavaScript) powerbi.js file for the actual rendering of the reports.  It has been included in this repo, but is not necessarily the latest version.

# Dependencies
PhpPowerBI relies on [PhpAzureAuth](https://github.com/khoelck0315/PhpAzureAuth/tree/main) to generate auth tokens for Azure.  If you install via Composer, it will automatically install this as a dependency.

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

If you are installing manually, edit the PowerBIConfig.php file and specify the location of the AzureAuth.php file dependency.

# Use
This is designed to include on any page you wish to embed a PowerBI report.  See Example.php for proper usage.

# Related packages
[PhpAzureAuth](https://github.com/khoelck0315/PhpAzureAuth/tree/main)
