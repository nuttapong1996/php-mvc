<?php
namespace App\Models\Auth;

use PDO;
use PDOException;

class AuthModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($username)
    {
        try {
            $stmt = $this->conn->prepare('SELECT username , user_role , password FROM tbl_users WHERE username  = :username LIMIT 1');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getCurPass(string $username): ?string
    {
        try {
            $stmt = $this->conn->prepare(
                'SELECT password FROM tbl_users WHERE username = :username LIMIT 1'
            );
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row['password'] ?? null;

        } catch (\PDOException $e) {
            error_log("DB Error [getCurPass]: " . $e->getMessage());
            return null;
        }
    }

    public function register($username, $password, $email ,$idenCode)
    {
        try {
            $stmt  = $this->conn->prepare('INSERT INTO tbl_users (username , password ,email ,iden_code) VALUES(:username,:password,:email ,:idencode)');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':idencode', $idenCode);
            $stmt->bindParam(':email', $email);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function forgot($username, $idenCode)
    {
        try {
            $stmt = $this->conn->prepare('SELECT username FROM tbl_users WHERE username =:username AND iden_code =:idencode');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':idencode', $idenCode, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function insertResetToken($username, $resetToken)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE tbl_users SET reset_token =:resetToken , reset_expires = NOW() + INTERVAL 5 MINUTE WHERE username =:username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':resetToken', $resetToken, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getResetToken($username, $resetToken)
    {
        try {
            $stmt = $this->conn->prepare('SELECT count(username) AS count FROM tbl_users WHERE username = :username AND reset_token = :resetToken AND reset_expires > NOW() LIMIT 1;');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':resetToken', $resetToken, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function resetPass($username, $password)
    {
        try {
            $stmt = $this->conn->prepare('UPDATE tbl_users SET password = :password , reset_at = NOW() ,reset_token = NULL , reset_expires = NULL WHERE username = :username');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
}
