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

    /**
     * Return value of $_POST
     * @param string|null $varName
     *  Variable name
     * @return string
     */
    
    function getPostValue(string $varName = null) {
        $this->getPost();
        if($varName != null) {
            return (isset($this->post[$varName])) ? $this->post[$varName] : null;
        }
        return $this->post;
    }

    /**
     * Return value of $_FILES
     * @param string|null $varName
     *  Variable name
     * @return array
     */

    function getFilesValue(string $varName = null) {
        $this->getFiles();
        if($varName != null) {
            return (isset($this->files[$varName])) ? $this->files[$varName] : null;
        }
        return $this->files;
    }

    /**
     * Return value of $_GET
     * @param string|null $varName
     *  Variable name
     * @return string
     */

    function getGetValue(string $varName = null) {
        $this->getGet();
        if($varName != null) {
            return (isset($this->get[$varName])) ? $this->get[$varName] : null;
        }
        return $this->get;
        
    }

    /**
     * Return value of $_SESSION
     * @param string|null $varName
     *  Variable name
     * @return string
     */

    function getSessionValue(string $varName = null) {
        $this->getSession();
        if($varName != null) {
            return (isset($this->session[$varName])) ? $this->session[$varName] : null;
        }
        return $this->session;
    }

    /**
     * Set value of $_SESSION
     * @param string $varName
     *  Variable name
     * @param string $value
     *  Value
     * @return array
     */

    function setSessionValue(string $varName, string $value) {
        $_SESSION[$varName] = $value;
    }

    /**
     * Delete session value
     * @param string $varName
     *  Variable name
     * @return void
     */

    function unsetSession(string $varName) {
        unset($_SESSION[$varName]);
    }

    /**
     * Return value of $_COOKIE
     * @param string|null $varName
     *  Variable name
     * @return string
     */

    function getCookieValue(string $varName = null) {
        $this->getCookie();
        if($varName != null) {
            return (isset($this->cookie[$varName])) ? $this->cookie[$varName] : null;
        }
        return $this->cookie;
    }

    /**
     * Set cookie value
     * @param string $varName
     *  Variable name
     * @param string $value
     *  Value of cookie
     * @param int $duration
     *  Duration of cookie
     * @return void
     */

    function setCookieValue(string $varName, string $value, int $duration) {
        setcookie($varName, $value, $duration);
    }

    private function getPost() {
        $this->post = (filter_input_array(INPUT_POST) !== null) ? filter_input_array(INPUT_POST) : null;
    }

    private function getFiles() {
        $this->files = (isset($_FILES)) ? $_FILES : null;
    }

    private function getGet() {
        $this->get = (filter_input_array(INPUT_GET) !== null) ? filter_input_array(INPUT_GET) : null;
    }

    private function getSession() {
        $this->session = (isset($_SESSION)) ? $_SESSION : null;
    }

    private function getCookie() {
        $this->cookie = (filter_input_array(INPUT_COOKIE) !== null) ? filter_input_array(INPUT_COOKIE) : null;
    }
}
