<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Options\PlanFeatures\PlanFeaturesOption;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\Request;

class GetCompanyStatusAction extends SetkaAPI\Prototypes\ActionAbstract
{

    /**
     * GetCompanyStatusAction constructor.
     */
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_GET)
            ->setEndpoint('/api/v1/wordpress/company_status.json');
    }

    public function getConstraint()
    {
    }

    public function handleResponse()
    {
        $response = $this->getResponse();
        $errors   = $this->getErrors();

        switch($response->getStatusCode()) {
            // 401 // Token not found
            case $response::HTTP_UNAUTHORIZED:
                $errors->add(new Errors\ServerUnauthorizedError());
                return;

            // 200 // 403 // Active or canceled subscription
            case $response::HTTP_OK:
            case $response::HTTP_FORBIDDEN:
                break;

            // XXX // Unknown
            default:
                $errors->add(new Errors\UnknownError());
                return;
        }

        $content   = $response->content;
        $validator = $this->getApi()->getValidator();

        try {
            $statusOption = new Options\SubscriptionStatus\Option();
            $results      = $validator->validate(
                $content->get('status'),
                $statusOption->getConstraint()
            );
            $this->violationsToException($results);
        } catch (\Exception $exception) {
            $errors->add(new Errors\ResponseBodyInvalidError());
            return;
        }

        try {
            $paymentStatusOption = new Options\SubscriptionPaymentStatus\Option();
            $results             = $validator->validate(
                $content->get('payment_status'),
                $paymentStatusOption->getConstraint()
            );
            $this->violationsToException($results);
        } catch (\Exception $exception) {
            $errors->add(new Errors\ResponseBodyInvalidError());
            return;
        }

        if($response->isOk()) {
            try {
                $activeUntil = new Options\SubscriptionActiveUntil\Option();
                $results     = $validator->validate(
                    $content->get('active_until'),
                    $activeUntil->getConstraint()
                );
                $this->violationsToException($results);
            } catch (\Exception $exception) {
                $errors->add(new Errors\ResponseBodyInvalidError());
                return;
            }
        }

        try {
            $planFeatures = new PlanFeaturesOption();
            $results      = $validator->validate(
                $content->get('features'),
                $planFeatures->getConstraint()
            );
            $this->violationsToException($results);
        } catch (\Exception $exception) {
            $errors->add(new Errors\ResponseBodyInvalidError());
            return;
        }
    }
}
