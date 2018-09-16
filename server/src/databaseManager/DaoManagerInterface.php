<?php

    interface DaoManagerInterface{
        function getAll($tableName, $columnsToSelect=["*"]);
        function getRecordsWhere($tableName, $columnsAndData, $columnsToSelect=["*"], $resultType=ResultSetTypeEnum::SingleResultSet);
        function getRecordById($tableName, $id, $columnsToSelect=["*"], $idName = "id", $resultType=ResultSetTypeEnum::SingleResultSet);
        function updateRecordById($tableName, $columnsAndData, $id, $idName = "id");
        function insertRecord($tableName, $columnsAndData);
        function deleteRecord($tableName, $id, $idName = "id" );
        function closeConnection();
    }

?>