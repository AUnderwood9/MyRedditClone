<?php

	require_once "server/src/databaseManager/DaoManager.php";

	class CastController{

		private $dao;

		function __construct(){
			$this->dao = new DaoManager();
		}

		/**
		 * The cast itself is created then its references to the user is created.
		 * @param array $castCreateSet
		 * 	title: (String) Title of the subCast (200 character limit. May reduce.)
		 * 	description: (String) Description of the subCast (750 character limit)
		 * 	castName: (String) Name of the cast (Main name as well as URL name. Alphanumeric only)
		 * 	sideBar: (String) Sidebar information for the subCast (20,000 character limit?)
		 * 	isActive: (boolean) Determines whether the subCast is active and can be navigated to
		 * 	visible: (boolean) Determines whether the subCast is visible and can be found in the main site search
		 * 	primaryAccentColor: (String) The accent color to be used
		 */
		public function createCast($castCreateSet){
			if($_SESSION["sessionUserStatus"]["loginStatus"]){
				// get current user id
				$currentUserId = $_SESSION["sessionUserStatus"]["userId"];

				// create subcast
				$castCreateResultSet = $this->dao->insertRecord("subCast", $castCreateSet);

				// update userCreatedContent
				$createdCastId = $castCreateResultSet["effectedId"];
				$userAssociationResultSet = $this->dao->insertRecord("userCreatedContent", ["userId" => $currentUserId, "subCastId" => $createdCastId]);
				
				return ["Cast creation Result" => $castCreateResultSet, "Cast association result" => $userAssociationResultSet];
			} else {
				return "User not logged in";
			}
		}

		/**
		 * 
		 */
		public function hideCast(){

		}

		/**
		 * 
		 */
		public function deleteCast(){

		}

		/**
		 * 
		 */
		public function updateSidebar(){

		}

		/**
		 * 
		 */
		public function updateDescription(){

		}

		/**
		 * 
		 */
		public function updatePrimaryColor(){

		}

		/**
		 * 
		 */
		public function updateBanList(){

		}

	}

?>