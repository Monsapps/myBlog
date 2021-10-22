<?php
/**
 * This is curriculum vitae model
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class CurriculumVitae {

    private $db;

    function __construct() {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function setCv(int $userId, string $fileName) {
        $sql = "INSERT INTO `curriculum_vitae` (`user_id`, `file_name`)
        VALUES(:user_id, :file_name);";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":user_id" => $userId,
            ":file_name" => $fileName
            ));
        $query = null;
    }

}