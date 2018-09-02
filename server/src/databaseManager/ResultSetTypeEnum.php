<?php

	include_once __DIR__."/../utils/Enum.php";

	class ResultSetTypeEnum extends Enum {
		const SingleResultSet = "SingleResultSet";
		const MultiResultSet = "MultiResultSet";
	}

?>