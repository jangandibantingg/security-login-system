<?php

/**
 * Advanced Security - PHP Register/Login System
 *
 * @author Milos Stojanovic
 * @link   http://mstojanovic.net/as
 */

/**
 * Comments class.
 */
class ASComment
{
    /**
     * @var ASDatabase
     */
    private $db = null;

    /**
     * @var ASUser
     */
    private $users;

    /**
     * Class constructor
     * @param ASDatabase $db
     * @param ASUser $users
     */
    public function __construct(ASDatabase $db, ASUser $users)
    {
        $this->db = $db;
        $this->users = $users;
    }

    /**
     * Inserts comment into database.
     * @param int $userId Id of user who is posting the comment.
     * @param string $comment Comment text.
     * @return string JSON encoded string that consist of 3 fields:
     * user,comment and postTime
     */
    public function insertComment($userId, $comment)
    {
        $userInfo = $this->users->getInfo($userId);
        $datetime = date("Y-m-d H:i:s");

        $this->db->insert("as_comments", array(
            "posted_by" => $userId,
            "posted_by_name" => $userInfo['username'],
            "comment" => strip_tags($comment),
            "post_time" => $datetime
        ));

        return json_encode(array(
            "user" => $userInfo['username'],
            "comment" => stripslashes(strip_tags($comment)),
            "postTime" => $datetime
        ));
    }

    /**
     * Return all comments left by one user.
     * @param int $userId Id of user.
     * @return array Array of all user's comments.
     */
    public function getUserComments($userId)
    {
        $result = $this->db->select(
            "SELECT * FROM `as_comments` WHERE `posted_by` = :id",
            array("id" => $userId)
        );

        return $result;
    }


    /**
     * Return last $limit (default 7) comments from database.
     * @param int $limit Required number of comments.
     * @return array Array of comments.
     */
    public function getComments($limit = 7)
    {
        $limit = (int) $limit;

        return $this->db->select("SELECT * FROM `as_comments` ORDER BY `post_time` DESC LIMIT $limit");
    }
}
