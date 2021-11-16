<?php
/**
 * This is curriculum vitae model
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class CurriculumVitae {

    private $dbManager;

    function __construct() {
        $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    /**
     * Add curriculum vitae info to database
     * @param int $userId
     *  User id
     * @param string $filename
     *  Filename
     * @return void
     */

    function setCv(int $userId, string $filename) {
        $sql = "INSERT INTO `curriculum_vitae` (`user_id`, `file_name`)
        VALUES(:user_id, :filename);";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":user_id" => $userId,
            ":filename" => $filename
            ));
        $query = null;
    }

}