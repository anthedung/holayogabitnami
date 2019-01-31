<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;

use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

/**
 * Class GetCompanyStatusAction
 */
class GetCompanyStatusAction extends SetkaEditorAPI\Prototypes\ActionAbstract
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

    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            case $response::HTTP_UNAUTHORIZED: // Token not found
                $this->addError(new Errors\ServerUnauthorizedError());
                break;

            case $response::HTTP_OK:
                $this->validateOk($response->content);
                break;

            case $response::HTTP_FORBIDDEN: // Canceled subscription
                $this->validateForbidden($response->content);
                break;

            default:
                $this->addError(new Errors\UnknownError());
                break;
        }

        return $this;
    }

    /**
     * Validates HTTP 200 data.
     *
     * @param ParameterBag $content Parameters to validate.
     * @return $this For chain calls.
     */
    public function validateOk(ParameterBag $content)
    {
        try {
            $results = $this->getApi()->getValidator()->validate(
                $content->all(),
                $this->buildConstraintsOk()
            );
            $this->getErrors()->addAll($results);
        } catch (\Exception $exception) {
            $this->addError(new Errors\ResponseBodyInvalidError());
        }
        return $this;
    }

    /**
     * Validates HTTP 403 data.
     *
     * @param ParameterBag $content Parameters to validate.
     * @return $this For chain calls.
     */
    public function validateForbidden(ParameterBag $content)
    {
        try {
            $results = $this->getApi()->getValidator()->validate(
                $content->all(),
                $this->buildConstraintsForbidden()
            );
            $this->getErrors()->addAll($results);
        } catch (\Exception $exception) {
            $this->addError(new Errors\ResponseBodyInvalidError());
        }
        return $this;
    }

    /**
     * @return Constraints\Collection
     */
    public function buildConstraintsOk()
    {
        $activeUntil = new Options\SubscriptionActiveUntilOption();

        $constraints                         = $this->buildConstraintsForbidden();
        $constraints->fields['active_until'] = new Constraints\Required($activeUntil->buildConstraint());

        return $constraints;
    }

    /**
     * @return Constraints\Collection
     */
    public function buildConstraintsForbidden()
    {
        $statusOption        = new Options\SubscriptionStatusOption();
        $paymentStatusOption = new Options\SubscriptionPaymentStatusOption();
        $planFeatures        = new Options\PlanFeatures\PlanFeaturesOption();

        $constraint = new Constraints\Collection(array(
            'fields' => array(
                'status'         => new Constraints\Required($statusOption->buildConstraint()),
                'payment_status' => new Constraints\Required($paymentStatusOption->buildConstraint()),
                'features'       => new Constraints\Required($planFeatures->buildConstraint()),
            ),
            'allowExtraFields' => true,
        ));

        return $constraint;
    }
}
