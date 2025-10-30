<?php
namespace App\Models;

use PDO;
use PDOException;

class SectionsModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function validate($username)
    {
        try {
            $stmt = $this->conn->prepare('SELECT password FROM tbl_users WHERE username = :username');
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user['password'];

        } catch (PDOException $e) {
            return false;
        }
    }
}
