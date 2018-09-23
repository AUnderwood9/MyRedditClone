<?php

    class CommentController {
        private $dao;

        function __construct(DaoManagerInterface $dbConnection){
            $this->dao = $dbConnection;
        }

        /**
         * The comment is created then its references to the user is created.
         * @param integer postId
         * @param array $commentCreateSet
         *
         * @return string (enum)
         */
        public function createComment($castId, $postId, $commentCreateSet){
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
                        return OperationStatusEnum::SUCCESS;
                    } else {
                        $this->dao->rollbackTransaction();
                    }


                } catch (Exception $e) {
                    echo $e;
                    $this->dao->rollbackTransaction();
                }
            } else {
                return OperationStatusEnum::NONE;
            }
        }

    }

?>