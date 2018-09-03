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
			$columns = array("userId","userName", "password");
			$userResults = $this->dao->getRecordsWhere("users", $columnDataSet, $columns);

			// TODO Possibly use unique ids here?
			// TODO Will also need to check if user is already logged in on another computer
			if(password_verify($password, $userResults["password"])){
				$_SESSION["sessionUserStatus"] = ["userId" => $userResults["userId"], "userName" => $userName, "loginStatus" => true];
			} else {
				$_SESSION["sessionUserStatus"] = ["userName" => $userName, "loginStatus" => false, "reason" => "incorrect info"];
			}

			// return ["results: " => $userResults];
		}

		public function logoutUser(){
			$_SESSION["sessionUserStatus"] = null;
			session_destroy();
			// unload user from DB as well.
		}

	}

?>