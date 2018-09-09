<?php

    require_once __DIR__."/../databaseManager/DaoManager.php";

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
				
				return ($userAssociationResultSet["rowsEffected"] == 1 ) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;
			} else {
				return OperationStatusEnum::NONE;
			}
		}

		/**
		 * 
		 */
		public function getCastInfo($currentCastId=0, $castLink=""){
		    $isOwner = 0;
		    $userId = $_SESSION["sessionUserStatus"]["userId"];
		    $castId = 0;

			// Set current cast in the session variable $_SESSION["currentCastId"]
            if($currentCastId > 0)
                $currentCastInfo = $this->dao->getRecordById("subCast", $currentCastId, ["castID", "title", "description", "headerImg", "castName", "isActive", "sideBar", "primaryAccentColor"]);
            else
                $currentCastInfo = $this->dao->getRecordsWhere("subCast", ["castName" => $castLink], ["castID", "title", "description", "headerImg", "castName", "isActive", "sideBar", "primaryAccentColor"]);;

            // Check if user is an owner of the cast.
            $castId = $currentCastInfo["castID"];

            if($_SESSION["sessionUserStatus"]["loginStatus"]){
                $isOwner = $this->dao->getRecordsWhere("userCreatedContent", ["userId" => $userId, "subCastId" => $castId], ["userId"]);
            }

            $currentCastInfo += (isset($isOwner["userId"])) ?  ["isOwner" => true] : ["isOwner" => false];

			return $currentCastInfo;
		}

        /**
         * Retrieve a list of cast ids, their names, and their descriptions.
         *
         * @return array
         */
		public function getCastList(){

		    /*
		     * if a cast is visible then it will be listed.
		     * if a cast is not visible then that means that it won't be listed and it is private.
		     * if a cast is not active it was either banned or disabled by the owner
		     */

            $castListing = [];
		    $castsInDb= $this->dao->getAll("subCast", ["castID", "description", "castName", "isActive", "visible"]);

		    for($x = 0; $x < count($castsInDb); $x++){
		        if($castsInDb[$x]["visible"]){
		            unset($castsInDb[$x]["visible"]);
                    $castListing[] = $castsInDb;
                }
            }

		    return $castListing;

        }

		/**
		 * In the cast control panel The owner can hide the casts that they created.
         * @param $currentCastId - type: int
         *
         * return enum
		 */
		public function hideCast($currentCastId){
			if($_SESSION["sessionUserStatus"]["loginStatus"]){
				// if not return an error
                $numberOfRowsChanged = $this->dao->updateRecordById("subCast", ["visible" => 0], $currentCastId, "castID");

                return ($numberOfRowsChanged == 1) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;
			} else {
				return OperationStatusEnum::NONE;
			}
		}

		/**
		 * 
		 */
		public function deleteCast($currentCastId){

		}

		/**
		 * 
		 */
		public function updateSidebar($currentCastId){

		}

		/**
		 * 
		 */
		public function updateDescription($currentCastId){

		}

		/**
		 * 
		 */
		public function updatePrimaryAccentColor($currentCastId){

		}

		/**
		 * 
		 */
		public function updateBanList($currentCastId){

		}

	}

?>