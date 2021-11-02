<?php
/**
 * This is class to read&|write $_POST, $_GET, $_COOKIE, $_SESSION, $_FILE content
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Utils;

class Dollar {

    function inputPost(string $varName = null) {
        if(isset($varName)) {
            return filter_input(INPUT_POST, $varName);
        } else {
            return filter_input_array(INPUT_POST);
        }
    }

    function inputGet(string $varName = null) {
        if(isset($varName)) {
            return filter_input(INPUT_GET, $varName);
        } else {
            return filter_input_array(INPUT_GET);
        }
    }

    function getSession(string $varName) {
        return (isset($_SESSION[ $varName ]) ? $_SESSION[ $varName ] : null);
    }
    
}
