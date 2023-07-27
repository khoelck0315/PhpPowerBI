<?php declare(strict_types=1);
namespace Khoelck\PhpPowerBI {
    require "AzureApiCall.php";
    use stdClass;
    

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
    final class PowerBIReport extends AzureApiCall {
        public function __construct(string $reportId, string $workspaceId, string $settings, stdClass $token) {
            $this->ReportID = $reportId;
            $this->WorkspaceID = $workspaceId;

            //Next step is to parse this report data into private properties for this class instance to be referened when calling/displaying the report
            $reportData = $this->GetReportData($token);
            $this->DataContext = $reportData->{'@odata.context'};
            $this->BaseRequestUrl = $this->GetBaseRequestUrl();
            $this->ReportType = $reportData->reportType;
            $this->Name = $reportData->name;
            $this->WebUrl = $reportData->webUrl;
            $this->EmbedUrl = $reportData->embedUrl;
            $this->IsFromPbix = $reportData->isFromPbix;
            $this->IsOwnedByMe = $reportData->isOwnedByMe;
            $this->DatasetId = $reportData->datasetId;
            $this->DatasetWorkspaceId = $reportData->datasetWorkspaceId;
            $this->ReportSettings = PowerBIConfig::GetReportSettings($settings);
        }
        
        /**
         * To append the report to the document, create a parent element and give it an id that will be passed as an 
         * argument to this function.
         * @param string reportid - The HTML attribute ID of the parent element that will contain the report.
         */
        public function ShowReport(string $reportid): void {
            if(!$this->ValidateToken()) {
                $this->OutputErrorToConsole();
            }
            else {
                $this->OutputReportJS($reportid);
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
                    return true;
                }
                else {
                    $this->OutputErrorToConsole();
                    error_log("A refresh token is needed, but unable to obtain one");
                    return false;   
                }
            }
            else return true;

        }

        protected function GetBaseRequestUrl(): string {
            $location = parse_url($this->DataContext);
            return $location['host'];
        }

        protected function BuildAuthHeader(): array {
            return array('Authorization: '. $_SESSION['AzureAuth']['Token']->token_type . ' ' . $_SESSION['AzureAuth']['Token']->access_token);
        }

        private function OutputReportJS(string $reportid): void {
            echo "
            <script src='".PowerBIConfig::$PowerBI_JS."'></script>
            <script>
                const embedConfiguration = {
                    type: 'report',
                    accessToken: '".$_SESSION['AzureAuth']['Token']->access_token."',
                    id: '". $this->ReportID."',
                    embedUrl: '".$this->EmbedUrl."',".
                    $this->ReportSettings.
                "};

                const reportContainerParentId = '".$reportid."';
                const reportContainerParent = document.getElementById(reportContainerParentId);
                const reportContainer = document.createElement('div');
                reportContainer.setAttribute('id', 'reportContainer');
                reportContainerParent.appendChild(reportContainer);
                const report = powerbi.embed(reportContainer, embedConfiguration);
            </script>
            ";
        }

        private function OutputErrorToConsole(): void {
            echo "
            <script>
                Console.Error('Unable to refresh session token.  See server error log for more details.');
            </script>
            ";
        }

        private function GetReportData(): stdClass {
            $getReportInfo = curl_init();
            curl_setopt($getReportInfo, CURLOPT_URL, $this->GetSingleReportUrl());
            curl_setopt($getReportInfo, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($getReportInfo, CURLOPT_HTTPHEADER, $this->BuildAuthHeader());

            // Make the HTTP request for the report, and parse the JSON into an object
            $reportInfo = json_decode(curl_exec($getReportInfo));
            curl_close($getReportInfo);          
            return $reportInfo;
        }

        private function GetSingleReportUrl(): string {
            return PowerBIConfig::$PowerBIApiRoot.'groups/'.$this->WorkspaceID.'/'.'reports/'.$this->ReportID;
        }



        public string $DataContext;
        public string $ReportID;
        public string $ReportType;
        public string $Name;
        public string $WebUrl;
        public string $EmbedUrl;
        public bool $IsFromPbix;
        public bool $IsOwnedByMe;
        public string $DatasetId;
        public string $DatasetWorkspaceID;
        public string $WorkspaceID;
        public stdClass $Token;
    }

}