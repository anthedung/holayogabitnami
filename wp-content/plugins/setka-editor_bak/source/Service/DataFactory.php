<?php
namespace Setka\Editor\Service;

use Korobochkin\WPKit\DataComponents\NodeInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class DataFactory
 */
class DataFactory
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * DataFactory constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $className
     * @return NodeInterface
     */
    public function create($className)
    {
        /**
         * @var $dataComponent NodeInterface
         */
        $dataComponent = new $className();

        $dataComponent
            ->setValidator($this->validator)
            ->setConstraint($dataComponent->buildConstraint());

        return $dataComponent;
    }
}
