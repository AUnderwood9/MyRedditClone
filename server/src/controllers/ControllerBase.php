<?php
/**
 * Created by PhpStorm.
 * User: Wr8konedOne
 * Date: 9/23/2018
 * Time: 12:59 PM
 */

Abstract class ControllerBase
{
    protected $dao;

    function __construct(DaoManagerInterface $dbConnection){
        $this->dao = $dbConnection;
    }
}