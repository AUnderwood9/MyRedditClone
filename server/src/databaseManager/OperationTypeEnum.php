<?php

	include __DIR__."/../utils/Enum.php";

	class OperationTypeEnum extends Enum {
		const RecordRetrieval = "RecordRetrieval";
		const RecordInsert = "RecordInsert";
		const RecordChange = "RecordChange";
	}

?>