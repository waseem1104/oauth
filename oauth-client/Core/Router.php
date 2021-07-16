<?php


class Router
{

    private $route;

    /**
     * Router constructor.
     * @param $route
     */
    public function __construct($route)
    {
        $this->route = $route;
    }


    public function run(){

        switch ($this->route) {
            case '/':
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
    }

}