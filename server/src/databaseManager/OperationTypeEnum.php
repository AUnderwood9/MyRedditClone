<?php

	include_once __DIR__."/../utils/Enum.php";

	class OperationTypeEnum extends Enum {
		const RecordRetrieval = "RecordRetrieval";
		const RecordInsert = "RecordInsert";
		const RecordChange = "RecordChange";
	}

?>