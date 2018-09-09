<?php 

	require_once "OperationTypeEnum.php";
//	require_once "ResultSetTypeEnum.php";

	class DaoManager{
		private $dbConn;
		private $servername;
		private $dbUsername;
		private $dbPassword;
		private $dbName;
		private $charset;


		function __construct(){
			$this->servername = apache_getenv("HTTP_SERVER_NAME");
			$this->dbUsername = apache_getenv("HTTP_DB_USER_NAME");
			$this->dbPassword = apache_getenv("HTTP_DB_PASSWORD");
			$this->dbName = apache_getenv("HTTP_DB_NAME");
			$this->charset = 'utf8mb4';

			$dsn = "mysql:host=$this->servername;dbname=$this->dbName;charset=$this->charset";
			$opt = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => false,
			];
			$this->dbConn = new PDO($dsn, $this->dbUsername, $this->dbPassword, $opt);
			// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		}

		/**
		 * Used to generate placeholders for prepared statements
		 * @param $operationType - type: enum - used to determine the operation type we are generating placeholders for
		 * @param $columnsOrData - type: array(string) or Array(Assoc String) - Can contain only the columns we are replacing or a key value pair of columns and values(data)
         *
         * @return string
		 */
		function generatePlaceholders($operationType, $columnsOrData){
			$placeholderSet = "";

			if($operationType == OperationTypeEnum::RecordInsert){
				for ($x = 0; $x < count($columnsOrData); $x++ ){
					if($x == count($columnsOrData) - 1)
						$placeholderSet .= "?"; 
					else
						$placeholderSet .= "?, ";
				}
			}
			else {
				$arrayKeys = array_keys($columnsOrData);
				$lastElement = array_pop($arrayKeys);
				
				while( $element = each( $columnsOrData ) )
				{

					if($element["key"] == $lastElement)
						$placeholderSet .= $element[ 'key' ]." = ?"; 
					else
						$placeholderSet .= $element[ 'key' ]." = ? AND ";

				}	
			}

			return $placeholderSet;
		}

		/**
		 * Builds a columns substring afor SQL statements.
         * @param $columnArray array(string)
         *
         * return array
		 */
		function buildColumns($columnArray){
		    return (count($columnArray) == 1) ? $columnArray[0] : join(", ",$columnArray);
        }

        /**
         * Used to format results that retrieved from the database
         * @param $statement - type: string
         * @param $resultType - type: Enum
         *
         * @return array
         */
        function formatAndRetrieveResults($statement, $resultType){
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            if($resultType == ResultSetTypeEnum::SingleResultSet)
                return $result[0];
            else
                return $result;
        }

        /**
         * Used to get all the records in a database.
         * @param $tableName - type: string
         * @param $columnsToSelect - type: array
         *
         * @return array
         */
		function getAll($tableName, $columnsToSelect=["*"]){
			
			$columnsToSelect = $this->buildColumns($columnsToSelect);
			$sql = "SELECT $columnsToSelect FROM $tableName";
			
			$statement = $this->dbConn->prepare($sql);
			$statement->execute();
			// $result->fetchAll(PDO::FETCH_CLASS, '$columnName');
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			$statement = null;

			return $result;
		}

		/**
		 * Used to get records from a database using a where clause (Currently not working)
		 * @param $tableName - type: string
		 * @param $columnsAndData - type: array(string)
		 * @param $columnsToSelect - type: array(string)
         * @param $resultType - type: enum
         *
         * @return array
		 */
		function getRecordsWhere($tableName, $columnsAndData, $columnsToSelect=["*"], $resultType=ResultSetTypeEnum::SingleResultSet){
			$operationType = new OperationTypeEnum(OperationTypeEnum::RecordRetrieval);
			$placeholderSet = $this->generatePlaceholders($operationType, $columnsAndData);
			$columnsToSelect = $this->buildColumns($columnsToSelect);

			$sql = "SELECT $columnsToSelect FROM $tableName WHERE $placeholderSet";

            $statement = $this->dbConn->prepare($sql);
			$statement->execute(array_values($columnsAndData));
			$result = $this->formatAndRetrieveResults($statement, $resultType);
			$statement = null;

			return $result;
		}

		/**
		 * Used to get records from a database by their ID
		 * @param $tableName - type: string
		 * @param $id - type: int
         *
         * @return array
		 */
		function getRecordById($tableName, $id, $columnsToSelect=["*"], $idName = "id"){
            $columnsToSelect = $this->buildColumns($columnsToSelect);
			$sql = "SELECT $columnsToSelect FROM $tableName where $idName = ? ";


			$statement = $this->dbConn->prepare($sql);
			$statement->execute([$id]);
			$result = $this->formatAndRetrieveResults($statement, ResultSetTypeEnum::SingleResultSet);
			$statement = null;

			return $result;

		}

		/**
		 * Used to update records in a database by using the keys and values of an associative array.
		 * format:("Key" => "Column")
		 * @param $tableName - type: string
		 * @param $id - type: int
		 * @param $columnsAndData - type: Array(Assoc String)
		 * @param $idName - type: string
         *
         * @return int
		 */
		function updateRecordById($tableName, $columnsAndData, $id, $idName = "id"){
			$operationType = new OperationTypeEnum(OperationTypeEnum::RecordChange);
			$placeholderSet = $this->generatePlaceholders($operationType, $columnsAndData);

			$sql = "UPDATE $tableName SET $placeholderSet WHERE $idName = $id";

			$statement = $this->dbConn->prepare($sql);
			$statement->execute(array_values($columnsAndData));
			$numberOfRowschanged = $statement->rowCount();
			$statement = null;

			return $numberOfRowschanged;
		}

		/**
		 * Used to insert records in a database by using the keys and values of an associative array.
		 * format:("Key" => "Column")
		 * @param $tableName - type: string
		 * @param $columnsAndData - type: Array(Assoc String)
		 *
         * @return array
		 */
		function insertRecord($tableName, $columnsAndData){
			$operationType = new OperationTypeEnum(OperationTypeEnum::RecordInsert);
			$placeholderSet = $this->generatePlaceholders($operationType, $columnsAndData);

			$sql = "INSERT INTO $tableName (".join(", ", array_keys($columnsAndData)).") VALUES ($placeholderSet)";

			$statement = $this->dbConn->prepare($sql);
			$statement->execute(array_values($columnsAndData));
			$resultSet = array("rowsEffected" => $statement->rowCount(), "effectedId" => $this->dbConn->LastInsertId());
			$statement = null;

			return $resultSet;
		}

        /**
         * Deletes records in database by a supplied id
         * @param $tableName - type: string
         * @param $id - type: int
         * @param $idName - type: string
         *
         * @return array
         */
		function deleteRecord($tableName, $id, $idName = "id" ){
			$sql = "DELETE FROM $tableName WHERE $idName = ?";

			$statement = $this->dbConn->prepare($sql);
			$statement->execute([$id]);
			$numberOfRowsChanged = array("Number of Rows Effected" => $statement->rowCount());
			$statement = null;

			return $numberOfRowsChanged;
		}

        /**
         * Closes connection
         */
		function closeConnection(){
			$this->dbConn = null;
		}
	}
	

?>