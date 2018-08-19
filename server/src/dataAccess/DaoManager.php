<?php 

	require_once "OperationTypeEnum.php";

	class DaoManager{
		private $pdo;
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
			$this->pdo = new PDO($dsn, $this->dbUsername, $this->dbPassword, $opt);
			// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		}

		/**
		 * Used to generate placeholders for prepared statements
		 * @param $operationType - type: enum - used to determine the operation type we are generating placeholders for
		 * @param $columnsOrData - type: array(string) or Array(Assoc String) - Can contain only the columns we are replacing or a key value pair of columns and values(data)
		 */
		function generatePlaceholders($operationType, $columnsOrData){
			$placeholderSet = "";

			if($operationType == OperationTypeEnum::RecordRetrieval || $operationType == OperationTypeEnum::RecordInsert){
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
			$statement = $this->pdo->prepare($sql);
			$statement->execute();
			// $result->fetchAll(PDO::FETCH_CLASS, '$columnName');
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);

			return $result;
		}

		/**
		 * Used to get records from a database with specified coulumns (Currently not working)
		 * @param $tableName - type: string
		 * @param $columns - type: array(string)
		 */
		function getRecordSetWithColumns($tableName, $columns){
			$operationType = new OperationTypeEnum(OperationTypeEnum::RecordRetrieval);
			$placeholderSet = $this->generatePlaceholders($operationType, $columnsAndData);

			$sql = "SELECT $placeholderSet FROM $tableName";
			var_dump($columns);
			$statement = $this->pdo->prepare($sql);
			$statement->execute($columns);
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);

			return $result;

		}

		/**
		 * Used to get records from a database by their ID
		 * @param $tableName - type: string
		 * @param $id - type: int
		 */
		function getRecordById($tableName, $id, $idName = "id"){
			
			$sql = "SELECT * FROM $tableName where $idName = ? ";

			$statement = $this->pdo->prepare($sql);
			$statement->execute([$id]);
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);

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
			$statement = $this->pdo->prepare($sql);
			$statement->execute(array_values($columnsAndData));
			$numberOfRowschanged = $statement->rowCount();

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
			$statement = $this->pdo->prepare($sql);
			$statement->execute(array_values($columnsAndData));
			$numberOfRowschanged = $statement->rowCount();

			return $numberOfRowschanged;
		}
	}
	

?>