<?php
namespace App\Controllers;

use App\Database\Database;
use Dotenv\Dotenv;

class DBController
{
    private $db;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        require_once $root . '/vendor/autoload.php';

        $dotenv = Dotenv::createImmutable($root);
        $dotenv->load();

        $database = new Database($_ENV['DB_DSN'], $_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE'], $_ENV['DB_PORT']);
        $this->db = $database->DBconnection();

    }

    public function connection()
    {
        return $this->db;
    }

}
