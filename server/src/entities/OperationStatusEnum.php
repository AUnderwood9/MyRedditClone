<?php

include_once __DIR__ . "/../utils/Enum.php";

class OperationStatusEnum extends Enum
{
    const SUCCESS = "SUCCESS";
    const FAIL = "FAIL";
    const NONE = "NONE";

}