<?php
namespace Authentication;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\JWT;
use Application\Env;
use Database\Connection;
use Mail\MailTo;

class Authenticate
{
/* =================================================================================================
        PUBLIC METHODS
================================================================================================= */
    /**
     * [loggedIn]
     *
     * Checks to see if the user is logged in or not
     *
     * @return [bool] [Either returns false or the valid tokenID]
     *
     */
    public static function loggedIn()
    {
        if(!Validate::validateToken())
        {
            header("Location: /admin/home?signin");
            die();
        }
    }

    /**
     * [logIn]
     *
     * Logs the user in
     *
     * @param  [string] $username
     * @param  [string] $password
     * @param  [string] $returnPage [Always set to /admin/home]
     * @return [mixed]              [Either redirection or array]
     *
     */
    public static function logIn($username, $password, $returnPage)
    {
        $conn = Connection::getConnection();
        $env = Env::fetchEnv();
        //Clean, Prepare, and Execute
        $cleanVals = Connection::mysqlClean(compact('username', 'password'));
        $stmt = $conn->prepare("SELECT `ID`, AES_DECRYPT(`username`, '$env[APP_ENCRYPT_KEY]') as
                                `username`, `password`, `lastsignin` FROM `Q_USERS` WHERE
                                AES_DECRYPT(`username`, '$env[APP_ENCRYPT_KEY]') = ? AND `active` = '1'
                                LIMIT 1");
        $stmt->bind_param('s', $cleanVals['username']);
        $stmt->execute();
        $stmt->store_result();


        if($stmt->num_rows > 0) {
            // User found, now just confirm password.
            $stmt->bind_result($id, $user, $pass, $lsi);
            $stmt->fetch();
            if(self::confirmPassword($cleanVals['password'], $pass)) {
                // Password is good, complete login
                $updateLastSignIn = "UPDATE `Q_USERS` SET `lastsignin` = NOW() WHERE
                    AES_DECRYPT(`username`, '$env[APP_ENCRYPT_KEY]') = '$cleanVals[username]' LIMIT 1";
                $conn->query($updateLastSignIn);
                self::generateToken(compact('id', 'user'));
                $stmt->free_result();
                $stmt->close();

                // Success
                header("Location: $returnPage");
                die();
            }else{
                $stmt->close();
                // Invalid password
                // header("Location: /admin/home?signin");
                return ["success" => false, "message" => "Invalid Password"];
            }
        } else {
            $stmt->close();
            //  User not found
            return ["success" => false, "message" => "No user exists with given username."];
            //header("Location: /admin/home?signin");
        }
    }

    /**
     * [logOut]
     *
     * Delete's the Quasar_Token cookie and redirects the user to /admin/home
     *
     * @return [void] [die()]
     *
     */
    public static function logOut()
    {
        setcookie("Quasar_Token", "", time()-1209600);
        header("location: /admin/home");
        die();
    }

    /**
     * [resetPass]
     *
     * Sends an email to the user with a newly generated password.
     * The user is instructed to change this password ASAP.
     *
     * @param  [string] $email  [the user's email]
     * @return [array]          [message to be displayed on the front end]
     */
    public static function resetPass($email)
    {
        $conn = Connection::getConnection();
        $env = Env::fetchEnv();
        $cleanVals = Connection::mysqlClean(compact('email'));
        $stmt = $conn->prepare("SELECT AES_DECRYPT(`username`, '$env[APP_ENCRYPT_KEY]') as `username`,
                                AES_DECRYPT(`email`, '$env[APP_ENCRYPT_KEY]') as `email`
                                FROM `Q_USERS`
                                WHERE `email` = AES_ENCRYPT(?, '$env[APP_ENCRYPT_KEY]')
                                LIMIT 1");
        $stmt->bind_param('s', $cleanVals['email']);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0)
        {
            $stmt->bind_result($fetched_username, $fetched_email);
            $stmt->fetch();

            $newPassword = self::setNewPass($cleanVals['email']);
            if(!$newPassword)
                return ["success" => false, "message" => "Your password could not be reset due to a
                    connection error."];
            // email new info
            if(MailTo::sendEmail(   $fetched_email,
                                    "Your Quasar CMS password has been reset.",
                                    MailTo::passwordResetMsg($fetched_username, $newPassword),
                                    "noreply@quasar.cms",
                                    "Quasar Support"))
                return ["success" => true, "message" => "Your password has been sent to $email."];
            else
                return ["success" => false, "message" => "There was an error. Your password
                    couldn't be reset. Please try again."];
        }
        else
            return ["success" => false, "message" => "We can't find an account with that email
                address."];
    }


    public static function changePass($oldPassword, $newPassword)
    {
        $conn = Connection::getConnection();
        $env = Env::fetchEnv();
        if($token = Validate::validateToken())
        {
            $cleanVals = Connection::mysqlClean(compact('oldPassword', 'newPassword'));
            $query = "SELECT AES_DECRYPT(`username`, '$env[APP_ENCRYPT_KEY]') as `username`,
                            AES_DECRYPT(`email`, '$env[APP_ENCRYPT_KEY]') as `email`, `password`
                        FROM `Q_USERS`
                        WHERE `token` = '$token'";
            $user = $conn->query($query);
            $user = $user->fetch_assoc();
            if(self::confirmPassword($cleanVals['oldPassword'], $user['password']))
            {
                $newHash = password_hash($cleanVals['newPassword'], CRYPT_BLOWFISH);
                $updatePass = $conn->prepare("UPDATE `Q_USERS` SET `password` = ?");
                $updatePass->bind_param('s', $newHash);
                if($updatePass->execute())
                {
                    // Send Mail!
                    MailTo::sendEmail(  $user['email'],
                                        "Your Quasar CMS password has been changed.",
                                        MailTo::passwordChangeMsg($user['username']),
                                        "noreply@quasar.cms",
                                        "Quasar Support");
                    return ["success" => true, "message" => "Password successfully updated"];
                }
                else
                    return ["success" => false, "message" => "Failed to execute password change."];
            }
            else
                return ["success" => false, "message" => "Invalid Password"];
        }
        return ["success" => false, "message" => "Invalid Token"];
    }

/* =================================================================================================
        PRIVATE METHODS
================================================================================================= */
    /**
     * [confirmPassword]
     *
     * Compares the user's inputted password against the hash in the database
     *
     * @param  [string] $password       [user input password]
     * @param  [string] $hashedPassword [database provided password]
     * @return [bool]                   [true or false]
     */
    private static function confirmPassword($password, $hashedPassword)
    {
        if(password_verify($password, $hashedPassword))
            return true;
        else
            return false;
    }

    /**
     * [createUniqueEntry]
     *
     * Recursive function to generate a unique tokenID for JWT encryption.
     * This is stored in the database to serve as validation later.
     *
     * @param  [database obj] $conn [Connection to the database]
     * @param  [string] $key        [Generated key to be tried]
     * @return [string]             [Unique key]
     */
    private static function createUniqueEntry($conn, $key)
    {
        $query = "SELECT `token` FROM `Q_USERS` WHERE `token` = '$key'";
        $results = $conn->query($query);
        if($results->num_rows > 0)
            return self::createUniqueEntry($conn, base64_encode(mcrypt_create_iv(32)));
        return $key;
    }

    /**
     * [generateToken]
     *
     * Upon logging in, the user is granted a brand new token, and it is set as a cookie.
     *
     * @param  [array] $user [basic user information]
     * @return [void]
     */
    private static function generateToken($user)
    {
        $conn = Connection::getConnection();
        $env = Env::fetchEnv();
        // Token stored in database
        $tokenID = self::createUniqueEntry($conn, base64_encode(mcrypt_create_iv(32)));
        $issuedAt = time();
        $notBefore = $issuedAt + 10;
        $expire = $notBefore + 1209600;             // lasts 2 weeks
        $serverName = $env['APP_HOST'];

        // Create token as an array
        $data = [
            'iat' => $issuedAt,                     // Timestamp of token
            'jti' => $tokenID,                      // Unique String
            'iss' => $serverName,                   // Name or identifier of the issuer
            'nbf' => $notBefore,                    // Timestamp of when the token should start being considered valid
            'exp' => $expire,                       // Timestamp of when the token should cease to be valid
            'data' => [                             // Optional data
                'ID' => $user['id'],
                'userName' => $user['user']
            ]
        ];

        $placeToken = "UPDATE `Q_USERS` SET `token` = '$tokenID' WHERE `ID` = " . $user['id'];
        $conn->query($placeToken);

        $jwt = JWT::encode($data, $env['APP_TOKEN_KEY'], 'HS512');
        $unencodedArray = ["Token" => $jwt];

        // Set cookie in the browser, lasts 2 weeks
        setcookie("Quasar_Token", json_encode($unencodedArray), time()+1209600);
    }

    /**
     * [randomString]
     *
     * Made for the use of TEMPORARY random strings, the user will always
     * be informed and be expected to change anything that is
     * "randomly" generated with this function.
     *
     * @param  [int] $length  [length of generated string]
     * @return [string]       [randomly generated string]
     */
    private static function randomString($length)
    {
        $numbers = "0123456789";
        $lowercase = "abcdefghijklmnopqrstuvwxyz";
        $uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $specials = ",.?/;:[]{}()<>\\|=+-_*&^%$#@!~";
        $randString = "";
        $int = 0;
        for ($i = 0; $i < $length; $i++)
        {
            // roll int, must be different from previously assigned value
            $temp = mt_rand(0, 3);
            while($int == $temp)
                $temp = mt_rand(0, 3);
            $int = $temp;
            switch($int)
            {
                case 0: {
                    $randString = self::placeRandomCharacter($randString, $numbers);
                    break;
                }
                case 1: {
                    $randString = self::placeRandomCharacter($randString, $lowercase);
                    break;
                }
                case 2: {
                    $randString = self::placeRandomCharacter($randString, $uppercase);
                    break;
                }
                default: {
                    $randString = self::placeRandomCharacter($randString, $specials);
                    break;
                }
            }
        }
        return $randString;
    }

    /**
     * [resetPassword]
     *
     * Performs the actual reset of a password for the user.
     *
     * @return [void]
     */
    private static function setNewPass($email)
    {
        $env = Env::fetchEnv();
        $newPassword = self::randomString(16); // reset password in database
        $resetQuery = "UPDATE `Q_USERS` SET `password` ='".password_hash($newPassword,
            CRYPT_BLOWFISH)."'WHERE email = AES_ENCRYPT('".$email."', '$env[APP_ENCRYPT_KEY]') LIMIT 1";
        if(Connection::executeQuery($resetQuery))
            return $newPassword;
        else
            return false;
    }

    /**
     * [placeRandomCharacter]
     *
     * Appends a random character within a haystack to the string
     * only if it is not currently in the haystack.
     *
     * @param  [string] $string   [current string]
     * @param  [char] $haystack   [array of possible char values to be added]
     * @return [string]           [current string with added char]
     */
    private static function placeRandomCharacter($string, $haystack)
    {
        $tempChar = $haystack[mt_rand(0, strlen($haystack) - 1)];
        while(substr_count($string, $tempChar) == 1)
            $tempChar = $haystack[mt_rand(0, strlen($haystack) - 1)];
        $string .= $tempChar;
        return $string;
    }
}

class Validate
{
    /**
     * [validateToken]
     *
     * Validates the existing cookie set in the browser against the ID set in the database
     * in addition to JWT protocol.
     *
     * @return [bool] [returns tokenID if cookie is still valid, false otherwise]
     */
    public static function validateToken()
    {
        $conn = Connection::getConnection();
        $env = Env::fetchEnv();
        if(isset($_COOKIE['Quasar_Token']))
        {
            $token = (array)json_decode($_COOKIE['Quasar_Token']);
            JWT::$leeway = 60;
            try
            {
                //echo "Attempting to decode Cookie...<br/>";
                $decoded = (array)JWT::decode($token['Token'], $env['APP_TOKEN_KEY'], array('HS512'));
                $findTokenID = "SELECT `token` FROM `Q_USERS` WHERE `token` = '$decoded[jti]'";
                //echo $findTokenID . "<br/>";
                $result = $conn->query($findTokenID);
                if($result->num_rows > 0)
                    return $decoded['jti'];
            }
            catch(Exception $e)
            {
                return false;
            }
        }
        //echo "Cookie not set <br/>";
        return false;
    }

    /**
     * [validateAuth]
     *
     * Validates the user's authority level, this is executed before most admin level
     * functions to confirm if the user has the authority to execute such a function.
     *
     * @param  [int]requiredAuth [The authority level required to execute the function]
     * @return [bool] [returns true or false depending on the user's authority level]
     */
    public static function validateAuth($requiedAuth)
    {
        if($token = self::validateToken())
        {
            $select = ["unencrypted" => ["permissionID"]];
            $from = "Q_USERS";
            $where = ["token" => $token];
            $result = Connection::decryptAndShow(compact("select", "from", "where"));
            if($result['permissionID'] >= $requiedAuth)
                return true;
            else
                return false;
        }
        else
            return false;
    }
}