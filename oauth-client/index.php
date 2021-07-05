<?php
const CLIENT_ID = "client_60a3778e70ef02.05413444";
const CLIENT_FBID = "3648086378647793";
const CLIENT_SECRET = "cd989e9a4b572963e23fe39dc14c22bbceda0e60";
const CLIENT_FBSECRET = "1b5d764e7a527c2b816259f575a59942";
const CLIENT_DISCORD = "861264462563639306";
const CLIENT_DISCORD_SECRET = "_fxDVpnJXJKJzFh00FH8E5iODDnkOhKq";
const CLIENT_GITHUB = "6c216a58eb5bc5a3a1e5";
const CLIENT_GITHUB_SECRET = "142227221bf6a0dac5a7f03dd3e08ac897dcdb13";
const STATE = "fdzefzefze";
function handleLogin()
{
    // http://.../auth?response_type=code&client_id=...&scope=...&state=...
    echo "<h1>Login with OAUTH</h1>";
    echo "<a href='http://localhost:8081/auth?response_type=code"
        . "&client_id=" . CLIENT_ID
        . "&scope=basic"
        . "&state=" . STATE . "'>Se connecter avec Oauth Server</a>";
    echo "<a href='https://www.facebook.com/v2.10/dialog/oauth?response_type=code"
        . "&client_id=" . CLIENT_FBID
        . "&scope=email"
        . "&state=" . STATE
        . "&redirect_uri=https://localhost/fbauth-success'>Se connecter avec Facebook</a>";

    echo "<a href='https://discord.com/api/oauth2/authorize?response_type=code"
        . "&client_id=" . CLIENT_DISCORD
        . "&scope=identify"
        . "&state=" . STATE
        . "&redirect_uri=https://localhost/discord-success"
        . "&prompt=consent'>Se connecter avec Discord</a>";

    echo "<a href='https://github.com/login/oauth/authorize?response_type=code"
        . "&client_id=" . CLIENT_GITHUB
        . "&scope=user"
        . "&state=" . STATE
        . "&redirect_uri=https://localhost/github-success"
        . "&prompt=consent'>Se connecter avec Github</a>";
}

function handleError()
{
    ["state" => $state] = $_GET;
    echo "{$state} : Request cancelled";
}

function handleSuccess()
{
    ["state" => $state, "code" => $code] = $_GET;
    if ($state !== STATE) {
        throw new RuntimeException("{$state} : invalid state");
    }
    // https://auth-server/token?grant_type=authorization_code&code=...&client_id=..&client_secret=...
    getUser([
        'grant_type' => "authorization_code",
        "code" => $code,
    ]);
}

function handleFbSuccess()
{
    ["state" => $state, "code" => $code] = $_GET;
    if ($state !== STATE) {
        throw new RuntimeException("{$state} : invalid state");
    }
    // https://auth-server/token?grant_type=authorization_code&code=...&client_id=..&client_secret=...
    $url = "https://graph.facebook.com/oauth/access_token?grant_type=authorization_code&code={$code}&client_id=" . CLIENT_FBID . "&client_secret=" . CLIENT_FBSECRET."&redirect_uri=https://localhost/fbauth-success";
    $result = file_get_contents($url);
    $resultDecoded = json_decode($result, true);
    ["access_token"=> $token] = $resultDecoded;
    $userUrl = "https://graph.facebook.com/me?fields=id,name,email";
    $context = stream_context_create([
        'http' => [
            'header' => 'Authorization: Bearer ' . $token
        ]
    ]);
    echo file_get_contents($userUrl, false, $context);
}

function handleGithubSuccess(){

    ["state" => $state, "code" => $code] = $_GET;
    if ($state !== STATE) {
        throw new RuntimeException("{$state} : invalid state");
    }

    $url = "https://github.com/login/oauth/access_token";


    $token = apiRequest($url, array(
        "grant_type" => "authorization_code",
        'client_id' => CLIENT_GITHUB,
        'client_secret' => CLIENT_GITHUB_SECRET,
        'redirect_uri' => 'https://localhost/github-success',
        'code' => $code
    ));

    echo $token->access_token;


}


function handleDiscordSuccess()
{
    ["state" => $state, "code" => $code] = $_GET;
    if ($state !== STATE) {
        throw new RuntimeException("{$state} : invalid state");
    }
    $url = "https://discord.com/api/oauth2/token";

    $token = apiRequest($url, array(
        "grant_type" => "authorization_code",
        'client_id' => CLIENT_DISCORD,
        'client_secret' => CLIENT_DISCORD_SECRET,
        'redirect_uri' => 'https://localhost/discord-success',
        'code' => $code
    ));
    $context = stream_context_create([
        'http' => [
            'header' => 'Authorization: Bearer ' . $token->access_token
        ]
    ]);
    echo file_get_contents('https://discord.com/api/users/@me', false, $context);
}

function apiRequest($url, $post=FALSE, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    if($post)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

    $headers[] = 'Accept: application/json';

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    return json_decode($response);
}

function getUser($params)
{
    $url = "http://oauth-server:8081/token?client_id=" . CLIENT_ID . "&client_secret=" . CLIENT_SECRET . "&" . http_build_query($params);
    $result = file_get_contents($url);
    $result = json_decode($result, true);
    $token = $result['access_token'];

    $apiUrl = "http://oauth-server:8081/me";
    $context = stream_context_create([
        'http' => [
            'header' => 'Authorization: Bearer ' . $token
        ]
    ]);
    echo file_get_contents($apiUrl, false, $context);
}

/**
 * AUTH CODE WORKFLOW
 * => Generate link (/login)
 * => Get Code (/auth-success)
 * => Exchange Code <> Token (/auth-success)
 * => Exchange Token <> User info (/auth-success)
 */
$route = strtok($_SERVER["REQUEST_URI"], "?");
switch ($route) {
    case '/login':
        handleLogin();
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
        handleGithubSuccess();
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
