<?php

/**
 * Advanced Security - PHP Register/Login System
 *
 * @author Milos Stojanovic
 * @link   http://mstojanovic.net/as
 */

/**
 * User registration class.
 *
 */
class ASRegister
{
    /**
     * @var ASEmail Instance of ASEmail class
     */
    private $mailer;

    /**
     * @var ASDatabase Instance of ASDatabase class
     */
    private $db = null;

    /**
     * @var ASValidator
     */
    private $validator;

    /**
     * @var ASLogin
     */
    private $login;

    /**
     * @var ASPasswordHasher
     */
    private $hasher;

    /**
     * Class constructor
     * @param ASDatabase $db
     * @param ASEmail $mailer
     * @param ASValidator $validator
     * @param ASLogin $login
     * @param ASPasswordHasher $hasher
     */
    public function __construct(
        ASDatabase $db,
        ASEmail $mailer,
        ASValidator $validator,
        ASLogin $login,
        ASPasswordHasher $hasher
    ) {
        $this->db = $db;
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->login = $login;
        $this->hasher = $hasher;
    }
    
    /**
     * Register user.
     * @param array $data User details provided during the registration process.
     */
    public function register($data)
    {
        $user = $data['userData'];
        
        //validate provided data
        $errors = $this->validateUser($data);
        
        if (count($errors) > 0) {
            return respond(array(
                "status" => "error",
                "errors" => $errors
            ));
        }

        //generate email confirmation key
        $key = $this->generateKey();

        MAIL_CONFIRMATION_REQUIRED ? $confirmed = 'N' : $confirmed = 'Y';

        //insert new user to database
        $this->db->insert('as_users', array(
            "email"     => $user['email'],
            "username"  => strip_tags($user['username']),
            "password"  => $this->hashPassword($user['password']),
            "confirmed" => $confirmed,
            "confirmation_key" => $key,
            "register_date" => date("Y-m-d")
        ));

        $userId = $this->db->lastInsertId();

        $this->db->insert('as_user_details', array('user_id' => $userId));

        //send confirmation email if needed
        if (MAIL_CONFIRMATION_REQUIRED) {
            $this->mailer->confirmationEmail($user['email'], $key);
            $msg = trans('success_registration_with_confirm');
        } else {
            $msg = trans('success_registration_no_confirm');
        }

        //prepare and output success message
        return respond(array(
            "status" => "success",
            "msg" => $msg
        ));
    }

    /**
     * Get user by email.
     * @param $email string User's email
     * @return mixed User info if user with provided email exist, empty array otherwise.
     */
    public function getByEmail($email)
    {
        $result = $this->db->select(
            "SELECT * FROM `as_users` WHERE `email` = :e",
            array('e' => $email)
        );

        if (count($result) > 0) {
            return $result[0];
        }

        return $result;
    }


    /**
     * Check if user has already logged in via specific provider and return user's data if he does.
     * @param $provider string oAuth provider (Facebook, Twitter or Gmail)
     * @param $id string Identifier provided by provider
     * @return array|mixed User info if user has already logged in via specific provider, empty array otherwise.
     */
    public function getBySocial($provider, $id)
    {
        $result = $this->db->select(
            'SELECT as_users.*
            FROM as_social_logins, as_users 
            WHERE as_social_logins.provider = :p AND as_social_logins.provider_id = :id
            AND as_users.user_id = as_social_logins.user_id',
            array('p' => $provider, 'id' => $id)
        );

        if (count($result) > 0) {
            return $result[0];
        }

        return $result;
    }

