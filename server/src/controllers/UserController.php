<?php
	session_start();

	class UserController{

		private $dao;

		function __construct(DaoManagerInterface $dbConnection){
            $this->dao = $dbConnection;
		}

		public function getLoggedInUserName(){
			return isset($_SESSION["sessionUserStatus"]["userName"]) ? ["hasLoginSession" => true, "loggedInUser" => $_SESSION["sessionUserStatus"]["userName"] ] : ["hasLoginSession" => false];
			// return $_SESSION["sessionUserStatus"]["userName"];
		}

		public function getUserById($userId){
			
			return $this->dao->getRecordById("users", $userId, ["userName", "email"], "userId");
		}

		public function getUserByUserName($userName, $columnsToSelect=["userName", "email"]){
			return $this->dao->getRecordsWhere("users", ["userName" => $userName],["userName", "email"]);
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

        /**
         * Checks if a user is logged in (used by routes and the front end)
         *
         * @param $userName - type: string
         * @return bool
         */
		public function isLoggedIn($userName=""){
			if(isset($userName) && !empty($userName)){
				if(isset($_SESSION["sessionUserStatus"]) && $_SESSION["sessionUserStatus"]["userName"] == $userName)
		        	return true;
		    	else
					return false;
			} else if(isset($_SESSION["sessionUserStatus"]["userName"])){
				return true;
			} else {
				return false;
			}
        }

		public function logoutUser(){
			try{
				$_SESSION["sessionUserStatus"] = null;
				session_destroy();
				// unload user from DB as well.	
				return OperationStatusEnum::SUCCESS;
			} catch (Exception $e){
				return OperationStatusEnum::FAIL;
			}
			
		}

		public function getLoggedInUserId($userName){
			if(isset($_SESSION["sessionUserStatus"]["userName"])){
				if($_SESSION["sessionUserStatus"]["userName"] == $userName)
		        	return $_SESSION["sessionUserStatus"]["userId"];
		    	else
					return OperationStatusEnum::FAIL;
			} else {
				return OperationStatusEnum::FAIL;
			}
		}

	}

?>