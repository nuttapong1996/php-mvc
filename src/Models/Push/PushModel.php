<?php

namespace App\Models\Push;

use PDO;
use PDOException;

class PushModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Add Subscription 
    public function insertSub($username, $empDevice, $empIp, $subCode, $endPoint, $publicKey, $authKey)
    {
        try {
            $stmt = $this->conn->prepare('INSERT INTO tbl_subscribers(username, device_name , ip_address ,sub_code ,endpoint,pub_key,auth_key)  VALUES (:username,:device,:ip,:subcode,:endpnt,:pub_key,:auth_key)');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':device', $empDevice, PDO::PARAM_STR);
            $stmt->BindParam(':ip', $empIp, PDO::PARAM_STR);
            $stmt->BindParam(':subcode', $subCode, PDO::PARAM_STR);
            $stmt->BindParam(':endpnt', $endPoint, PDO::PARAM_STR);
            $stmt->BindParam(':pub_key', $publicKey, PDO::PARAM_STR);
            $stmt->BindParam(':auth_key', $authKey, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get Sub by endpoint (Check current subscription)
    public function getSubByUserEndpoint($username, $endPoint)
    {
        try {
            $stmt = $this->conn->prepare('SELECT endpoint , pub_key , auth_key FROM tbl_subscribers WHERE username = :username AND endpoint =:endpnt LIMIT 1');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':endpnt', $endPoint, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Delete Sub by endpoint (Unsubscribe current subscription and delete subscription from database)
    public function deleteSubByUserEndpoint($username, $endPoint)
    {
        try {
            $stmt = $this->conn->prepare('DELETE FROM tbl_subscribers WHERE username =:username AND endpoint =:endPoint');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':endPoint', $endPoint, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Delete Sub by sub_id (Delete subscription from database by code)
    public function deleteSubBySubCode($username, $subCode)
    {
        try {
            $stmt = $this->conn->prepare('DELETE FROM tbl_subscribers WHERE username =:username AND sub_code =:subcode');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->BindParam(':subcode', $subCode, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Delete Sub by sub_id (Delete subscription from database by code)
    public function deleteSubByEndpoint($endPoint)
    {
        try {
            $stmt = $this->conn->prepare('DELETE FROM tbl_subscribers WHERE endpoint =:endPoint ');
            $stmt->BindParam(':endPoint', $endPoint, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get Sub List by username 
    public function getSubListByUserCode($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM tbl_subscribers WHERE username =:username");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get Sub list  from all user
    public function getAllSub()
    {
        try {
            $stmt = $this->conn->prepare('SELECT endpoint , pub_key , auth_key FROM tbl_subscribers');
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }
}
