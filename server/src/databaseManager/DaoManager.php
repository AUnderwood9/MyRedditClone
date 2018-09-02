<?php 

	require_once "OperationTypeEnum.php";
	require_once "ResultSetTypeEnum.php";

	class DaoManager{
		private $dbConn;
		// private $tableName;
		private $servername;
		private $dbUsername;
		private $dbPassword;
		private $dbName;
		private $charset;


		function __construct(){
			// $this->$tableName = $tableName;
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
						$placeholderSet .= $element[ 'key' ]." = ?, ";

				}	
			}

			return $placeholderSet;
		}

		function getAll($tableName){
			
			$sql = "SELECT * FROM $tableName";
			
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
		 * @param $columnAndValue - type: array(string)
		 * @param $columnsToSelect - type: aray(string)
		 */
		function getRecordsWhere($tableName, $columnsAndData, $columnsToSelect=["*"], $resultType=ResultSetTypeEnum::SingleResultSet){
			$operationType = new OperationTypeEnum(OperationTypeEnum::RecordRetrieval);
			$placeholderSet = $this->generatePlaceholders($operationType, $columnsAndData);
			$columnsToSelect = (count($columnsToSelect) == 1) ? $columnsToSelect[0] : join(", ",$columnsToSelect);
			
			var_dump($columnsAndData);
			echo "</br></br>";
			$columns = array("userName", "password");
			var_dump($columnsToSelect);
			echo "</br></br>";

			$sql = "SELECT $columnsToSelect FROM $tableName WHERE $placeholderSet";

			$statement = $this->dbConn->prepare($sql);
			$statement->execute(array_values($columnsAndData));
			// $result = $statement->fetchAll(PDO::FETCH_ASSOC);
			$result = $this->formatAndRetrieveResults($statement, $resultType);
			$statement = null;

			return $result;

		}

		/**
		 * Used to get records from a database by their ID
		 * @param $tableName - type: string
		 * @param $id - type: int
		 */
		function getRecordById($tableName, $id, $idName = "id"){
			
			$sql = "SELECT * FROM $tableName where $idName = ? ";

			$statement = $this->dbConn->prepare($sql);
			$statement->execute([$id]);
			// $result = $statement->fetchAll(PDO::FETCH_ASSOC);
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
		 */
		function updateRecordById($tableName, $columnsAndData, $id, $idName = "id"){
			$operationType = new OperationTypeEnum(OperationTypeEnum::RecordChange);
			$placeholderSet = $placeholderSet = $this->generatePlaceholders($operationType, $columnsAndData);

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
		 */
		function insertRecord($tableName, $columnsAndData){
			$operationType = new OperationTypeEnum(OperationTypeEnum::RecordInsert);
			$placeholderSet = $this->generatePlaceholders($operationType, $columnsAndData);;

			$sql = "INSERT INTO $tableName (".join(", ", array_keys($columnsAndData)).") VALUES ($placeholderSet)";

			$statement = $this->dbConn->prepare($sql);
			$statement->execute(array_values($columnsAndData));
			$resultSet = array("Number of Rows Effected" => $statement->rowCount(), "Effected row id(s)" => $this->dbConn->LastInsertId());
			$statement = null;

			return $resultSet;
		}

		function deleteRecord($tableName, $id, $idName = "id" ){
			$sql = "DELETE FROM $tableName WHERE $idName = ?";

			$statement = $this->dbConn->prepare($sql);
			$statement->execute([$id]);
			$numberOfRowschanged = array("Number of Rows Effected" => $statement->rowCount());
			$statement = null;

			return $numberOfRowschanged;
		}

		function formatAndRetrieveResults($statement, $resultType){
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			if($resultType == ResultSetTypeEnum::SingleResultSet)
				return $result[0];
			else
				return $result;
		}

		function closeConnection(){
			$this->dbConn = null;
		}
	}
	

?>