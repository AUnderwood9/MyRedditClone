<?php

    class PostController
    {

        private $dao;

        function __construct(DaoManagerInterface $dbConnection){
            $this->dao = $dbConnection;
        }

        /**
         * The post is created then its references to the user is created.
         * @param integer castId
         * @param array $postCreateSet
         *
         * @return string (enum)
         */
        public function createPost($castId, $postCreateSet){
			$operationStatus;
            if($_SESSION["sessionUserStatus"]["loginStatus"]){
				try{
					$this->dao->startTransaction();
					// get current user id
					$currentUserId = $_SESSION["sessionUserStatus"]["userId"];

					$postCreateSet += ["creatorUserId" => $currentUserId,"likes" => 1, "dislikes" => 0] ;
	
					// create post
					$postCreateResultSet = $this->dao->insertRecord("Post", $postCreateSet);
	
					$createdPostId = $postCreateResultSet["effectedId"];
	
					// update castPostndex
					$castPostRelationResultSet = $this->dao->insertRecord("castPostCommentMatrixIndex", ["castId" => $castId, "postId" => $createdPostId]);
	
					// update userCreatedContent
					$userAssociationResultSet = $this->dao->insertRecord("userCreatedContent", ["userId" => $currentUserId, "subCastId" => $castId, "postId" => $createdPostId]);
	
					if ($postCreateResultSet["rowsEffected"] == 1 && $userAssociationResultSet["rowsEffected"] == 1 && $castPostRelationResultSet["rowsEffected"] == 1){
						$operationStatus = OperationStatusEnum::SUCCESS;
						$this->dao->commitTransaction();
                    } else {
                        $this->dao->rollbackTransaction();
                        $operationStatus = OperationStatusEnum::FAIL;
                    }
				} catch (Exception $e) {
					$this->dao->rollbackTransaction();
                    $operationStatus = OperationStatusEnum::FAIL;

				}
                
            } else {
                $operationStatus = OperationStatusEnum::NONE;
			}
			
			return $operationStatus;
        }

        /**
         * Used to get all of the posts that are within a cast or that belong to a user. When selecting by user, postId
         * as well as subCastId are used to identify a unique post.
         * @param $method string
         * @param $idToSearch integer
         *
         * @return array
         */
        function getPosts($method, $idToSearch) {
            $byCastId = new SearchByMethodEnum(SearchByMethodEnum::BYCASTID);
            $byUserId = new SearchByMethodEnum(SearchByMethodEnum::BYUSERID);

			$postListResults = [];

            switch (strtoupper($method)){
                case $byCastId->getValue() :

					$postList = $this->dao->getRecordById("usercreatedcontent", $idToSearch, ["subCastId", "postId"], "subCastId", ResultSetTypeEnum::MultiResultSet, true);
					$postList = array_filter($postList, function($value){return !is_null($value["postId"]);});
					
					if(count($postList) > 0){
						foreach($postList as $value){
							$postListResults[] = $this->dao->getRecordById("post", $value["postId"], ["title", "postType", "description", "likes", "dislikes"], "postId");
						}
					}


                    break;
                case $byUserId->getValue() :

					$postList = $this->dao->getRecordById("usercreatedcontent", $idToSearch, ["postId", "subCastId"], "userId", ResultSetTypeEnum::MultiResultSet);
					$postList = array_filter($postList, function($value){return !is_null($value["postId"]);});

                    if(count($postList) > 0){
						foreach($postList as $value){
							$postListResults[] = $this->dao->getRecordById("post", $value["postId"], ["title", "postType", "description", "likes", "dislikes"], "postId");
						}
					}
                    break;
            }

            return $postListResults;
        }

        /**
         * @param $postId integer
         * @param $edit string
         * @return string
         */
        function editPostDescription($postId, $edit){
			$updateResultSet = 0;
            if($_SESSION["sessionUserStatus"]["loginStatus"]){
				$creatorId = $this->dao->getRecordById("post", $postId, ["creatorUserId"], $idName = "postId")["creatorUserId"];
				
                if($_SESSION["sessionUserStatus"]["userId"] == $creatorId){
                    $updateResultSet = $this->dao->updateRecordById("post", ["description" => $edit], $postId, "postId");
                }
            }

            return ($updateResultSet > 0) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;
        }

        /**
         * @TODO Add this to a controller of its own since it can also have its own generic functinoality. POPO can be used for the Ids.
         * @param $castId integer
         * @param $postId integer
         * @param $userId integer
         * @param $newAffinity integer (0 - no affinity, 1 - like, 2 - dislike)
         *
         * @return string
         */
        function setUserPostAffinity($castId, $postId, $userId, $newAffinity){
            try {

				$currentAffinityId="";

                $this->dao->startTransaction();
				// INSERT INTO userlikedislikeindex(userId, contentId, userAffinity) VALUES (17, 4, 1)

				// Find the content that the user is applying an affinity to
				// SELECT * FROM usercreatedcontent WHERE subCastId = 1 AND postId = 1 AND commentId IS NULL 
				$postRecordList = $this->dao->getRecordsWhere("userCreatedContent", ["subCastId" => $castId, "postId" => $postId],$columnsToSelect=["id", "commentId"], ResultSetTypeEnum::MultiResultSet);
				
				foreach($postRecordList as $currentElement){
					if(is_null($currentElement["commentId"]))
						$currentAffinityId = $currentElement["id"];
				}
				
                // Check if user has an affinity with the selected post
                $currentUserAffinityId = $this->dao->getRecordById("userLikeDislikeIndex", $currentAffinityId, $columnsToSelect=["id", "contentId", "userAffinity"], "contentId")["id"];
                // if user has an affinity
                if(isset($currentUserAffinityId)){
					
                    // get the affinity record of the user that refers to this current post
                    $operationResults = $this->dao->updateRecordById("userLikeDislikeIndex", ["userAffinity" => $newAffinity], $currentUserAffinityId);

                    // @TODO add like/dislike to current post
                } else {
                    $operationResults = $this->dao->insertRecord("userLikeDislikeIndex", ["userId" => $userId, "contentId" => $currentUserAffinityId, "userAffinity" => $newAffinity]);
                }

                $this->dao->commitTransaction();

            } catch(Exception $e) {
                
                $this->dao->rollbackTransaction();
            }

            return ($operationResults > 0) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;

        }

        function removeEmpty2dElements($array2d, $keyToTest){
            $new2dArray = [];
            for($i = 0; $i < count($array2d); $i++){
                $tempSet = $array2d[$i][$keyToTest];
                if(isset($tempSet)){
                    $new2dArray []= $array2d[$i];
                }
            }

            return $new2dArray;
        }

    }

?>