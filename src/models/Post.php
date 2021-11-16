<?php
/**
 * This is post model
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Post {

  private $dbManager;

  function __construct() {
    $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
  }

  /**
   * Return all posts from database
   * @return array
   */

  function getAllPosts() {
    $query = $this->dbManager->query("SELECT p.id, p.user_id, p.title, p.hat, p.date, p.last_edited, u.name, u.surname
                      FROM `post` AS p
                      LEFT JOIN `user` AS u ON u.`id` = p.`user_id`
                      ORDER by `last_edited` DESC;");
    $results = $query->fetchAll();
    return $results;
  }

  /**
   * Return unique post info from database
   * @param int $idPost
   *  Post id
   * @return array
   */

  function getPostInfos(int $idPost) {
    $sql = "SELECT p.id, p.user_id, p.title, p.hat, p.content, p.date, p.last_edited, p.keywords, u.name, u.surname
          FROM `post` AS p
          LEFT JOIN `user` AS u ON u.`id` = p.`user_id`
          WHERE p.`id` = :id
          LIMIT 1;";

    $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
    $query->execute(array(":id" => $idPost));

    $results = $query->fetch();
    $query->closeCursor();
    return $results;
  }

  /**
   * Add post to the database
   * @param int $useriId
   *  User id
   * @param string $title
   *  Title of the post
   * @param string $hat
   *  Hat of the post
   * @param string $content
   *  Content of the post
   * @param string $keywords
   *  Keywords of the post for SEO
   * @return void
   */

  function addPost(int $userId, string $title, string $hat, string $content, string $keywords) {
    $date = date("Y-m-d H:i:s");

    $sql = "INSERT INTO `post` (`user_id`, `title`, `hat`, `content`, `date`, `last_edited`, `keywords`)
        VALUES (:user_id, :title, :hat, :content, :date, :date, :keywords);";

    $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

    $query->execute(array(
      ":user_id" => $userId,
      ":title" => $title,
      ":hat" => $hat,
      ":content" => $content,
      ":date" => $date,
      ":keywords" => $keywords
  ));
    $query = null;

  }

  /**
   * Update post info into the database
   * @param int $useriId
   *  User id
   * @param string $title
   *  Title of the post
   * @param string $hat
   *  Hat of the post
   * @param string $content
   *  Content of the post
   * @param string $keywords
   *  Keywords of the post for SEO
   * @return void
   */

  function updatePost(int $idPost ,int $userId, string $title, string $hat, string $content, string $keywords) {
    $date = date("Y-m-d H:i:s");

    $sql = "UPDATE `post` 
        SET `user_id` = :user_id, `title` = :title, `hat` = :hat, `content` = :content, `last_edited` = :date, `keywords` = :keywords
        WHERE `id` = :id;";

    $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

    $query->execute(array(
      ":id" => $idPost,
      ":user_id" => $userId,
      ":title" => $title,
      ":hat" => $hat,
      ":content" => $content,
      ":date" => $date,
      ":keywords" => $keywords
    ));
    $query = null;
  }

  /**
   * Delete post of the database
   * @param int $idPost
   *  Post id
   * @return void
   */

  function deletePost(int $idPost) {
    $sql = "DELETE FROM `post` WHERE `id` = :id;";

    $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

    $query->execute(array(":id" => $idPost));
    $query = null;
  }
}
