<?php
namespace Khoelck\PhpPowerBI {  

    abstract class AzureApiCall {
        abstract protected function ValidateToken() : bool;

        abstract protected function GetBaseRequestURL() : string;

        abstract protected function BuildAuthHeader() : array;
    }

}

?>