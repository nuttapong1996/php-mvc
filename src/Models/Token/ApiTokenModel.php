<?php

namespace App\Models\Token;

use PDO;

class ApiTokenModel
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getApiTokenByUserCode($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT token, token_id, usage_count ,created_at , last_usage FROM tbl_api_tokens WHERE owner =:username");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getApiTokenByToken($Token)
    {
        try {
            $stmt = $this->conn->prepare("SELECT token, token_id, owner, usage_count FROM tbl_api_tokens");
            $stmt->execute();
            $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tokens as $row) {
                if (password_verify($Token, $row['token'])) {
                    return $row;
                }
            }

            return false; // ไม่มี token ตรงกัน

        } catch (\Exception $e) {
            return false;
        }
    }

    public function insertApiToken($username, $token, $tokenid, $ip)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO tbl_api_tokens (token , owner , token_id ,ip) VALUES(:token,:username ,:tokenid ,:ip)");
            $stmt->BindParam(':token', $token, PDO::PARAM_STR);
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':tokenid', $tokenid, PDO::PARAM_STR);
            $stmt->BindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateApiTokenUsageTime($username, $tokenid, $useNum)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE tbl_api_tokens SET last_usage = NOW() ,usage_count =:usenum WHERE owner =:username AND token_id =:tokenid");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':tokenid', $tokenid, PDO::PARAM_STR);
            $stmt->BindParam(':usenum', $useNum, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteApiToken($username, $tokenid)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM tbl_api_tokens WHERE owner =:username AND token_id =:tokenid");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':tokenid', $tokenid, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (\Exception $e) {
            return false;
        }
    }
}
