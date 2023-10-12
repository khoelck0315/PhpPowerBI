<?php declare(strict_types=1);
namespace Khoelck\PhpPowerBI {
    require "AzureApiCall.php";
    use Khoelck\PhpAzureAuth\AzureAuth;
    use stdClass;


    final class PowerBIReport extends AzureApiCall {

           /** 
         * To create an object from this class and embed a report, pass the following parameters.  These can be easily obtained from the
         * PowerBI app URL.
         * @param string reportId 
         * @param string workspaceId 
         *      Navigate to the report in PowerBI and copy the GUIDs from the URL.  The are in the below locations
         *      https://app.powerbi.com/groups/<WORKSPACE ID>/reports/<REPORT ID>/ReportSection?experience=power-bi
         * @param string settings - Reference to a JSON file containing the individual report embedding settings.  
         *      https://learn.microsoft.com/en-us/javascript/api/overview/powerbi/configure-report-settings
         * @param stdClass token - A valid Azure auth token generated by the AzureAuth class
         *      <github repo url>
         */
        public function __construct(string $reportId, string $workspaceId, string $settings, stdClass $token) {
            $this->ReportID = $reportId;
            $this->WorkspaceID = $workspaceId;
            $this->ReportSettings = $this->GetReportSettings($settings);
        
            if(!$this->ValidateToken()) {
                $this->HasToken = false;
            }
            else {
                $this->HasToken = true;
                $this->Token = $token;
            }    
        }
        
        /**
         * To append the report to the document, create a parent element and give it an id that will be passed as an 
         * argument to this function.
         * @param string reportid - The HTML attribute ID of the parent element that will contain the report.
         */
        public function ShowReport(string $reportid): void {
            if($this->HasToken) {
                $reportData = $this->GetReportData();
                if(!isset($reportData->error)) {
                    echo "
                    <script>
                        (function() {
                            const embedConfiguration = {
                                \"type\": 'report',
                                \"accessToken\": '". $this->Token->access_token ."',
                                \"id\": '". $this->ReportID."',
                                \"embedUrl\": '".$reportData->embedUrl."',".
                                $this->ReportSettings.
                            "};
                            
                            const reportContainer = document.getElementById('".$reportid."');
                            const report = powerbi.embed(reportContainer, embedConfiguration);
                        })();
                    </script>
                    ";
                }
                else {
                    $this->OutputErrorToConsole(($reportData->error));
                    error_log($reportData->error);
                }
            }
            else {
                error_log("Token was not passed to report constructor.  Unable to retrieve reports.");
            }
        }

        /**
         * Instead of appending the report to the document with this call, simply output the report configuration including the access
         * token.  This option can be used if custom JavaScript is going to be used to append the reports intead, but makes the needed information
         * available to your custom function.
         * @param string reportid - A unique name for the config that will be referenced by your custom function.
         */
        public function ShowConfig(string $reportid): void {
            if($this->HasToken) {
                $reportData = $this->GetReportData();
                if(!isset($reportData->error)) {
                    echo "
                    <script>
                        
                        const ".$reportid."Config = {
                                \"type\": 'report',
                                \"accessToken\": '". $this->Token->access_token."', 
                                \"id\": '". $this->ReportID."',
                                \"embedUrl\": '".$reportData->embedUrl."',".
                                $this->ReportSettings.
                            "};    
                    </script>
                    ";
                }
                else {
                    $this->OutputErrorToConsole(($reportData->error));
                    error_log($reportData->error);
                }
            }
            else {
                error_log("Token was not passed to report constructor.  Unable to retrieve reports.");
            }
        }
        
        protected function ValidateToken(): bool {
            // If it is not set at all, the user does not have a valid session to refresh and must reauth to the application
            if (!isset($_SESSION['AzureAuth']['Expiration'])) {
                error_log("No valid session detected. Aborting");
                return false;
            }
            else if ($_SESSION['AzureAuth']['Expiration'] < time()) {
                if(AzureAuth::RefreshToken($_SESSION['AzureAuth']['Token'])) {
                    $this->HasToken = true;
                }
                else {
                    $error = 'Unable to refresh session token.  See server error log for more details.';
                    $this->OutputErrorToConsole($error);
                    error_log("A refresh token is needed, but unable to obtain one");
                    return false;   
                }
            }
            else return true;
        }


        protected function BuildAuthHeader(): array {
            return array('Authorization: '. $this->Token->token_type . ' ' . $this->Token->access_token);
        }

        private function OutputErrorToConsole($msg): void {
            echo "
            <script>
                Console.Error('$msg');
            </script>
            ";
        }


        /**
        *           The API call will return the below available values:
        *           $reportData->{'@odata.context'};
        *           $reportData->reportType;
        *           $reportData->name;
        *           $reportData->webUrl;
        *           $reportData->embedUrl;
        *           $reportData->isFromPbix;
        *           $reportData->isOwnedByMe;
        *           $reportData->datasetId;
        *           $reportData->datasetWorkspaceId;
        */
        private function GetReportData(): stdClass {
            $getReportInfo = curl_init();
            curl_setopt($getReportInfo, CURLOPT_URL, $this->GetSingleReportUrl());
            curl_setopt($getReportInfo, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($getReportInfo, CURLOPT_HTTPHEADER, $this->BuildAuthHeader());

            // Make the HTTP request for the report, and parse the JSON into an object
            $result = curl_exec($getReportInfo);
            if (!$result) {
                error_log(curl_getinfo($getReportInfo, CURLINFO_HTTP_CODE)." ".curl_error($getReportInfo));
                $reportInfo = new stdClass();
                $reportInfo->error = "GetReportData() failed, unable to retrieve report data";
            }
            else {
                $reportInfo = json_decode($result);
            }
            curl_close($getReportInfo);          
            return $reportInfo;
        }

        private function GetSingleReportUrl(): string {
            return self::$PowerBIApiRoot.'groups/'.$this->WorkspaceID.'/'.'reports/'.$this->ReportID;
        }

        private function GetReportSettings(string $settings): string {
            $json = file_get_contents(get_include_path()."/$settings");
            return trim($json, "{}");
        }

        public string $ReportID;
        public string $WorkspaceID;
        public string $ReportSettings;
        public bool $HasToken;
        public stdClass $Token;
        public static string $PowerBIApiRoot = "https://api.powerbi.com/v1.0/myorg/";
        
    }

}