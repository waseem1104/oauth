<?php
namespace App\Provider;

use App\Core\MyFunction;

class OauthProvider extends Provider
{

    protected $clientId;
    protected $clientSecret;
    protected $state;

    protected $url = "http://oauth-server:8081/token";
    protected $redirectUri = "https://localhost/auth-success";

    /**
     * GithubProvider constructor.
     * @param $clientId
     * @param $clientSecret
     * @param $state
     */
    public function __construct($clientId, $clientSecret, $state)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->state = $state;
    }


    public function handleLogin(){

        echo "<a href='http://localhost:8081/auth?response_type=code"
            . "&client_id=" . $this->clientId
            . "&scope=basic"
            . "&state=" . $this->state . "'>Se connecter avec Oauth Server</a>";

    }


    public function handleOauthSuccess(){

        $getToken = $this->getToken();
        $urlUser = "http://oauth-server:8081/me";

        $array = [
            'Authorization: Bearer ' .  $getToken->access_token
        ];

        var_dump($this->getInfos($urlUser,$array));

    }


}