<?php
/**
 * This is the installer, it:
 * - Check config.ini
 * - Check uploads and socials folders permissions
 * - Check composer is installed
 * - Check Twig is installed
 * - Create SQL tables
 * - Create the main user
 */

include "./src/utils/DatabaseManager.php";

// define true if you want to force minimum requirements
$debug = false;

// Information's images
$warningMarkImage = '<img src="data:image/png;base64,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"/>';
        
$page = new Page();

$getArray = filter_input_array(INPUT_GET);

if(isset($getArray["step"])) {
    switch($getArray["step"]) {
        case 1:
            $page->firstStep();
        break;
        case 2:
            $page->secondStep();
        break;
        case 3: 
            $page->thirdStep();
        break;
        case 4:
            $postArray = filter_input_array(INPUT_POST);
            $page->fourthStep($postArray);
        break;
        default:
            $page->firstStep();
    }
} else {
    $page->firstStep();
}

/**
* Class that contain four pages
*/

class Page {

    function firstStep() {
        global $debug;

        $minimumRequirements = new MinimumRequirements();
        $html = new Html();

        $html->head("Installation de myBlog");

        $html->header("&Eacute;tape 1: v&eacute;rification des minimums requis");

        if($minimumRequirements->configIniStatus) {
            $html->approvedRequirement("Fichier config.ini");
        } else {
            $html->disapprovedRequirement("Fichier config.ini: vous devez ajouter le fichier config.ini avec les identifiants de votre base de donn&eacute;es dans le dossier &laquo; config &raquo; (prenez l\'exemple de config.ini.example)");
        }

        if($minimumRequirements->databaseStatus) {
            $html->approvedRequirement("Connexion &agrave; la base de donn&eacute;es");
        } else {
            $html->disapprovedRequirement("Connexion &agrave; la base de donn&eacute;es: les informations dans le fichier config.ini ne sont pas bonnes");
        }

        if($minimumRequirements->socialsFolderStatus) {
            $html->approvedRequirement("Permission du dossier /public/images/socials/");
        } else {
            $html->disapprovedRequirement("Permission du dossier /public/images/socials/: veuillez appliquer la permission Chmod 755 au dossier");
        }

        if($minimumRequirements->uploadsFolderStatus) {
            $html->approvedRequirement("Permission du dossier /public/uploads/");
        } else {
            $html->disapprovedRequirement("Permission du dossier /public/uploads/: veuillez appliquer la permission Chmod 755 au dossier ");
        }

        if($minimumRequirements->composerStatus) {
            $html->approvedRequirement("Composer Autoload");
        } else {
            $html->disapprovedRequirement("Composer Autoload: vous devez installer la biblioth&egrave;que autoload de composer &agrave; la racine de votre site");
        }

        if($minimumRequirements->twigStatus) {
            $html->approvedRequirement("Composer Twig");
        } else {
            $html->disapprovedRequirement("Composer Twig: vous devez ajouter la biblioth&egrave;que twig avec composer &agrave; la racine de votre site");
        }

        if(($minimumRequirements->configIniStatus
            &&$minimumRequirements->databaseStatus
            &&$minimumRequirements->socialsFolderStatus
            &&$minimumRequirements->uploadsFolderStatus 
            && $minimumRequirements->composerStatus 
            && $minimumRequirements->twigStatus) || $debug) {

                $html->nextStepButton("2", "Passer &agrave; la seconde &eacute;tape");

        } else {
            $html->reloadStepButton("1");
        }

        $html->foot();
    }

    function secondStep() {
        global $checkMarkImage, $crossMarkImage;

        $dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
        $html = new Html();

        $userTableSql = "
            CREATE TABLE IF NOT EXISTS `user` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL,
                `surname` VARCHAR(255) NULL,
                `email` VARCHAR(255) NULL,
                `password` VARCHAR(255) NULL,
                `registration_date` DATETIME NULL,
                `user_hat` VARCHAR(140) NULL,
                `role_id` INT(11) NULL,
                PRIMARY KEY (`id`))
            ENGINE = InnoDB;";

