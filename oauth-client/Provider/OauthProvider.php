<?php
namespace App\Provider;

use App\Core\MyFunction;

class OauthProvider extends Provider
{

    protected $clientId;
    protected $clientSecret;
    protected $state;
    protected $apiUrl = "http://oauth-server:8081/me";
    protected $url = "http://oauth-server:8081/token";
    protected $redirectUri = "https://localhost/auth-success";

    private $scope = ["basic"];

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
            . "&scope=" . implode(",",$this->scope)
            . "&state=" . $this->state . "'>Se connecter avec Oauth Server</a>";

    }


    public function handleOauthSuccess(){

        ["state" => $state, "code" => $code] = $_GET;
        if ($state !== $this->state) {
            throw new RuntimeException("{$state} : invalid state");
        }
        // https://auth-server/token?grant_type=authorization_code&code=...&client_id=..&client_secret=...
        $this->getUser([
            'grant_type' => "authorization_code",
            "code" => $code,
        ]);
    }

    function getUser($params)
    {
        $url = "http://oauth-server:8081/token?client_id=" . $this->clientId . "&client_secret=" . $this->clientSecret . "&" . http_build_query($params);
        $result = file_get_contents($url);
        $result = json_decode($result, true);
        $token = $result['access_token'];

        $context = stream_context_create([
            'http' => [
                'header' => 'Authorization: Bearer ' . $token
            ]
        ]);
        echo file_get_contents($this->apiUrl, false, $context);
    }

    function handleError()
    {
        ["state" => $state] = $_GET;
        echo "{$state} : Request cancelled";
    }


}