    /**
     * Check if user is already registered via some social network.
     * @param $provider string Name of the provider ( twitter, facebook or google )
     * @param $id string Provider identifier
     * @return bool TRUE if user exist in database (already registred), FALSE otherwise
     */
    public function registeredViaSocial($provider, $id)
    {
        $result = $this->getBySocial($provider, $id);

        if (count($result) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Connect user's social account with his account at this system.
     * @param $userId int User Id on this system
     * @param $provider string oAuth provider (Facebook, Twitter or Gmail)
     * @param $providerId string Identifier provided by provider.
     */
    public function addSocialAccount($userId, $provider, $providerId)
    {
        $this->db->insert('as_social_logins', array(
            'user_id' => $userId,
            'provider' => $provider,
            'provider_id' => $providerId,
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Send forgot password email.
     * @param string $userEmail Provided email.
     * @return bool|mixed|string
     */
    public function forgotPassword($userEmail)
    {
        //we only have one field to validate here
        //so we don't need id's from other fields
        if ($userEmail == "") {
            return trans('email_required');
        }

        if (! $this->validator->emailValid($userEmail)) {
            return trans('email_wrong_format');
        }
        
        if (! $this->validator->emailExist($userEmail)) {
            return trans('email_not_exist');
        }

        if ($this->login->isBruteForce()) {
            return trans('brute_force');
        }
        
        //ok, no validation errors, we can proceed

        //generate password reset key
        $key = $this->generateKey();

        //write key to db
        $this->db->update(
            'as_users',
            array(
                "password_reset_key" => $key,
                "password_reset_confirmed" => 'N',
                "password_reset_timestamp" => date('Y-m-d H:i:s')
            ),
            "`email` = :email",
            array("email" => $userEmail)
        );

        $this->login->increaseLoginAttempts();

        //send email
        $this->mailer->passwordResetEmail($userEmail, $key);

        return true;
    }
    
    
    /**
     * Reset user's password if password reset request has been made.
     * @param string $newPass New password.
     * @param string $passwordResetKey Password reset key sent to user
     * in password reset email.
     */
    public function resetPassword($newPass, $passwordResetKey)
    {
        if (! $this->validator->prKeyValid($passwordResetKey)) {
            echo 'Invalid password reset key!';
            return;
        }

        $pass = $this->hashPassword($newPass);

        $this->db->update(
            'as_users',
            array("password" => $pass, 'password_reset_confirmed' => 'Y', 'password_reset_key' => ''),
            "`password_reset_key` = :prk ",
            array("prk" => $passwordResetKey)
        );
    }

    /**
     * Hash given password.
     * @param string $password Un-hashed password.
     * @return string Hashed password.
     */
    public function hashPassword($password)
    {
        return $this->hasher->hashPassword($password);
    }

    /**
     * Generate two random numbers and store them into $_SESSION variable.
     * Numbers are used during the registration to prevent bots to register.
     */
    public function botProtection()
    {
        ASSession::set("bot_first_number", rand(1, 9));
        ASSession::set("bot_second_number", rand(1, 9));
    }

    /**
     * Validate user provided fields.
     * @param $data array User provided fields and id's of those fields that will be
     * used for displaying error messages on client side.
     * @param bool $botProtection Should bot protection be validated or not
     * @return array Array with errors if there are some, empty array otherwise.
     */
    public function validateUser($data, $botProtection = true)
    {
        $id = $data['fieldId'];
        $user = $data['userData'];
        $errors = array();

        //check if email is not empty
        if ($this->validator->isEmpty($user['email'])) {
            $errors[] = array(
                "id" => $id['email'],
                "msg" => trans('email_required')
            );
        }
        
        //check if username is not empty
        if ($this->validator->isEmpty($user['username'])) {
            $errors[] = array(
                "id" => $id['username'],
                "msg" => trans('username_required')
            );
        }
        
        // Check if password is not empty.
        // We cannot check the password length since it is SHA 512 hashed
        // before it is even sent to the server.
        if ($this->validator->isEmpty($user['password'])) {
            $errors[] = array(
                "id" => $id['password'],
                "msg" => trans('password_required')
            );
        }
        
        //check if password and confirm password are the same
        if ($user['password'] != $user['confirm_password']) {
            $errors[] = array(
                "id" => $id['confirm_password'],
                "msg" => trans('passwords_dont_match')
            );
        }
        
        //check if email format is correct
        if (! $this->validator->emailValid($user['email'])) {
            $errors[] = array(
                "id" => $id['email'],
                "msg" => trans('email_wrong_format')
            );
        }
        
        //check if email is available
        if ($this->validator->emailExist($user['email'])) {
            $errors[] = array(
                "id" => $id['email'],
                "msg" => trans('email_taken')
            );
        }
        
        //check if username is available
        if ($this->validator->usernameExist($user['username'])) {
            $errors[] = array(
                "id" => $id['username'],
                "msg" => trans('username_taken')
            );
        }
        
        if ($botProtection) {
            //bot protection
            $sum = ASSession::get("bot_first_number") + ASSession::get("bot_second_number");
            if ($sum != intval($user['bot_sum'])) {
                $errors[] = array(
                    "id" => $id['bot_sum'],
                    "msg" => trans('wrong_sum')
                );
            }
        }
        
        return $errors;
    }

    /**
     * Generates random password
     * @param int $length Length of generated password
     * @return string Generated password
     */
    public function randomPassword($length = 7)
    {
        return str_random($length);
    }

    /**
     * Generate random token that will be used for social authentication
     * @return string Generated token.
     */
    public function socialToken()
    {
        return str_random(40);
    }

    /**
     * Generate key used for confirmation and password reset.
     * @return string Generated key.
     */
    private function generateKey()
    {
        return md5(time() . PASSWORD_SALT . time());
    }
}
