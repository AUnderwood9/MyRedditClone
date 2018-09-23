<?php

    class CommentController {
        private $dao;

        function __construct(DaoManagerInterface $dbConnection){
            $this->dao = $dbConnection;
        }

        /**
         * @TODO create a logging system instead of echoing out errors to abstract database information away from the front end (Client)
         * The comment is created then its references to the user is created.
         * @param integer postId
         * @param array $commentCreateSet
         *
         * @return string (enum)
         */
        public function createComment($castId, $postId, $commentCreateSet){
            $operationStatus = OperationStatusEnum::NONE;

            if($_SESSION["sessionUserStatus"]["loginStatus"]){
                // get current user id
                try{
                    $this->dao->startTransaction();

                    $currentUserId = $_SESSION["sessionUserStatus"]["userId"];
                    $commentCreateSet += ["creatorId" => $currentUserId,"likes" => 1, "dislikes" => 0];

                    // create comment
                    $commentCreateResultSet = $this->dao->insertRecord("comment", $commentCreateSet);
                    $createdCommentId = $commentCreateResultSet["effectedId"];

                    // update comment index
                    $castPostCommentRelationResultSet = $this->dao->insertRecord("castPostCommentMatrixIndex", ["castId" => $castId, "postId" => $postId, "commentId" => $createdCommentId]);

                    // update userCreatedContent
                    $userAssociationResultSet = $this->dao->insertRecord("userCreatedContent", ["userId" => $currentUserId, "subCastId" => $castId, "postId" => $postId, "CommentId" => $createdCommentId]);

                    if($commentCreateResultSet["rowsEffected"] == 1 && $userAssociationResultSet["rowsEffected"] == 1 && $castPostCommentRelationResultSet["rowsEffected"]){
                        $this->dao->commitTransaction();
                        $operationStatus = OperationStatusEnum::SUCCESS;
                    } else {
                        $this->dao->rollbackTransaction();
                        $operationStatus = OperationStatusEnum::FAIL;
                    }


                } catch (Exception $e) {
                    echo $e;
                    $this->dao->rollbackTransaction();
                    $operationStatus = OperationStatusEnum::FAIL;
                }
            } else {
                $operationStatus = OperationStatusEnum::NONE;
            }
            return $operationStatus;
        }

        /**
         * @TODO since we are checking to see if the user is logged in a lot this should be abstracted out of the controllers. Or checked in the front end only.
         * @param $castId integer
         * @param $postId integer
         * @param $commentId integer
         * @param $commentEditSet array
         *
         * @return string
         */
        function editComment($commentId, $newCommentText) {
            $operationStatus = OperationStatusEnum::NONE;

            if($_SESSION["sessionUserStatus"]["loginStatus"]) {
                try {
                    $this->dao->startTransaction();
                    $numberOfRowsChanged = $this->dao->updateRecordById("comment", ["content" => $newCommentText], $commentId, "commentId");

                    if($numberOfRowsChanged  == 1){
                        $this->dao->commitTransaction();
                        $operationStatus = OperationStatusEnum::SUCCESS;
                    } else {
                        $this->dao->rollbackTransaction();
                        $operationStatus = OperationStatusEnum::FAIL;
                    }

                } catch (Exception $e) {
                    echo $e;
                    $this->dao->rollbackTransaction();
                    $operationStatus = OperationStatusEnum::FAIL;
                }
            } else {
                $operationStatus = OperationStatusEnum::NONE;
            }
            return $operationStatus;
        }

        /**
         * @param $castId integer
         * @param $postId integer
         * @param $commentId array
         *
         * @return string
         */
        function deleteComment($commentId) {
            $operationStatus = OperationStatusEnum::NONE;

            if($_SESSION["sessionUserStatus"]["loginStatus"]) {
                try {
                    $this->dao->startTransaction();
                    $numberOfRowsChanged = $this->dao->deleteRecord("comment", $commentId, "commentId");
                    
                    if($numberOfRowsChanged ["Number of Rows Effected"]  == 1){
                        $this->dao->commitTransaction();
                        $operationStatus = OperationStatusEnum::SUCCESS;
                    } else {
                        $this->dao->rollbackTransaction();
                        $operationStatus = OperationStatusEnum::FAIL;
                    }

                } catch (Exception $e) {
                    echo $e;
                    $this->dao->rollbackTransaction();
                    $operationStatus = OperationStatusEnum::FAIL;
                }
            } else {
                $operationStatus = OperationStatusEnum::NONE;
            }
            return $operationStatus;
        }

    }

?>