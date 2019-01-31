<?php
namespace Setka\Editor\Service\SetkaAccount;

use Setka\Editor\Admin\Options;

class Account
{

    /**
     * Some checks for Setka Account. User logged in if token provided
     * (other account stuff must also available in DB). This is not bullet proof
     * checks so manually editing any of plugin settings not recommended :)
     *
     * @return bool True if user logged in, false otherwise.
     */
    public static function isLoggedIn()
    {
        $_option = new Options\Token\Option();
        if($_option->getValue()) {
            return true;
        }
        return false;
    }

    /**
     * Check if subscription running and not expired.
     *
     * @return bool true if subscription running, false otherwise.
     */
    public static function isSubscriptionRunning()
    {
        if(self::isSubscriptionStatusRunning()) {
            return self::isSubscriptionNotExpire();
        }
        return false;
    }

    /**
     * If account currently active (user can create new posts with Editor).
     *
     * @return bool True if account status is "running", false otherwise.
     */
    public static function isSubscriptionStatusRunning()
    {
        $_option = new Options\SubscriptionStatus\Option();
        if($_option->getValue() === 'running') {
            return true;
        }
        return false;
    }

    public static function isSubscriptionNotExpire()
    {
        $_option     = new Options\SubscriptionActiveUntil\Option();
        $activeUntil = \DateTime::createFromFormat(\DateTime::ISO8601, $_option->getValue());
        unset($_option);

        if(!$activeUntil) {
            return false;
        }

        $now = new \DateTime('now', $activeUntil->getTimezone());

        if(!$now) {
            return false;
        }

        if($activeUntil > $now) {
            return true;
        }

        return false;
    }

    /**
     * If account have trial period.
     *
     * @return bool True if account trialling, false otherwise.
     */
    public static function isSubscriptionStatusTrialing()
    {
        $_option = new Options\SubscriptionPaymentStatus\Option();
        if($_option->getValue() === 'trialing') {
            return true;
        }
        return false;
    }

    /**
     * Editor resources is the Editor JS-CSS + Theme resources JS-CSS
     *
     * @return bool
     */
    public static function isEditorResourcesAvailable()
    {
        if(!self::isSubscriptionStatusRunning()) {
            return false;
        }

        if(!self::isThemeResourcesAvailable()) {
            return false;
        }

        $_option = new Options\EditorJS\Option();
        if(!$_option->getValue()) {
            return false;
        }

        $_option = new Options\EditorCSS\Option();
        if(!$_option->getValue()) {
            return false;
        }

        return true;
    }

    /**
     * Resources is the Theme JS-CSS which always available if you're logged in (token provided).
     * Even if you canceled subscription your post shows perfectly.
     *
     * @return bool True if user logged in (resour
     */
    public static function isThemeResourcesAvailable()
    {
        $_option = new Options\ThemeResourceCSS\Option();
        if(!$_option->getValue()) {
            return false;
        }

        $_option = new Options\ThemeResourceJS\Option();
        if(!$_option->getValue()) {
            return false;
        }

        $_option = new Options\ThemePluginsJS\Option();
        if(!$_option->getValue()) {
            return false;
        }

        return true;
    }

    /**
     * Check if trial ends in one day.
     *
     * @return bool True if trial ends in one day. False if not trial, or if more than one day of trial,
     * or if trial already expired.
     */
    public static function isTrialEndsInOneDay()
    {
        if(self::isSubscriptionStatusTrialing()) {
            $_option     = new Options\SubscriptionActiveUntil\Option();
            $activeUntil = \DateTime::createFromFormat(\DateTime::ISO8601, $_option->getValue());
            unset($_option);

            if(!$activeUntil) {
                return false;
            }

            $now = new \DateTime('now', $activeUntil->getTimezone());

            if(!$now) {
                return false;
            }

            // Trial not over
            if($activeUntil > $now) {
                $difference = $activeUntil->getTimestamp() - $now->getTimestamp();
                if($difference < DAY_IN_SECONDS) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * If payment past due. User have 14 days to solve issue with payment (change card for example).
     * @return bool True if payment status is past due. False otherwise.
     */
    public static function isSubscriptionPaymentPastDue()
    {
        $_option = new Options\SubscriptionPaymentStatus\Option();
        if($_option->getValue() === 'past_due') {
            return true;
        }
        return false;
    }
}
