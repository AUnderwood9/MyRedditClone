<?php 

	require_once "server/src/databaseManager/DaoManager.php";
	session_start();

	class UserController{

		private $dao;
		private $salt;

		function __construct(){
			$this->dao = new DaoManager();
			$this->salt = apache_getenv("VALUE_SALT");
			// $this->salt = "salt";
		}

		public function getAllUsers(){

			return $this->dao->getAll("users");

		}

		public function getUserById($userId){
			$userResults = $this->dao->getRecordById("users", $userId, "userId");
			// $2y$10$e0KbNorOSBMJVZaOYtBZOOIwiN2ORxvdtYaE2MFdZj.MlKv9tRifG
			// return ["results: " => $userResults, "salt: " => $this->salt, "pass hash: " => password_hash($userResults["password"], PASSWORD_BCRYPT)];
			// return ["results: " => $userResults, "pass hash: " => password_verify($userResults["password"], "\$2y\$10\$e0KbNorOSBMJVZaOYtBZOOIwiN2ORxvdtYaE2MFdZj.MlKv9tRifG")];
			
		}

		public function createUser($createSet){
			$createSet["password"] = password_hash($createSet["password"], PASSWORD_BCRYPT);

			return $this->dao->insertRecord("users", $createSet);
		}

		public function deleteUser($idToDelete){
			return $this->dao->deleteRecord("users", $idToDelete, "userId");
		}

		public function updateUser($updateSet, $idToUpdate){
			return $this->dao->pdateRecordById($tableName, $updateSet, $idToUpdate, "userId");
		}

		public function loginUser($userName, $password){
			$columnDataSet = array("userName" => $userName);
			// var_dump($columnDataSet);
			// echo "</br></br>";
			$columns = array("userName", "password");
			// var_dump($columns);
			// echo "</br></br>";
			$userResults = $this->dao->getRecordsWhere("users", $columnDataSet, $columns);

			// Possibly use unique ids here?
			if(password_verify($password, $userResults["password"])){
				$_SESSION["sessionUserName"] = $userName;
				$_SESSION["loginStatus"] = true;
			}

			// return ["results: " => $userResults];
		}

	}

?>