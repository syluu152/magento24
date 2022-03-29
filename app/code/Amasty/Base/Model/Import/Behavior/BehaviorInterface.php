<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Import\Behavior;

/**
 * @since 1.4.6
 */
interface BehaviorInterface
{
    /**
     * @param array $importData
     *
     * @return \Magento\Framework\DataObject|void
     */
    public function execute(array $importData);
}
