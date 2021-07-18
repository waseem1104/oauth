<?php
namespace App\Provider;

use App\Core\MyFunction;

class FacebookProvider extends Provider
{

    protected $clientId;
    protected $clientSecret;
    protected $state;
    protected $urlUser = "https://graph.facebook.com/me?fields=id,name,email";
    protected $url = "https://graph.facebook.com/oauth/access_token";
    protected $redirectUri = "https://localhost/fbauth-success";

    private $scope = ["email"];

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

        echo "<a href='https://www.facebook.com/v2.10/dialog/oauth?response_type=code"
            . "&client_id=" . $this->clientId
            . "&scope=" . implode(",",$this->scope)
            . "&state=" . $this->state
            . "&redirect_uri=https://localhost/fbauth-success'>Se connecter avec Facebook</a>";

    }


    public function handleFacebookSuccess(){

        $getToken = $this->getToken();
        $array = [
            'Authorization: Bearer ' .  $getToken->access_token
        ];

        var_dump($this->getInfos($this->urlUser,$array));

    }


}