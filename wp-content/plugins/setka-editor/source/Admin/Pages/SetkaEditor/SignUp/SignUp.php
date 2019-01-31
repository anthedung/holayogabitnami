<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\EmployeesRangeInterface;
use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\Positions\PositionInterface;

class SignUp
{

    /**
     * @var string
     */
    protected $accountType;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $companyDomain;

    /**
     * @var string
     */
    protected $companyName;

    /**
     * @var EmployeesRangeInterface
     */
    protected $companySize;

    /**
     * @var PositionInterface
     */
    protected $companyDepartment;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var boolean
     */
    protected $termsAndConditions = false;

    /**
     * @var boolean
     */
    protected $whiteLabel = false;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * @var string
     */
    protected $config;

    /**
     * @var string Slug of current active theme.
     */
    protected $currentTheme;

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     * @return $this
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyDomain()
    {
        return $this->companyDomain;
    }

    /**
     * @param string $companyDomain
     * @return $this
     */
    public function setCompanyDomain($companyDomain)
    {
        $this->companyDomain = $companyDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     * @return $this
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * @return EmployeesRangeInterface
     */
    public function getCompanySize()
    {
        return $this->companySize;
    }

    /**
     * @param EmployeesRangeInterface $companySize
     * @return $this
     */
    public function setCompanySize($companySize)
    {
        $this->companySize = $companySize;
        return $this;
    }

    /**
     * @return PositionInterface
     */
    public function getCompanyDepartment()
    {
        return $this->companyDepartment;
    }

    /**
     * @param PositionInterface $companyDepartment
     * @return $this
     */
    public function setCompanyDepartment($companyDepartment)
    {
        $this->companyDepartment = $companyDepartment;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTermsAndConditions()
    {
        return $this->termsAndConditions;
    }

    /**
     * @param bool $termsAndConditions
     * @return $this
     */
    public function setTermsAndConditions($termsAndConditions)
    {
        $this->termsAndConditions = $termsAndConditions;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWhiteLabel()
    {
        return $this->whiteLabel;
    }

    /**
     * @param bool $whiteLabel
     * @return $this
     */
    public function setWhiteLabel($whiteLabel)
    {
        $this->whiteLabel = $whiteLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param string $nonce
     * @return $this
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentTheme()
    {
        return $this->currentTheme;
    }

    /**
     * @param string $currentTheme
     * @return $this
     */
    public function setCurrentTheme($currentTheme)
    {
        $this->currentTheme = $currentTheme;
        return $this;
    }
}
