<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Import\Behavior;

interface BehaviorProviderInterface
{
    /**
     * @param string $behaviorCode
     *
     * @throws \Amasty\Base\Exceptions\NonExistentImportBehavior
     * @return \Amasty\Base\Model\Import\Behavior\BehaviorInterface
     */
    public function getBehavior($behaviorCode);
}
