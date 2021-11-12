<?php
/**
 * Comment controller
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class CommentController extends Controller {

    function getAddCommentPage(array $postArray) {

        // check if input are filled
        if(empty($postArray["comment"]) || empty($postArray["user_id"])) {
            $this->redirectTo("./index.php?page=post&id=". $postArray["post_id"] ."&error=1&token=". $this->superGlobal->getSessionValue("token"));
            return;
        }

        if(($this->role != -1) && ($postArray["token"] == $this->superGlobal->getSessionValue("token"))) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->addComment((int)$postArray["post_id"], (int)$postArray["user_id"], $postArray["comment"]);
            $this->redirectTo("./index.php?page=post&id=". $postArray["post_id"] ."&status=1");
        }
        $this->redirectTo("./index.php?page=post&id=". $postArray["post_id"] ."&token=". $this->superGlobal->getSessionValue("token"));
    }

    /**
     * This is admin section
     */

    function getCommentManagerPage() {
        // only admin can crud comment
        if($this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comments = $comment->getPendingComments();
            $this->twig->display("panel/comment.html.twig", array(
                "title" => "Gestion des commentaires - " . $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
                "role" => $this->role,
                "user" => $this->userInfos,
                "comments" => $comments,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
            return;
        }
        $this->redirectTo("./index.php");
    }

    function getActivateCommentPage(int $commentId) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->activateComment($commentId);
            $this->redirectTo("./index.php?page=commentmanager");
        }
        $this->redirectTo("./index.php?page=panel");
    }

    function getRejectCommentPage(int $commentId) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->rejectComment($commentId);
            $this->redirectTo("./index.php?page=commentmanager");
        }
        $this->redirectTo("./index.php?page=panel");
    }
}
