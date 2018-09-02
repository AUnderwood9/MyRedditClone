<?php 

	require_once "server/src/databaseManager/DaoManager.php";
	session_start();

	class UserController{

		private $dao;

		function __construct(){
			$this->dao = new DaoManager();
		}

		public function getAllUsers(){

			return $this->dao->getAll("users");

		}

		public function getUserById($userId){
			
			return $this->dao->getRecordById("users", $userId, "userId");
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
			$columns = array("userName", "password");
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