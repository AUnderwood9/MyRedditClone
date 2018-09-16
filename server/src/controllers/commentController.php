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
        public function createComment($postId, $commentCreateSet){
            if($_SESSION["sessionUserStatus"]["loginStatus"]){
                // get current user id
                $currentUserId = $_SESSION["sessionUserStatus"]["userId"];

                // create comment
                $commentCreateResultSet = $this->dao->insertRecord("Comment", $commentCreateSet);

                // update postCommentIndex

                // update userCreatedContent
                $createdCommentId = $commentCreateResultSet["effectedId"];
                $userAssociationResultSet = $this->dao->insertRecord("userCreatedContent", ["userId" => $currentUserId, "CommentId" => $createdCommentId]);

                return ($userAssociationResultSet["rowsEffected"] == 1 ) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;
            } else {
                return OperationStatusEnum::NONE;
            }
        }

    }

?>