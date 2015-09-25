<?php

namespace BitPrepared\Security;

class SecureHash
{

    /**
    * Creates a very secure hash. Uses blowfish by default with a fallback on SHA512.
    *
    * Usage of CRYPT_BLOWFISH:
    * -----------------------
    * Blowfish hashing with a salt is as follows:
    * "$2a$" + a two digit cost parameter + "$" + 22 digits from the base64 alphabet
    * "./0-9A-Za-z" + "$".
    *
    * Using characters outside of this range in the salt will cause crypt() to return
    * a zero-length string. The two digit cost parameter is the base-2 logarithm
    * of the iteration count for the underlying Blowfish-based hashing algorithmeter
    * and must be in range 04-31, values outside this range will cause crypt() to fail.
    *
    * @access public
    * @param string $password
    * @param string $salt
    * @param int $stretch_cost
    */
    public function create_hash($password, &$salt = '', $stretch_cost = 10)
    {

        $salt = strlen($salt) != 21 ? $this->_create_salt() : $salt;

        if (function_exists('crypt') && defined('CRYPT_BLOWFISH')) {
            return crypt($password, '$2a$' . $stretch_cost . '$' . $salt . '$');
        }

        // fallback encryption
        if (!function_exists('hash') || !in_array('sha512', hash_algos())) {
            throw new Exception('You must have the PHP PECL hash module installed or use PHP 5.1.2+');
        }

        return $this->_create_hash($password, $salt);
    }

    /**
    * @param string $pass The user submitted password
    * @param string $hashed_pass The hashed password pulled from the database
    * @param string $salt The salt used to generate the encrypted password
    */
    public function validate_hash($pass, $hashed_pass, $salt)
    {
        return $hashed_pass === $this->create_hash($pass, $salt);
    }

    /**
    * Create a new salt string which conforms to the requirements of CRYPT_BLOWFISH.
    *
    * @access protected
    * @return string
    */
    protected function _create_salt()
    {
        $salt = $this->_pseudo_rand(128);

        return substr(preg_replace('/[^A-Za-z0-9_]/is', '.', base64_encode($salt)), 0, 21);
    }

    /**
    * Generates a secure, pseudo-random password with a safe fallback.
    *
    * @access public
    * @param int $length
    */
    protected function _pseudo_rand($length)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $is_strong = false;
            $rand = openssl_random_pseudo_bytes($length, $is_strong);
            if ($is_strong === true) return $rand;
        }

        $rand = '';
        $sha = '';

        for ($i = 0; $i < $length; $i++) {
            $sha = hash('sha256', $sha . mt_rand());
            $chr = mt_rand(0, 62);
            $rand .= chr(hexdec($sha[$chr] . $sha[$chr + 1]));
        }

        return $rand;
    }

    /**
    * Fall-back SHA512 hashing algorithm with stretching.
    *
    * @access private
    * @param string $password
    * @param string $salt
    * @return string
    */
    private function _create_hash($password, $salt)
    {
        $hash = '';
        for ($i = 0; $i < 20000; $i++) {
            $hash = hash('sha512', $hash . $salt . $password);
        }

        return $hash;
    }

}