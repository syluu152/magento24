<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Import\Validation;

interface ValidatorPoolInterface
{
    /**
     * @return \Amasty\Base\Model\Import\Validation\ValidatorInterface[]
     */
    public function getValidators();

    /**
     * @param \Amasty\Base\Model\Import\Validation\ValidatorInterface
     *
     * @return void
     */
    public function addValidator($validator);
}
