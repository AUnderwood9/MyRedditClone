<?php 

	require_once __DIR__."/../databaseManager/DaoManager.php";
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
			
			return $this->dao->getRecordById("users", $userId, ["userName", "email"], "userId");
		}

		public function createUser($createSet){
			$createSet["password"] = password_hash($createSet["password"], PASSWORD_BCRYPT);

			return $this->dao->insertRecord("users", $createSet);
		}

		public function deleteUser($idToDelete){
			return $this->dao->deleteRecord("users", $idToDelete, "userId");
		}

		public function updateUser($tableName, $updateSet, $idToUpdate){
			return $this->dao->updateRecordById($tableName, $updateSet, $idToUpdate, "userId");
		}

		public function loginUser($userName, $password){
			$columnDataSet = array("userName" => $userName);
			$columns = array("userId","userName", "password");
			$userResults = $this->dao->getRecordsWhere("users", $columnDataSet, $columns);
            $loginResult = OperationStatusEnum::NONE;

			// TODO Possibly use unique ids here?
			// TODO Will also need to check if user is already logged in on another computer
			if(password_verify($password, $userResults["password"])){
				$_SESSION["sessionUserStatus"] = ["userId" => $userResults["userId"], "userName" => $userName, "loginStatus" => true];
				$loginResult = OperationStatusEnum::SUCCESS;
			} else {
				$_SESSION["sessionUserStatus"] = ["userName" => $userName, "loginStatus" => false, "reason" => "incorrect info"];
                $loginResult = OperationStatusEnum::FAIL;
			}

			 return $loginResult;
		}

		public function logoutUser(){
			$_SESSION["sessionUserStatus"] = null;
			session_destroy();
			// unload user from DB as well.
		}

	}

?>