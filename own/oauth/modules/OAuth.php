<?php


require_once __DIR__ . '/../buisness_rules/iOAuth.php';
require_once __DIR__ . '/../modules/token/Token.php';
require_once __DIR__ . '/../modules/token/TokenSchema.php';


class OAuth implements iOAuth
{
    private TokenSchema $OAuthS;

    public function __construct(public int $account_id)
    {
        $this->OAuthS = new TokenSchema($this->account_id);
    }

    function isExistsToken(): bool
    {
        return !$this->OAuthS->isExist();
    }


    function Token(): iToken
    {
        $AccessToken = $this->OAuthS->getAccessToken();
        $RefreshToken = $this->OAuthS->getRefreshToken();
        $LiveToken = $this->OAuthS->getLiveToken();
        return new Token($AccessToken, $RefreshToken, $LiveToken);
    }


    function createToken(): void
    {


//         \oAuth\Factory::getNode()->createInactiveApiKey($user_id);

        $accessToken = $this->gen_token() . "A";
        $workTime = time() + 604800 . 'R';
        $refreshToken = $this->gen_token();
        $this->OAuthS->createToken($accessToken . $workTime . $refreshToken);
    }

    function gen_token(): string
    {
        $bytes = openssl_random_pseudo_bytes(20, $cstrong);
        return bin2hex($bytes);
    }


    function validate(string $token): bool
    {
        $answer1 =  $token == $this->Token()->getAccess();
        $answer2 = $this->Token()->getLive() > 100;
        return$answer1 && $answer2;
    }
}