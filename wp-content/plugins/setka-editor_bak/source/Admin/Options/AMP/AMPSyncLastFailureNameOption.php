<?php
namespace Setka\Editor\Admin\Options\AMP;

use Korobochkin\WPKit\Options\AbstractOption;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

class AMPSyncLastFailureNameOption extends AbstractOption
{
    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_amp_sync_last_failure_name')
            ->setDefaultValue('');
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type(array(
                'type' => 'string',
            )),
        );
    }
}
