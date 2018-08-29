<?php 

	require_once "server/src/databaseManager/DBManager.php";

	class UserController{

		private $dao;

		function __construct(){
			$this->dao = new DaoManager();
		}

		function getAllUsers(){

			return $this->dao->getAll("users");

		}

		function getUser($userId){
			return $this->dao->getRecordById("users", $userId, "userId");
		}

		function createUser($createSet){

			return $this->dao->insertRecord("users", $createSet);
		}

		function deleteUser($idToDelete){
			return $this->dao->deleteRecord("users", $idToDelete, "userId");
		}

		function updateUser($updateSet, $idToUpdate){
			return $this->dao->pdateRecordById($tableName, $updateSet, $idToUpdate, "userId");
		}

	}

?>