<?php declare(strict_types=1);
namespace Khoelck\PhpPowerBI {

    final class PowerBIConfig {
        /**
         * The root URL for calling the PowerBI API.  This should not need to change.
         * https://learn.microsoft.com/en-us/rest/api/power-bi/
         */
        public static string $PowerBIApiRoot = "https://api.powerbi.com/v1.0/myorg/";
	
       /**
        *  Specify the relative path of the reportsettings.json files.
        */
       public static string $directory = __DIR__;
       
       
        /**
         * This loads the chunk of JSON as a string that will be loaded into the PowerBIReport/ShowReport method.  This specifies certain
         * settings for the report that can be customized.
         * This file should be placed in the same directory as this file.
         * More info https://learn.microsoft.com/en-us/javascript/api/overview/powerbi/configure-report-settings
         * @param string settings - specify the filename of the json file that should be used for the PowerBI report.
         */
        
        public static function GetReportSettings(string $settings): string {
            return file_get_contents(Self::$directory."/$settings");
        }
    }

}
