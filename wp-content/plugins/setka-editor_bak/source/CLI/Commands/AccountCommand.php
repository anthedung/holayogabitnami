<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Options\TokenOption;
use Setka\Editor\Service\DataFactory;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use WP_CLI as Console;

/**
 * Manage Setka Editor plugin and content with helpful commands.
 */
class AccountCommand extends \WP_CLI_Command
{
    /**
     * @var DataFactory
     */
    protected $dataFactory;

    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * AccountCommand constructor.
     * @param DataFactory $dataFactory
     * @param SetkaEditorAccount $setkaEditorAccount
     */
    public function __construct(DataFactory $dataFactory, SetkaEditorAccount $setkaEditorAccount)
    {
        $this->dataFactory        = $dataFactory;
        $this->setkaEditorAccount = $setkaEditorAccount;
    }

    /**
     * Setup Setka account by license key.
     *
     * ## OPTIONS
     *
     * <license-key>
     * : The Setka license key (32 symbols).
     *
     * ## EXAMPLES
     *
     *      # Login with license key (default usage). All previously account info will be replaced by new account (if auth will successful).
     *      $ wp setka-editor login xrHCQzRWSsIqXVhUMpULNgHC4aSB0yf1
     *      Success: Authentication completed.
     *
     *      # Login with wrong license key.
     *      $ wp setka-editor login 123
     *      Error: License key should have exactly 32 characters.
     *      Error: Can't log in. See errors above.
     *
     * @alias sign-in
     *
     * @when after_wp_load
     */
    public function login($args)
    {
        $tokenOption = $this->dataFactory->create(TokenOption::class);
        $result      = $tokenOption->set($args[0])->validate();

        if(count($result) !== 0) {
            foreach($result as $violation) {
                /**
                 * @var $violation \Symfony\Component\Validator\ConstraintViolationInterface
                 */
                Console::error($violation->getMessage(), false);
            }
            Console::error('Can\'t log in. See errors above.');
        }
        unset($tokenOption, $violation, $result);

        $result = $this->setkaEditorAccount->getSignIn()->signInByToken($args[0]);


        foreach($result as $action) {
            $errors = $action->getErrors();
            if(count($errors) !== 0) {
                foreach($errors as $error) {
                    /**
                     * @var $error \Symfony\Component\Validator\ConstraintViolationInterface
                     */
                    Console::error($error->getCode() . ': ' . $error->getMessage(), false);
                }
                Console::error('Authentication request was unsuccessful. See errors above.');
            }
        }
        unset($error, $errors, $action);

        Console::success('Authentication completed.');
    }

    /**
     * Logout from Setka account (remove all account specific settings).
     *
     * ## EXAMPLES
     *      # Sign out from account.
     *      $ wp setka-editor sign-out
     *      Success: You are logged out from Setka account.
     *
     * @subcommand sign-out
     *
     * @when after_wp_load
     */
    public function signOut()
    {
        $this->setkaEditorAccount->signOut();
        Console::success('You are logged out from Setka account.');
    }
}
