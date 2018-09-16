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
            if($_SESSION["sessionUserStatus"]["loginStatus"]){
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

                if ($userAssociationResultSet["rowsEffected"] == 1 && $castPostRelationResultSet["rowsEffected"] == 1)
                    return OperationStatusEnum::SUCCESS;
                else
                    return OperationStatusEnum::FAIL;
            } else {
                return OperationStatusEnum::NONE;
            }
        }

    }

?>