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

        /**
         * Used to get all of the posts that are within a cast or that belong to a user. When selecting by user, postId
         * as well ass subCastId are used to identify a unique post.
         * @param $method string
         * @param $idToSearch integer
         */
        function getPosts($method, $idToSearch) {
            $byCastId = new SearchByMethodEnum(SearchByMethodEnum::BYCASTID);
            $byUserId = new SearchByMethodEnum(SearchByMethodEnum::BYUSERID);
            $operationSuccess = OperationStatusEnum::NONE;
            $postListResults[] = ["status" => $operationSuccess];

            switch (strtoupper($method)){
                case $byCastId->getValue() :
//                    echo "By Cast ID";
                    $postList = $this->dao->getRecordById("castpostcommentmatrixindex", $idToSearch, ["castId", "postId"], "castId", ResultSetTypeEnum::MultiResultSet);
                    $postListResults = $this->removeEmpty2dElements($postList, "postId");

                    if(count($postListResults) == 0)
                        $postListResults[0][] = ["result" => "nothing found"];
                    else if(count($postListResults) > 0)
                        array_unshift($postListResults, ["status" => OperationStatusEnum::SUCCESS]);
                    else
                        $postListResults[0]["status"] = OperationStatusEnum::FAIL;

                    break;
                case $byUserId->getValue() :
//                    echo "By User ID";
                    $postList = $this->dao->getRecordById("usercreatedcontent", $idToSearch, ["postId", "subCastId"], "userId", ResultSetTypeEnum::MultiResultSet);
                    $postListResults = $this->removeEmpty2dElements($postList, "postId");

                    if(count($postListResults) == 0)
                        $postListResults[0][] = ["result" => "nothing found"];
                    else if(count($postListResults) > 0)
                        array_unshift($postListResults, ["status" => OperationStatusEnum::SUCCESS]);
                    else
                        $postListResults[0]["status"] = OperationStatusEnum::FAIL;

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
            if($_SESSION["sessionUserStatus"]["loginStatus"]){
                $creatorId = $this->dao->getRecordById("post", $postId, ["creatorUserId"], $idName = "postId")["creatorUserId"];
                if($_SESSION["sessionUserStatus"]["userId"] == $creatorId){
                    $updateResultSet = $this->dao->updateRecordById("post", ["description" => $edit], $postId, "postId");
                }
            }

            return ($updateResultSet > 0) ? OperationStatusEnum::SUCCESS : OperationStatusEnum::FAIL;
        }

        /**
         * @param $castId integer
         * @param $postId integer
         * @param $userId integer
         * @param $newAffinity integer (0 - no affinity, 1 - like, 2 - dislike)
         */
        function setUserPostAffinity($castId, $postId, $userId, $newAffinity){
            // Check if user has an affinity with the selected post
            $currentUserAffinity = $this->dao->getRecordsWhere("userLikeDislikeIndex", ["userId" => $userId, "castId" => $castId, "postId" => $postId], $columnsToSelect=["id", "castId", "postId", "userLike"]);
            // if user has an affinity
            var_dump($currentUserAffinity);
            if(isset($currentUserAffinity)){
                echo "set";
            } else {
                $operationResults = $this->dao->insertRecord("userLikeDislikeIndex", ["userId" => $userId, "castId" => $castId, "postId" => $postId, "userLike" => $newAffinity]);
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