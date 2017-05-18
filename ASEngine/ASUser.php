<?php

/**
 * Advanced Security - PHP Register/Login System
 *
 * @author Milos Stojanovic
 * @link   http://mstojanovic.net/as
 */

/**
 * User class.
 */
class ASUser
{
    /**
     * @var ASDatabase Instance of ASDatabase class
     */
    private $db;
    /**
     * @var ASPasswordHasher
     */
    private $hasher;
    /**
     * @var ASValidator
     */
    private $validator;
    /**
     * @var ASLogin
     */
    private $login;
    /**
     * @var ASRegister
     */
    private $registrator;

    /**
     * Class constructor
     * @param ASDatabase $db
     * @param ASPasswordHasher $hasher
     * @param ASValidator $validator
     * @param ASLogin $login
     * @param ASRegister $registrator
     */
    public function __construct(
        ASDatabase $db,
        ASPasswordHasher $hasher,
        ASValidator $validator,
        ASLogin $login,
        ASRegister $registrator
    ) {
        $this->db = $db;
        $this->hasher = $hasher;
        $this->validator = $validator;
        $this->login = $login;
        $this->registrator = $registrator;
    }

    /**
     * Get all user details including email, username and last_login
     * @param $userId int User's id.
     * @return array User details or null if user with given id doesn't exist.
     */
    public function getAll($userId)
    {
        $query = "SELECT `as_users`.`email`, `as_users`.`username`,`as_users`.`last_login`, `as_user_details`.*
                  FROM `as_users`, `as_user_details`
                  WHERE `as_users`.`user_id` = :id
                  AND `as_users`.`user_id` = `as_user_details`.`user_id`";

        $result = $this->db->select($query, array('id' => $userId));

        if (count($result) > 0) {
            return $result[0];
        }

        return null;
    }

    public static function getAdmin()
    {
        $result = $db->select(
            "SELECT * FROM `as_users`
            INNER JOIN as_user_roles ON as_users.user_role = as_user_roles.role_id
            WHERE as_user_roles.role = :role",
            array('role' => 'admin')
        );

        if (count($result) > 0) {
            return $result[0];
        }

        return null;
    }

    /**
     * Add new user using data provided by administrator from admin panel.
     * @param $postData array All data filled in administrator's "Add User" form
     * @return array Result that contain status (error or success) and message.
     */
    public function add($postData)
    {
        $errors = $this->registrator->validateUser($postData, false);

        // if count ($errors) > 0 means that validation
        // didn't passed and that there are errors
        if (count($errors) > 0) {
            respond(array(
                "status" => "error",
                "errors" => $errors
            ));
        }

        //validation passed
        $data = $postData['userData'];

        // insert user login info
        $this->db->insert('as_users', array(
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $this->hashPassword($data['password']),
            'confirmed' => 'Y',
            'confirmation_key' => '',
            'register_date' => date('Y-m-d H:i:s')
        ));

        // get user id
        $id = $this->db->lastInsertId();

        // insert users details
        $this->db->insert('as_user_details', array(
            'user_id' => $id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'address' => $data['address']
        ));

        // generate response
        respond(array(
            "status" => "success",
            "msg" => trans("user_added_successfully")
        ));
    }

    /**
     * Update user's details.
     * @param $userId int User's id.
     * @param $data array User data from admin's "edit user" form
     */
    public function updateUser($userId, array $data)
    {
        $currInfo = $this->getInfo($userId);

        // validate data
        $errors = $this->validateUserUpdate($currInfo, $data);

        if (count($errors) > 0) {
            respond(array(
                "status" => "error",
                "errors" => $errors
            ));
        }

        // validation passed, update user
        $userData = $data['userData'];

        $userInfo = array();

        // update user's email and username only if they are changed, skip them otherwise
        if ($currInfo['email'] != $userData['email']) {
            $userInfo['email'] = $userData['email'];
        }

        if ($currInfo['username'] != $userData['username']) {
            $userInfo['username'] = $userData['username'];
        }

        // update password only if "password" field is filled
        // and password is different than current password
        if ($userData['password'] != hash('sha512', '')) {
            $password = $this->hashPassword($userData['password']);

            if ($currInfo['password'] !== $password) {
                $userInfo['password'] = $password;
            }
        }

        if (count($userInfo) > 0) {
            $this->updateInfo($userId, $userInfo);
            ASSession::regenerate();
        }

        $this->updateDetails($userId, array(
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'phone' => $userData['phone'],
            'address' => $userData['address']
        ));

        respond(array(
            "status" => "success",
            "msg" => trans("user_updated_successfully")
        ));
    }

    /**
     * Check if user with provided id is admin.
     * @param $userId User's id.
     * @return bool TRUE if user is admin, FALSE otherwise.
     */
    public function isAdmin($userId)
    {
        if ($userId == null) {
            return false;
        }

        if (strtolower($this->getRole($userId)) === "admin") {
            return true;
        }

        return false;
    }

    /**
     * Updates user's password.
     * @param string $oldPass Old password.
     * @param string $newPass New password.
     */
    public function updatePassword($userId, $oldPass, $newPass)
    {
        //hash both passwords
        $oldPass = $this->hashPassword($oldPass);
        $newPass = $this->hashPassword($newPass);
        
        //get user info (email, password etc)
        $info = $this->getInfo($userId);

        //update if entered old password is correct
        if ($oldPass === $info['password']) {
            $this->updateInfo($userId, array("password" => $newPass));
            ASSession::regenerate();
            return;
        }

        echo trans('wrong_old_password');
    }

