<?php
namespace App\Models\Token;

use PDO;
use PDOException;

class TokenModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getRefreshTokenByID($username, $token_id)
    {
        try {
            $stmt = $this->conn->prepare('SELECT * FROM tbl_refresh_tokens WHERE username = :username AND token_id = :tokenid AND revoked = 0 AND expires_at > NOW() ORDER BY rfsh_id DESC LIMIT 1');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':tokenid', $token_id, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRefreshTokenList($username)
    {
        try {
            $stmt = $this->conn->prepare('SELECT * FROM tbl_refresh_tokens WHERE username = :username AND revoked = 0 ORDER BY rfsh_id ASC ');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getExpiresToken($username)
    {
        try {
            $stmt = $this->conn->prepare('SELECT * FROM tbl_refresh_tokens WHERE username = :username  AND expires_at < NOW()');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRevokeToken($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM tbl_refresh_tokens WHERE username = :username  AND revoked = 1 OR expires_at < NOW() - INTERVAL 7 DAY");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function insertRefreshToken($username, $token_id, $refresh_token, $device, $ip, $expires)
    {
        try {
            $stmt = $this->conn->prepare('INSERT INTO tbl_refresh_tokens (username, token_id,token, device_name, ip_address,create_at, expires_at) VALUES (:username, :token_id, :token, :device , :ip ,NOW(),:expires)');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':token_id', $token_id, PDO::PARAM_STR);
            $stmt->BindParam(':token', $refresh_token, PDO::PARAM_STR);
            $stmt->BindParam(':device', $device, PDO::PARAM_STR);
            $stmt->BindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->BindParam(':expires', $expires, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateToken($username, $token_id, $refresh_token, $device, $ip, $expires)
    {
        try {
            $stmt = $this->conn->prepare('UPDATE tbl_refresh_tokens SET token = :token, device_name =:device ,ip_address=:ip,update_at=NOW(),expires_at=:expires WHERE username = :username AND token_id = :tokenid');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':tokenid', $token_id, PDO::PARAM_STR);
            $stmt->BindParam(':token', $refresh_token, PDO::PARAM_STR);
            $stmt->BindParam(':device', $device, PDO::PARAM_STR);
            $stmt->BindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->BindParam(':expires', $expires, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function setExpiredToken($username)
    {
        try {
            $stmt = $this->conn->prepare('UPDATE tbl_refresh_tokens SET remark="Expired" WHERE username = :username AND expires_at < NOW()');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function setRevokeToken($username, $tokenid, $remark)
    {
        try {
            $stmt = $this->conn->prepare('UPDATE tbl_refresh_tokens SET revoked = 1 , revoked_at = NOW(), remark = :token_remark WHERE username = :username AND token_id = :tokenid AND revoked = 0');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':tokenid', $tokenid, PDO::PARAM_STR);
            $stmt->BindParam(':token_remark', $remark, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteExpiredToken($username)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM tbl_refresh_tokens WHERE username = :username AND revoked = 1 OR expires_at < NOW() - INTERVAL 7 DAY"); // Delete in 7 day
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteTokenByID($username, $tokenid)
    {
        try {
            $stmt = $this->conn->prepare('DELETE FROM tbl_refresh_tokens WHERE username = :username AND token_id = :tokenid');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':tokenid', $tokenid, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteAllToken($username)
    {
        try {
            $stmt = $this->conn->prepare('DELETE FROM tbl_refresh_tokens WHERE username = :username');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
}
