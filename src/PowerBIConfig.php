<?php declare(strict_types=1);
namespace Khoelck\PhpPowerBI {

    final class PowerBIConfig {
        /**
         * Specify the relative location on the webserver of the powerbi.js file to the document root.  Make sure to copy this file from the PowerBI-JavaScript
         * repository dist folder.
         * https://github.com/microsoft/PowerBI-JavaScript
         */
        public static string $PowerBI_JS = "/js/modules/powerbi.js";

        /**
         * The root for calling the PowerBI API.  This should not need to change.
         * https://learn.microsoft.com/en-us/rest/api/power-bi/
         */
        public static string $PowerBIApiRoot = "https://api.powerbi.com/v1.0/myorg/";

        /**
         * This loads the chunk of JSON as a string that will be loaded into the PowerBIReport/ShowReport method.  This specifies certain
         * settings for the report that can be customized.
         * This file should be placed in the same directory as this file.
         * More info https://learn.microsoft.com/en-us/javascript/api/overview/powerbi/configure-report-settings
         * @param string settings - specify the filename of the json file that should be used for the PowerBI report.
         */

        /**
         * Specify the relative path to the AzureAuth.php file for use in refresh tokens
         */
        public static string $AzureAuth = __DIR__."../PhpAzureAuth/AzureAuth.php";


        public static function GetReportSettings(string $settings): string {
            return file_get_contents(__DIR__."/$settings");
        }
    }

}
