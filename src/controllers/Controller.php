<?php
/**
 * This is the main controller
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class Controller {

    private $title;
    private $keywords;
    private $descriptions;
    private $twig;
    private $role;

    function __construct() {

        $config = new \Monsapp\Myblog\Utils\ConfigManager();

        $this->title = $config->getConfig("site_title");
        $this->keywords = $config->getConfig("site_keywords");
        $this->descriptions = $config->getConfig("site_descriptions");

        $loader = new \Twig\Loader\FilesystemLoader("src/views/");
        $this->twig = new \Twig\Environment($loader);

    }

    function getHomepage() {

        echo $this->twig->render("index.html", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "desciption" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role
            ));
    }

}