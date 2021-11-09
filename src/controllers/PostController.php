<?php
/**
 * Post controller, CRUD Posts
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class PostController extends Controller {

    function getPostsPage() {
        $post = new \Monsapp\Myblog\Models\Post();
        $posts = $post->getAllPosts();

        $this->twig->display("posts.html.twig", array(
            "title" => "Articles - " . $this->siteInfo["site_title"], 
            "navtitle" => $this->siteInfo["site_title"], 
            "descriptions" => $this->siteInfo["site_descriptions"],
            "keywords" => $this->siteInfo["site_keywords"],
            "role" => $this->role,
            "user" => $this->userInfos,
            "posts" => $posts,
            "token" => $this->superGlobal->getSessionValue("token")
        ));
    }

    function getPostPage(int $idPost) {
        $post = new \Monsapp\Myblog\Models\Post();
        $postInfos = $post->getPostInfos($idPost);

        $comment = new \Monsapp\Myblog\Models\Comment();
        $comments = $comment->getComments($idPost);

        $this->twig->display("post.html.twig", array(
            "title" => $postInfos["title"] . " - " . $this->siteInfo["site_title"], 
            "navtitle" => $this->siteInfo["site_title"], 
            "descriptions" => $this->siteInfo["site_descriptions"],
            "keywords" => $this->siteInfo["site_keywords"],
            "role" => $this->role,
            "is_allowed_to_crud" => $this->isAllowedToCRUD, 
            "user" => $this->userInfos,
            "post" => $postInfos,
            "comments" => $comments,
            "token" => $this->superGlobal->getSessionValue("token")
        ));
    }

    function getAddPostPage() {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->isAllowedToCRUD) {
            $this->twig->display("addpost.html.twig", array(
                "title" => "Ajouter un article - " . $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
                "role" => $this->role,
                "user" => $this->userInfos,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getPublishPage(array $postArray) {

        if(empty($postArray["title"]) || empty($postArray["hat"]) || $postArray["content"]) {
            $this->redirectTo("./index.php?page=addpost&error=1&token=" . $this->superGlobal->getSessionValue("token"));
        }
        if($this->isAllowedToCRUD && ($postArray["token"] == $this->superGlobal->getSessionValue("token"))) {
            // We need to attach user id to a post
            $userInfos = $this->userInfos;
            // TODO control les valeurs
            $post = new \Monsapp\Myblog\Models\Post();
            $post->addPost((int)$userInfos["id"], $postArray["title"], $postArray["hat"], $postArray["content"], $postArray["keywords"]);

            $this->redirectTo("./index.php?page=post&token=" . $this->superGlobal->getSessionValue("token"));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getEditPostPage(int $idPost) {

        // We store all users to edit author's post
        $user = new \Monsapp\Myblog\Models\User();
        $allUsers = $user->getAllUsers();

        $post = new \Monsapp\Myblog\Models\Post();
        $postInfos = $post->getPostInfos($idPost);

        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && ($this->isAllowedToCRUD && (($this->userInfos["id"] == $postInfos["user_id"])) || ($this->role == 1))) {
            $this->twig->display("editpost.html.twig", array(
                "title" => "Modifier "  . $postInfos["title"] . " - " . $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
                "role" => $this->role,
                "user" => $this->userInfos,
                "post" => $postInfos,
                "authors" => $allUsers,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php?page=post&id". $idPost);
        }
    }

    function getEditPostPublishPage(array $postArray) {

        if(empty($postArray["id"]) 
            || empty($postArray["author"]) 
            || empty($postArray["title"]) 
            || empty($postArray["hat"]) 
            || empty($postArray["content"]) 
            || empty($postArray["keywords"])) {
                if(empty($postArray["title"]) || empty($postArray["hat"]) || $postArray["content"]) {
                    $this->redirectTo("./index.php?page=post&error=1&token=" . $this->superGlobal->getSessionValue("token"));
                    return;
                }
        }

        if($this->isAllowedToCRUD && ($postArray["token"] == $this->superGlobal->getSessionValue("token"))) {
            $post = new \Monsapp\Myblog\Models\Post();
            $post->updatePost((int)$postArray["id"], (int)$postArray["author"], $postArray["title"], $postArray["hat"], $postArray["content"], $postArray["keywords"]);
            $this->redirectTo("./index.php?page=post&id=". $postArray["id"]);
        }

        $this->redirectTo("./index.php?page=post&id=". $postArray["id"]);
    }

    /**
     * This is admin section
     */


    function getPostManagerPage() {
        $post = new \Monsapp\Myblog\Models\Post();
        $posts = $post->getAllPosts();
        if($this->role >= 1) {
            $this->twig->display("panel/posts.html.twig", array(
                "title" => "Gestion des articles - " . $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
                "role" => $this->role,
                "user" => $this->userInfos,
                "posts" => $posts,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getDeletePostPage(int $idPost) {
        $post = new \Monsapp\Myblog\Models\Post();
        $postInfo = $post->getPostInfos($idPost);
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && (($this->role == 1) || (($this->role == 2) && ($this->userInfos == $postInfo["used_id"])))) {
            $post->deletePost((int)$idPost);
            $this->redirectTo("./index.php?page=postmanager");
        /*} elseif($this->role == 2 && $this->userInfos == $postInfo["used_id"]) {
            $post->deletePost((int)$id);
            Header("Location: ./index.php?page=postmanager");
            exit;*/
         }else {
            $this->redirectTo("./index.php");
        }
    }

}
