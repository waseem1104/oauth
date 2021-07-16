<?php
namespace App\Provider;

use App\Core\MyFunction;

class GithubProvider extends Provider
{

    protected $clientId;
    protected $clientSecret;
    protected $state;

    protected $url = "https://github.com/login/oauth/access_token";
    protected $redirectUri = "https://localhost/github-success";

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

        echo "<a href='https://github.com/login/oauth/authorize?response_type=code"
            . "&client_id=" . $this->clientId
            . "&scope=user"
            . "&state=" . $this->state
            . "&redirect_uri=https://localhost/github-success"
            . "&prompt=consent'>Se connecter avec Github</a>";

    }


    public function handleGithubSuccess(){

        $getToken = $this->getToken();
        $urlUser = "https://api.github.com/user";

        $array = [
            'Authorization: Bearer ' .  $getToken->access_token,
            'User-Agent: PHP'
        ];

        var_dump($this->getInfos($urlUser,$array));

    }


}