    /**
     * Changes user's role. If user's role was editor it will be set to user and vice versa.
     * @param $userId User's id.
     * @param $role New user's role.
     * @return string New user role.
     */
    public function changeRole($userId, $role)
    {
        $result = $this->db->select(
            "SELECT * FROM `as_user_roles` WHERE `role_id` = :r",
            array("r" => $role)
        );

        if (count($result) == 0) {
            return null;
        }

        $this->updateInfo($userId, array("user_role" => $role));

        ASSession::regenerate();

        return $result[0]['role'];
    }

    /**
     * Get current user's role.
     * @param $userId
     * @return string Current user's role.
     */
    public function getRole($userId)
    {
        $result = $this->db->select(
            "SELECT `as_user_roles`.`role` as role 
            FROM `as_user_roles`,`as_users`
            WHERE `as_users`.`user_role` = `as_user_roles`.`role_id`
            AND `as_users`.`user_id` = :id",
            array("id" => $userId)
        );

        return $result[0]['role'];
    }

    /**
     * Get basic user info provided during registration.
     * @param $userId int User's unique id.
     * @return array User info array.
     */
    public function getInfo($userId)
    {
        $result = $this->db->select(
            "SELECT * FROM `as_users` WHERE `user_id` = :id",
            array("id" => $userId)
        );

        if (count($result) > 0) {
            return $result[0];
        }

        return null;
    }

    /**
     * Updates user info.
     * @param $userId int User's unique id.
     * @param array $data Associative array where keys are database fields that need
     * to be updated and values are new values for provided database fields.
     */
    public function updateInfo($userId, $data)
    {
        $this->db->update(
            "as_users",
            $data,
            "`user_id` = :id",
            array("id" => $userId)
        );
    }

    /**
     * Get user details (First Name, Last Name, Address and Phone)
     * @param $userId int User's id.
     * @return array User details array.
     */
    public function getDetails($userId)
    {
        $result = $this->db->select(
            "SELECT * FROM `as_user_details` WHERE `user_id` = :id",
            array("id" => $userId)
        );

        if (count($result) == 0) {
            return array(
                "first_name" => "",
                "last_name" => "",
                "address" => "",
                "phone" => "",
                "empty" => true
            );
        }

        return $result[0];
    }


    /**
     * Updates user details.
     * @param $userId
     * @param array $details Associative array where keys are database fields that need
     * to be updated and values are new values for provided database fields.
     */
    public function updateDetails($userId, $details)
    {
        $currDetails = $this->getDetails($userId);

        if (isset($currDetails['empty'])) {
            $details["user_id"] = $userId;
            return $this->db->insert("as_user_details", $details);
        }

        return $this->db->update(
            "as_user_details",
            $details,
            "`user_id` = :id",
            array("id" => $userId)
        );
    }

    
    /**
     * Delete user, all his comments and connected social accounts.
     */
    public function deleteUser($userId)
    {
        $this->db->delete("as_users", "user_id = :id", array("id" => $userId));
        $this->db->delete("as_user_details", "user_id = :id", array("id" => $userId));
        $this->db->delete("as_comments", "posted_by = :id", array("id" => $userId));
        $this->db->delete("as_social_logins", "user_id = :id", array("id" => $userId));
    }

    /**
     * Validate data provided during user update
     * @param $userInfo
     * @param $data
     * @return array
     */
    private function validateUserUpdate($userInfo, $data)
    {
        $id = $data['fieldId'];
        $user = $data['userData'];
        $errors = array();

        if ($userInfo == null) {
            $errors[] = array(
                "id"    => $id['email'],
                "msg"   => trans('user_dont_exist')
            );
            return $errors;
        }

        //check if email is not empty
        if ($this->validator->isEmpty($user['email'])) {
            $errors[] = array(
                "id"    => $id['email'],
                "msg"   => trans('email_required')
            );
        }

        //check if username is not empty
        if ($this->validator->isEmpty($user['username'])) {
            $errors[] = array(
                "id"    => $id['username'],
                "msg"   => trans('username_required')
            );
        }

        //check if password and confirm password are the same
        if (! $user['password'] == hash('sha512', '') && ($user['password'] != $user['confirm_password'])) {
            $errors[] = array(
                "id"    => $id['confirm_password'],
                "msg"   => trans('passwords_dont_match')
            );
        }

        //check if email format is correct
        if (! $this->validator->emailValid($user['email'])) {
            $errors[] = array(
                "id"    => $id['email'],
                "msg"   => trans('email_wrong_format')
            );
        }

        //check if email is available
        if ($user['email'] != $userInfo['email'] && $this->validator->emailExist($user['email'])) {
            $errors[] = array(
                "id"    => $id['email'],
                "msg"   => trans('email_taken')
            );
        }

        //check if username is available
        if ($user['username'] != $userInfo['username'] && $this->validator->usernameExist($user['username'])) {
            $errors[] = array(
                "id"    => $id['username'],
                "msg"   => trans('username_taken')
            );
        }

        return $errors;
    }
    
    /**
     * Hash provided password.
     * @param string $password Password that needs to be hashed.
     * @return string Hashed password.
     */
    private function hashPassword($password)
    {
        return $this->hasher->hashPassword($password);
    }
}
