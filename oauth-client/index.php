<?php
namespace App;
//ini_set("display_errors",true);

use App\Provider\GithubProvider;
use App\Provider\DiscordProvider;
use App\Provider\FacebookProvider;
use App\Provider\OauthProvider;
require 'Autoload.php';

Autoload::register();


$github = new GithubProvider("6c216a58eb5bc5a3a1e5","142227221bf6a0dac5a7f03dd3e08ac897dcdb13","fdzefzefze");
$discord = new DiscordProvider("861264462563639306","_fxDVpnJXJKJzFh00FH8E5iODDnkOhKq","fdzefzefze");
$facebook = new FacebookProvider("2900632103586316","a29d3b2cf8df42354e1d29e9538a62d0","fdzefzefze");
$oauth = new OauthProvider("client_60a3778e70ef02.05413444","cd989e9a4b572963e23fe39dc14c22bbceda0e60","fdzefzefze");
$route = strtok($_SERVER["REQUEST_URI"], "?");



switch ($route) {
    case '/login':
        $github->handleLogin();
        $discord->handleLogin();
        $facebook->handleLogin();
        $oauth->handleLogin();
        break;
    case '/auth-success':
        $oauth->handleOauthSuccess();
        break;
    case '/fbauth-success':
        $facebook->handleFacebookSuccess();
        break;
    case '/discord-success':
        $discord->handleDiscordSuccess();
        break;
    case '/github-success':
        $github->handleGithubSuccess();
        break;
    case '/auth-cancel':
        $oauth->handleError();
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
            $oauth->getUser([
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