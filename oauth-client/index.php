<?php
namespace App;
ini_set("display_errors",true);

use App\Provider\GithubProvider;
require 'Autoload.php';

Autoload::register();



/**
 * AUTH CODE WORKFLOW
 * => Generate link (/login)
 * => Get Code (/auth-success)
 * => Exchange Code <> Token (/auth-success)
 * => Exchange Token <> User info (/auth-success)
 */


$github = new GithubProvider("6c216a58eb5bc5a3a1e5","142227221bf6a0dac5a7f03dd3e08ac897dcdb13","fdzefzefze");
$route = strtok($_SERVER["REQUEST_URI"], "?");



switch ($route) {
    case '/login':
        $github->handleLogin();
        break;
    case '/auth-success':
        handleSuccess();
        break;
    case '/fbauth-success':
        handleFbSuccess();
        break;
    case '/discord-success':
        handleDiscordSuccess();
        break;

    case '/github-success':
        $github->handleGithubSuccess();
        break;
    case '/auth-cancel':
        handleError();
        break;
    case '/password':
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            echo '<form method="POST">';
            echo '<input name="username">';
            echo '<input name="password">';
            echo '<input type="submit" value="Submit">';
            echo '</form>';
        } else {
            ["username" => $username, "password" => $password] = $_POST;
            getUser([
                'grant_type' => "password",
                "username" => $username,
                "password" => $password
            ]);
        }
        break;
    default:
        http_response_code(404);
        break;
}