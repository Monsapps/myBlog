<?php
/**
 * This is class to read&|write $_POST, $_GET, $_COOKIE, $_SESSION, $_FILE content
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Utils;

class SuperGlobal {
    private $post;
    private $files;
    private $get;
    private $session;
    private $cookie;
    
    function getPostValue(string $varName = null) {
        $this->getPost();
        if($varName != null) {
            return (isset($this->post["$varName"])) ? $this->post["$varName"] : null;
        } else {
            return $this->post;
        }
    }

    function getFilesValue(string $varName = null) {
        $this->getFiles();
        if($varName != null) {
            return (isset($this->files["$varName"])) ? $this->files["$varName"] : null;
        } else {
            return $this->files;
        }
    }

    function getGetValue(string $varName = null) {
        $this->getGet();
        if($varName != null) {
            return (isset($this->get["$varName"])) ? $this->get["$varName"] : null;
        } else {
            return $this->get;
        }
    }

    function getSessionValue(string $varName = null) {
        $this->getSession();
        if($varName != null) {
            return (isset($this->session["$varName"])) ? $this->session["$varName"] : null;
        } else {
            return $this->session;
        }
    }

    function getCookieValue(string $varName = null) {
        $this->getCookie();
        if($varName != null) {
            return (isset($this->cookie["$varName"])) ? $this->cookie["$varName"] : null;
        } else {
            return $this->cookie;
        }
    }

    private function getPost() {
        $this->post = (isset($_POST)) ? $_POST : null;
    }

    private function getFiles() {
        $this->files = (isset($_FILES)) ? $_FILES : null;
    }

    private function getGet() {
        $this->get = (isset($_GET)) ? $_GET : null;
    }

    private function getSession() {
        $this->session = (isset($_SESSION)) ? $_SESSION : null;
    }

    private function getCookie() {
        $this->cookie = (isset($_COOKIE)) ? $_COOKIE : null;
    }
}
