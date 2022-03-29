<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Import\Validation;

class ValidatorPool implements ValidatorPoolInterface
{
    /**
     * @var \Amasty\Base\Model\Import\Validation\ValidatorInterface[]
     */
    private $validators;

    public function __construct(
        $validators
    ) {
        $this->validators = [];
        foreach ($validators as $validator) {
            if (!($validator instanceof ValidatorInterface)) {
                throw new \Amasty\Base\Exceptions\WrongValidatorInterface();
            }

            $this->validators[] = $validator;
        }
    }

    /**
     * @inheritdoc
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @inheritdoc
     */
    public function addValidator($validator)
    {
        if (!($validator instanceof ValidatorInterface)) {
            throw new \Amasty\Base\Exceptions\WrongValidatorInterface();
        }

        $this->validators[] = $validator;
    }
}
