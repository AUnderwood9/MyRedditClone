<?php

include_once __DIR__ . "/../utils/Enum.php";

class SearchByMethodEnum extends Enum
{
    const BYPOSTID = "BYPOSTID";
    const BYCASTID = "BYCASTID";
    const BYUSERID = "BYUSERID";
    const NONE = "NONE";

}

?>