<?php

require_once "ControllerBase.php";

    class UserContentRelation extends ControllerBase{
        public function __construct(DaoManagerInterface $dbConnection)
        {
            parent::__construct($dbConnection);
        }

        /**
         * Checks to see if the user owns a contnet by its type. If the user does own the content then the id of that content is returned;
         * @param $idOptionalObject object
         * @param $contentTypeToCheck string
         *
         * return integer
         */
        function doesUserOwn($idOptionalObject, $contentTypeToCheck){
            $contentTypeCast = new UserContentType(UserContentType::CAST);
            $contentTypePost = new UserContentType(UserContentType::POST);
            $contentTypeComment = new UserContentType(UserContentType::COMMENT);

            $resultSet = [];

            switch (strtoupper($contentTypeToCheck)){
                case $contentTypeCast->getValue() :
                    $resultSet = $this->dao->getRecordsWhere("userCreatedContent",["userId" => $idOptionalObject->userId, "subCastId" => $idOptionalObject->castId], ["subCastId"]);
                    break;

                case $contentTypePost->getValue() :
                    $resultSet = $this->dao->getRecordsWhere("userCreatedContent",["userId" => $idOptionalObject->userId, "subCastId" => $idOptionalObject->castId, "postId" => $idOptionalObject->postId], ["subCastId", "postId"]);
                    break;

                case $contentTypeComment->getValue() :
                    $resultSet = $this->dao->getRecordsWhere("userCreatedContent",["userId" => $idOptionalObject->userId, "subCastId" => $idOptionalObject->castId, "postId" => $idOptionalObject->postId, "commentId" => $idOptionalObject->commentId], ["subCastId", "postId", "commentId"]);
                    break;
            }

            return $resultSet;
        }
    }

?>