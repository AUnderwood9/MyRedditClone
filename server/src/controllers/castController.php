<?php

	class CastController{

		private $dao;

		function __construct(DaoManagerInterface $dbConnection){
			$this->dao = $dbConnection;
		}

		/**
		 * The cast itself is created then its references to the user is created.
		 * @param array $castCreateSet
         *
         * @return string (enum)
		 */
		public function createCast($castCreateSet){
			$operationStatus = OperationStatusEnum::NONE;
			if($_SESSION["sessionUserStatus"]["loginStatus"]){
				try{
					$this->dao->startTransaction();	
					// get current user id
					$currentUserId = $_SESSION["sessionUserStatus"]["userId"];
					
					// create subcast
					$castCreateResultSet = $this->dao->insertRecord("subCast", $castCreateSet);
					
					// update userCreatedContent
					$createdCastId = $castCreateResultSet["effectedId"];
					$userAssociationResultSet = $this->dao->insertRecord("userCreatedContent", ["userId" => $currentUserId, "subCastId" => $createdCastId]);

					if($userAssociationResultSet["rowsEffected"] == 1 && $castCreateResultSet["rowsEffected"] == 1){   $this->dao->commitTransaction();
                        $operationStatus = OperationStatusEnum::SUCCESS;
                    } else {
                        $this->dao->rollbackTransaction();
                        $operationStatus = OperationStatusEnum::FAIL;
                    }
				} catch (Exception $e){
					echo $e;
                    $this->dao->rollbackTransaction();
                    $operationStatus = OperationStatusEnum::FAIL;
				}
				
				
				return $operationStatus;
			} else {
				return OperationStatusEnum::NONE;
			}
		}

		/**
		 * Retrieves information on a cast based off of its ID or its resource link endpoint
         * @param $currentCastId - type: int
         * @param $castLink - type: string
         *
         * @return array
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
		        if($castsInDb[$x]["visible"] == 0){
					unset($castsInDb[$x]);
                    $castListing[] = $castsInDb;
                }
            }

		    return $castListing;

        }

		/**
		 * In the cast control panel The owner can hide the casts that they created.
         * @param $currentCastId - type: int
         *
         * @return string (enum)
		 */
		public function hideCast($currentCastId){
			if($_SESSION["sessionUserStatus"]["loginStatus"]){
                $numberOfRowsChanged = $this->dao->updateRecordById("subCast", ["visible" => 0], $currentCastId, "castID");

                return ($numberOfRowsChanged == 1) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;
			} else {
				return OperationStatusEnum::NONE;
			}
		}

		public function showCast($currentCastId){
            if($_SESSION["sessionUserStatus"]["loginStatus"]){
                $numberOfRowsChanged = $this->dao->updateRecordById("subCast", ["visible" => 1], $currentCastId, "castID");

                return ($numberOfRowsChanged == 1) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;
            } else {
                return OperationStatusEnum::NONE;
            }
        }

        public function updateCast($currentCastId, $columnsAndData){
            if($_SESSION["sessionUserStatus"]["loginStatus"]){
                $numberOfRowsChanged = $this->dao->updateRecordById("subCast", $columnsAndData, $currentCastId, "castID");

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