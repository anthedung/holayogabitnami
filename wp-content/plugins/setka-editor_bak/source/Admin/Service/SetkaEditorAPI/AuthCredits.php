<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI;

/**
 * Class AuthCredits
 */
class AuthCredits
{
    /**
     * @var string Token for Setka API server.
     */
    private $token;

    /**
     * AuthCredits constructor.
     * @param $token string
     */
    public function __construct($token)
    {
        $this->setToken($token);
    }

    /**
     * @return string Setka API token.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token string Setka API token.
     * @return $this For chain calls.
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return array Auth credits.
     */
    public function getCreditsAsArray()
    {
        return array(
            'token' => $this->getToken(),
        );
    }
}
