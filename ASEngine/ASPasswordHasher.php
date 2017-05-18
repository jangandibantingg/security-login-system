<?php


class ASPasswordHasher
{
    /**
     * Hash provided password.
     * @param $password
     * @return string
     */
    public function hashPassword($password)
    {
        //this salt will be used in both algorithms
        //for bcrypt it is required to look like this,
        //for sha512 it is not required but it can be used.
        $salt = "$2a$" . PASSWORD_BCRYPT_COST . "$" . PASSWORD_SALT;

        if (PASSWORD_ENCRYPTION == "bcrypt") {
            return crypt($password, $salt);
        }

        $newPassword = $password;
        for ($i = 0; $i < PASSWORD_SHA512_ITERATIONS; $i++) {
            $newPassword = hash('sha512', $salt.$newPassword.$salt);
        }

        return $newPassword;
    }
}