        $roleTableSql = "
            CREATE TABLE IF NOT EXISTS `role` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `role_name` VARCHAR(45) NULL,
                PRIMARY KEY (`id`))
            ENGINE = InnoDB;";

        $roleEntriesSql = "INSERT INTO `role` (`id`, `role_name`) VALUES (1, 'Administrateur'), (2, 'Editeur');";

        $socialTableSql = "
            CREATE TABLE IF NOT EXISTS `social` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL,
                `social_image` VARCHAR(255) NULL,
                PRIMARY KEY (`id`))
            ENGINE = InnoDB;";

        $cvTableSql = "
            CREATE TABLE IF NOT EXISTS `curriculum_vitae` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NULL,
                `file_name` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT FK_UserId
                FOREIGN KEY (`user_id`)
                REFERENCES `user` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;"; 
        
        $userSocialTableSql = "
            CREATE TABLE IF NOT EXISTS `user_social` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NULL,
                `social_id` INT(11) NULL,
                `meta` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `social_user_id`
                FOREIGN KEY (`user_id`)
                REFERENCES `user` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
                CONSTRAINT `social_id`
                FOREIGN KEY (`social_id`)
                REFERENCES `social` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        $imageTableSql = "
            CREATE TABLE IF NOT EXISTS `image` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NULL,
                `path_name` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `image_user_id`
                FOREIGN KEY (`user_id`)
                REFERENCES `user` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        $postTableSql = "
            CREATE TABLE IF NOT EXISTS `post` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NULL,
                `title` VARCHAR(255) NULL,
                `hat` VARCHAR(255) NULL,
                `content` LONGTEXT NULL,
                `date` DATETIME NULL,
                `last_edited` DATETIME NULL,
                `keywords` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `post_user_id`
                FOREIGN KEY (`user_id`)
                REFERENCES `user` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        $commentTableSql = "
            CREATE TABLE IF NOT EXISTS `comment` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NULL,
                `post_id` INT(11) NULL,
                `content` VARCHAR(255) NULL,
                `date` DATETIME NULL,
                `status` VARCHAR(45) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `comment_post_id`
                FOREIGN KEY (`post_id`)
                REFERENCES `post` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
                CONSTRAINT `comment_user_id`
                FOREIGN KEY (`user_id`)
                REFERENCES `user` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        $configTableSql = "
            CREATE TABLE IF NOT EXISTS `config` (
                `id` INT NOT NULL,
                `name` VARCHAR(45) NULL,
                `value` VARCHAR(255) NULL,
                PRIMARY KEY (`id`))
            ENGINE = InnoDB;";

        $configEntriesSql = "
            INSERT INTO `config` (`id`, `name`, `value`) 
                VALUES (1, 'site_title', 'MyBlog v1.0'), 
                    (2, 'site_keywords', 'blog, system, myblog'), 
                    (3, 'site_descriptions', 'Powered by myBlog'), 
                    ('4', 'site_main_user_id', '1')";

        $contactTableSql = "
            CREATE TABLE IF NOT EXISTS `contact` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL,
                `surname` VARCHAR(255) NULL,
                `email` VARCHAR(255) NULL,
                `message` LONGTEXT NULL,
                `status` INT NULL,
                PRIMARY KEY (`id`))
            ENGINE = InnoDB;";
        

        $title = "Installation des tables dans la base de donn&eacute;es";

        $content = '
        <section>
            <div class="row">
                <div class="col">
                <h1>&Eacute;tape 2: Installation des tables dans la base de donn&eacute;es</h1>
                </div>
            </div>
        </section>';

        if($dbManager->query($userTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table user r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table user &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        if($dbManager->query($roleTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table role r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table role &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        if($dbManager->query($roleEntriesSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Insertion des r&ocirc;les r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Insertion des r&ocirc;les &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        if($dbManager->query($socialTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table social r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table social &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        if($dbManager->query($cvTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table curriculum_vitae r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table curriculum_vitae &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        
        if($dbManager->query($userSocialTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table user_social r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table user_social &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        
        if($dbManager->query($imageTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table image r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table image &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        
        if($dbManager->query($postTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table post r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table post &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        
        if($dbManager->query($commentTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table comment r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table comment &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        
        if($dbManager->query($configTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table config r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table config &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        if($dbManager->query($configEntriesSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Ins&eacute;rtion des entr&eacute;es de la table config r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Ins&eacute;rtion des entr&eacute;es de la table config &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        
        if($dbManager->query($contactTableSql)) {
            $content .= '
                <div class="row">
                    <div class="col lead">
                    '. $checkMarkImage .' Installation de la table contact r&eacute;ussite
                    </div>
                </div>
            ';
        } else {
            $error = print_r($dbManager->errorInfo(), true);
            $content .= '
            <div class="row">
                <div class="col lead">
                '. $crossMarkImage .' Installation de la table contact &eacute;chou&eacute;e: '. $error .'
                </div>
            </div>
        ';
        }

        $content .= '
                <br />
                <div class="row">
                    <div class="col lead text-center">
                        <a class="btn btn-success" href="./install.php?step=3" role="button">Passer &agrave; la troisi&egrave;me &eacute;tape</a>
                    </div>
                </div>';

        $html->buildPage($content, $title);
    }

    function thirdStep() {

        $html = new Html();

        $title = "Cr&eacute;ation du compte administrateur";

        $content = '
        <section>
            <div class="row">
                <div class="col">
                <h1>&Eacute;tape 3: cr&eacute;ation du compte administrateur</h1>
                </div>
            </div>
        </section>';
        $error = empty($getArray["error"]) ? 0: $getArray["error"];
        if($error == 1) {
            $content .= '
            <section class="container">
                <div class="box-error" id="box-error">
                    Tous les champs de sont pas renseigné
                </div>
            </section>';
        } elseif($error == 2) {
            $content .= '
            <section class="container">
                <div class="box-error" id="box-error">
                    Impossible d\'ajouter le compte administrateur.
                </div>
            </section>';
        }

        $content .= '
        <form action="./install.php?step=4" method="post">
            <div class="form-container">
                    <div>
                        <input type="text" name="surname" required>
                        <label for="surname">Nom</label>
                    </div>
                    <div>
                        <input type="text" name="name" required>
                        <label for="name">Pr&eacute;nom</label>
                    </div>
                    <div>
                        <input type="email" name="email" required>
                        <label for="email">Email</label>
                    </div>
                    <div>
                        <input type="text" name="password" required>
                        <label for="password">Mot de passe</label>
                    </div>
                    <div>
                    <input type="text" name="hat" required>
                    <label for="hat">Phrase qui vous correspond</label>
                </div>
                    <input type="submit" value="Cr&eacute;er">
            </div>
        </form> ';

        $html->buildPage($content, $title);
    }

    function fourthStep(array $postArray) {
        global $checkMarkImage, $warningMarkImage;

        $dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
        $html = new Html();

        $title = "Installation termin&eacute;e";

        $content = '
        <section>
            <div class="row">
                <div class="col">
                <h1>'. $checkMarkImage .' Installation termin&eacute;e</h1>
                </div>
            </div>
        </section>';

        $content .= '
                <br />
                <div class="row">
                    <div class="col">
                        '. $warningMarkImage .' Veuillez supprimer le fichier install.php à la racine de votre site '. $warningMarkImage .'<br />
                        '. $warningMarkImage .' Veuillez prot&eacute;t&eacute;ger les dossiers config, src et vendor de l\'accès au public (.htaccess pour Apache) '. $warningMarkImage .'
                    </div>
                </div>';

        $content .= '
                <br />
                <div class="row">
                    <div class="col lead text-center">
                        <a class="btn btn-success" href="./index.php" role="button">Aller vers mon blog</a>
                    </div>
                </div>';

        if(!empty($postArray["surname"]) && !empty($postArray["name"]) && !empty($postArray["email"]) && !empty($postArray["password"]) && !empty($postArray["hat"])) {
            $sql = "INSERT INTO `user` (`id`, `name`, `surname`, `email`, `password`, `registration_date`, `user_hat`, `role_id`)
                VALUES(1, :name, :surname, :email, :password, :date, :user_hat, 1);";

            $query = $dbManager->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            $date = date("Y-m-d H:i:s");
            $encryptedPassword = password_hash($postArray["password"], PASSWORD_DEFAULT);

            $query->execute(array(
                ":name" => $postArray["name"],
                ":surname" => $postArray["surname"],
                ":email" => $postArray["email"],
                ":password" => $encryptedPassword,
                ":date" => $date,
                ":user_hat" => $postArray["hat"]));
            $html->buildPage($content, $title);

        } else {
            Header("Location: ./install.php?step=3&error=1");
        }
    }
}

/**
* Generate html page 
*/

class Html {

    function buildPage(string $content, string $title) {

        $this->head($title);
        ?>
        <?= $content ?>
        <?php
        $this->foot();
    }

    function head(string $title) {
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
                <link rel="stylesheet" type="text/css" href="./public/css/style.css">
                <title><?= filter_var($title, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></title>
            </head>
            <body>
            <header class="container">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand nav-bar-margin-start" href="./index.php">myBlog</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav mr-auto">
                            <li class="active">
                                <a class="nav-link" href="#">Installeur</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
        <div class="container">
            <?php
    }

    function header(string $header) {
        ?>
            <section>
                <div class="row">
                    <div class="col">
                    <h1><?= filter_var($header, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></h1>
                    </div>
                </div>
            </section>
        <?php
    }

    function approvedRequirement(string $message) {
        ?>
            <div class="row">
                    <div class="col lead">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAABmJLR0QA/wD/AP+gvaeTAAAEWElEQVRoge2Yf0yVVRjHv8/9QYAotcwsnQkFyxzMROcQkx/TQOBK5i7NyRqmYNactVpbm63+0fVjaytXWw7aQiaGo5lACFg4gasVbEUxEWWXoOWo5SQ06N57nqc/soaX+3LP5VKw9n6288853/M8z3fvec97zguYmJiYmJiY/H+gmS5gMkr7H7+HyZMW5VFNhxIafptMO2uN7B7MT2BFrQDuBtBHFmv24SUn+oz0lv+uNH2eGth8LytqxF8mAOB+ZvXl7v78VKM5s87I3kub5lkZ9QDixvcTcCeDmkv6HZsCzZtVRp4fdEaNRthqAVlhIJkD4NOS/vwt/gOzxohTnNbrPFZFgvVBpHYCveLfafuX6goNAd3+w9gHAAr05NLu3zcrnsiuAcdBADs15S2RXvWif+eMb78l/Y5nALynoxWgg8co68MHT474j82okV1uxzZAKqG1MqgPQmll8SeHAo4Gm1560THfZ+WkJYM3Wl/LPOMLuVoDdrhzsompFoBdQ37FYlVpZUub3EaCSY0Uu3OWkpJzABYCct4HFBx5oOnnEGuewI7eR9fASp9DMEdDfk1ZKL0i/lTXZCJDI87ujJhou60dQPK4bjcDeUcST1/QrHkCxX0bE0RJG4AFGvIxwJL9UWLT2WDCwGtTQFFWSzkzJzMzxrU4MLcXXdiQEWL9AIAnL2YtUl7VzMwL/OIGakopVaRjwtBIUU/GqyxcKMII0O4AvI3bezKKQjHh7NsQy8z1InyfQdzxTYT56cplLTW68ScsrW1dj6TDIi2BxvwQEtp/NKn1YLAkjo6U6JjIqGYAa7WqEuyvSmo7oKW9yYQvO4vvYbDWtkwADjzxbWp8rNez5/CqTm8gUWlHiv1aREQ1s9IzATr0cbIrJBNAgKXFNlQo5e1S7INWE9/Oqzaq235+zbwJ0QV01UplzL48zXjHliW5ngvVBGCwfJzdy2PYY68SoXztSELfW4Xyjq/qHPi7a2vnyreEZMJxwoAvRueO5DYkXP5DO+c4jLffaqfVE9f7DpE8qx9OroDgOJHyXedjXye9BKI39Kahwx6tMo8v776un+tWgr4Lm7966AURvAn9A+YIiMohsk8nPgG9XrtnXcPKy79oxjeKE5x8V+IWIaoEEB1OsgD8pEBpp1J7+sMNpH1ozG1PWMHgWgCLw016k2ELLBmfpV36ZjqChXT6zXItXmRTtjoARldRXUaJKLtxnbs1zDj/EPIxPq1t/twoFXkMQO4UcyoAhafTf/xkivMDMqX7SEYLbCQL3wWwJ8SpIiQlZzKHyqeSdzLCulitb7prH0jehvaOJi+f3fjr6+HkNCLsG+LaU7FbCVSBIDuakLzvyh4O4ZsUGmH/fHDlDNcoxVnMviFmHwI25atynRveOx0FGzFtd/bVdZHxJFID0K07GqHqxu9jxd2F8ExXrkBM688HZzWs7tusTmFaDQsEQH1nga9lOnOYmJiYmJiYTMKfj0cWCRG0i64AAAAASUVORK5CYII="/>
                    <?= filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>
                    </div>
            </div>
        <?php
    }

    function disapprovedRequirement(string $message) {
        ?>
            <div class="row">
                    <div class="col lead">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABmJLR0QA/wD/AP+gvaeTAAAEi0lEQVRoge2ZTU8bVxSG33NpU5WCKCUTY4TUWBDCh+hiZmE2LMKCOwKFIqxIbQqb/ICA2k35B+2qVf9D1aZVUkWVgqgrpQsWYVEWVIaqRsIIRIlg4YliyxH4nC74ECB7fMdjFpX8Sl5Yc+fc9xnfc+fcY6Cuuuqq63+j3PRUVB5MNF9VfHkw0ZybnooGuUeZDizMJB4qUtuFw2ub+ZlEPLg9f+VnEvHC4bVNRWq7MH1v1vQ+Mgr+2dQcBN+cfhfIKyLSjd8/eVGN2cvK3Z90CA1JQFrPjAnNv/vD468q3VsRIP/p5JycM39Or0iUbnwUDiJ3f9IBIwmg9fI1EZpvevSLL4QvwOt7E7MgfOszxFOKdOOPT5fN7F5U/pOP48yyCKCl3BgBzTb/9PS7ctfLAuSmdJTV29sAGvxtkMdS1C2PnwWC8BJjcUUNi4CUNX+iIhF3Nv38bK/UxbJJnD9657VwMSvM8P8UW0iw6CXGjBPbS4zFSbAoXGwxiJ/NI/+6XCzfJZSd0jYxJQF8YOILityWJwu+OZGdHHUIquSaryZmxSTO3tU2IMYQBHZbfv295ITZ8VEHqnTCBo11KqNtNHtX28JsDMEsbtvCxYmz46OOHD8II/OlYpSSEQAAZPWIzcp8OQnozEB2/I7DfHGfN723kowBAGBfj9gqwHKCEpeLdKjIfNlAidu28IfxuyUQAAAcjN6JQ9h3775g6FhmY0np6789D7QdBwYAgP2RYRvHT9XklzCRRyz6+vOlwC/EqgCAYwgWTlJoCPIY0O1VmAdCAADA/vCQzWSc2KXkCYtuX3pRlXkgJAAA7A4P2UqME/u8PIBCmQdqAAAAu0OOTUoFgfBIsW5f+jOUeQB4K2wAAIAIMYMAMRpOAIpFqcnDC7+E4rbDYrzPn5cHgtu5vBLqPBEKYMu2HdXASUhg86fyiJXbuVI9RNUAW/agQ0LVPPnL8hTB7VxZrQqiKoCtwUEHyrgwM5HHotzYanCIwACbH/XHScjkJAWATkoJs7FComOra1dXSmwN9jpc5gBeQh5EaRI5FBWgAGRyY+vrtS/mNnp7HUVsfhgh0bG19DIAZPq6bYbxe8IjsBtb36hdOb3R2xUkYT3FpGPp9IWlkO7rthUbF4CeUjCCqAiw0dXloAGmW6XHULrnkvlTpbu7bRCSBDE7FAm5tzb8IXwBNrq6HBHzZSMsuieT8U3CdPeHNth8OYHFvZXJBD/U/337drN6U9gE0GY0Eaii+VP9c/NmHPBvaJ1JcHDYlI8NpPZLtlbK9oW4UGgS5vcr923Yw1HR2DwA9GQyyzgqamH2KsYXbkXuvaZyscoC9G9t/Sssn1cyTyy6Z2cncFXZs7OzTCwGEPLFQCZTsisHGCTxWjQ6R1SquUseq6Ie2HkZqiRe6+iwqWyjgOb7dnerb+6eTRKNXGivA/BEoAdehjN/Fr+jwwYXL0HQl/17e1/XIv7xJJY1m7Kso5Rl7acikZr/wZGKROIpyzo4meNhreMDAP66cSOSsqyyCRVWKctqSllW+1XFr6uuuuqqvf4DtHiJ42XrW+MAAAAASUVORK5CYII="/>
                    <?= filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>
                    </div>
            </div>
        <?php
    }

    function nextStepButton(string $number, string $message) {
        ?>
            <div class="row">
                    <div class="col lead text-center">
                        <a class="btn btn-success" href="./install.php?step=<?= filter_var($number, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>" role="button"><?= filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?></a>
                    </div>
            </div>
        <?php
    }

    function reloadStepButton(string $number) {
        ?>
            <div class="row">
                    <div class="col lead text-center">
                        <a class="btn btn-warning" href="./install.php?step=1" role="button">Rafra&icirc;chir la page</a>
                    </div>
            </div>
        <?php
    }

    function foot() {
        ?>
        </div>
            </body>
        </html>
        <?php
    }
}

/**
* Class to check minimum requirements
*/

class MinimumRequirements {

    public $configIniStatus = false;
    public $socialsFolderStatus  = false;
    public $uploadsFolderStatus = false;
    public $composerStatus = false;
    public $twigStatus = false;
    public $databaseStatus = false;

    function __construct() {
        try {
            // try to call DatabaseManager() if not exception status is true
            $dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
            $this->databaseStatus = true;
        } catch(PDOException $e) {
            $this->databaseStatus = false;
        }

        $socialsFolderPerm = substr(sprintf('%o', $this->filePerms("./public/images/socials")), -4);
        $uploadsFolderPerm = substr(sprintf('%o', $this->filePerms("./public/uploads")), -4);

        if($socialsFolderPerm == 755) {
            $this->socialsFolderStatus  = true;
        }

        if($uploadsFolderPerm == 755) {
            $this->uploadsFolderStatus = true;
        }

        if($this->fileExists("./vendor/autoload.php")) {
            $this->composerStatus = true;
        }

        if($this->fileExists("./vendor/twig")) {
            $this->twigStatus = true;
        }
        if($this->fileExists("./config/config.ini")) {
            $this->configIniStatus = true;
        }
    }

    private function fileExists(string $path) {
        return file_exists($path);
    }

    private function filePerms(string $path) {
        return fileperms($path);
    }
}
