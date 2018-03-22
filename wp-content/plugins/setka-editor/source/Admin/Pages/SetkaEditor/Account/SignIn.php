<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\Account;

class SignIn
{

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param mixed $nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }
}
