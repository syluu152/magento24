<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Product;

use Amasty\Prot\Api\Data\TemplateInterface;

class MassAssign extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Amasty_Prot::mass_assign';

    /**
     * @var bool
     */
    protected $isApplied = false;

    /**
     * @var int
     */
    protected $templateId = 0;

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
        $this->templateId = $templateId;
        if ($productIds) {
            $this->isApplied = $this->repository->assignTemplateToIds($templateId, $productIds);
            $this->productIds = $productIds;
        }
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t assign template right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        $collectionSize = count($this->productIds);
        return $this->repository->getSuccessMessage($collectionSize, $this->isApplied);
    }
}
