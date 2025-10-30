<?php

namespace App\Models\User;

use PDO;
use PDOException;

class UserModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getProfile($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT username FROM tbl_users WHERE username =:username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getRole($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT role FROM tbl_users WHERE username =:username");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            return false;
        }
    }

    // public function getusername($username)
    // {
    //     try {
    //         $stmt = $this->conn->prepare("SELECT username FROM tbl_users WHERE username =:username");
    //         $stmt->BindParam(':username', $username, PDO::PARAM_STR);
    //         $stmt->execute();
    //         return $stmt;
    //     } catch (PDOException $e) {
    //         return false;
    //     }
    // }

    // public function getEmpImgLoc($Ip, $username)
    // {
    //     try {
    //         $stmt = $this->conn->prepare("SELECT link_pig_emp FROM tbl_emp WHERE username =:username");
    //         $stmt->BindParam(':username', $username, PDO::PARAM_STR);
    //         $stmt->execute();
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $path = $result['link_pig_emp'];
    //         // ตัด prefix IP ออก
    //         $path = str_replace("\\$Ip", "", $path);
    //         // แปลง backslash เป็น slash
    //         $imgPath = str_replace("\\", "/", $path);
    //         // เอา / นำหน้าทิ้ง
    //         return ltrim($imgPath, "/");
    //     } catch (PDOException $e) {
    //         error_log($e->getMessage());
    //         return false;
    //     }
    // }

    // public function getEmpMedInfo($username)
    // {
    //     try {
    //         $stmt = $this->conn->prepare("SELECT 
    //                     emp.username,
    //                     bt.name_blood_type,
    //                     al.food_allergy,
    //                     al.food_detail_allergy,
    //                     al.drug_allergy,
    //                     al.drug_detail_allergy,
    //                     al.animals_allergy,
    //                     al.animals_detail_allergy
    //                 FROM
    //                     tbl_emp AS emp,
    //                     tbl_allergy AS al,
    //                     tbl_blood_type AS bt
    //                 WHERE 
    //                     emp.username = al.username_allergy
    //                 AND
    //                     emp.blood_type_emp = bt.code_tbl_blood_type
    //                 AND
    //                     emp.username =:username");
    //         $stmt->BindParam(':username', $username, PDO::PARAM_STR);
    //         $stmt->execute();
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //         return $result;
    //     } catch (PDOException $e) {
    //         return false;
    //     }
    // }

    // public function getEmpWarnInfo($username)
    // {
    //     try {
    //         $stmt = $this->conn->prepare("
    //                             SELECT 
    //                                 no_waring_emp ,
    //                                 effective_date_waring_emp ,
    //                                 month_waring_emp ,
    //                                 year_waring_emp ,
    //                                 reason_waring_emp,  
    //                                 level_1_waring_emp,
    //                                 level_2_waring_emp,
    //                                 level_3_waring_emp,
    //                                 level_4_waring_emp,
    //                                 comment_waring_emp
    //                             FROM tbl_waring_emp 
    //                             WHERE username_waring_emp =:username");
    //         $stmt->BindParam(':username', $username, PDO::PARAM_STR);
    //         $stmt->execute();
    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         return $result;
    //     } catch (PDOException $e) {
    //         return false;
    //     }
    // }

    public function getRegisEmp($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT username,iden_code,email,role FROM tbl_users WHERE username =:username");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getExistusername($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT username FROM tbl_users WHERE username =:username");
            $stmt->BindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getExistIdenCode($IdenCode)
    {
        try {
            $stmt = $this->conn->prepare("SELECT username AS iden FROM tbl_users WHERE iden_code =:IdCode");
            $stmt->bindParam(':IdCode', $IdenCode, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getExistEmail($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT email FROM tbl_users WHERE email =:email");
            $stmt->BindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
}
