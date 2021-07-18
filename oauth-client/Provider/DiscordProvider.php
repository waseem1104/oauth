<?php
namespace App\Provider;

use App\Core\MyFunction;

class DiscordProvider extends Provider
{

    protected $clientId;
    protected $clientSecret;
    protected $state;
    protected $urlUser = "https://discord.com/api/users/@me";
    protected $url = "https://discord.com/api/oauth2/token";
    protected $redirectUri = "https://localhost/discord-success";

    private $scope = ["identify"];

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

        echo "<a href='https://discord.com/api/oauth2/authorize?response_type=code"
            . "&client_id=" . $this->clientId
            . "&scope=" . implode(",",$this->scope)
            . "&state=" . $this->state
            . "&redirect_uri=https://localhost/discord-success"
            . "&prompt=consent'>Se connecter avec Discord</a>";

    }


    public function handleDiscordSuccess(){

        $getToken = $this->getToken();

        $array = [
            'Authorization: Bearer ' .  $getToken->access_token
        ];

        var_dump($this->getInfos($this->urlUser,$array));

    }


}