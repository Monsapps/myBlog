<?php
// CSRF fix
session_start();

// Load helpers
require_once "vendor/autoload.php";

include "./src/routes/routes.php";
