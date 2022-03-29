<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Product;

class MassRemove extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Amasty_Prot::mass_remove';

    /**
     * @var array
     */
    protected $productIds = [];

    /**
     * @param int $templateId
     * @param array $productIds
     */
    protected function itemAction($templateId, $productIds)
    {
        $this->repository->removeFromIds($templateId, $productIds);
        $this->productIds = $productIds;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t remove template from products right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        $collectionSize = count($this->productIds);
        if ($collectionSize) {
            return __('A total of %1 record(s) have been updated.', $collectionSize);
        }

        return __('No records have been updated.');
    }
}
