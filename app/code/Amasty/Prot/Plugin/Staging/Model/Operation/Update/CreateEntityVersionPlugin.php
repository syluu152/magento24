<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Staging\Model\Operation\Update;

class CreateEntityVersionPlugin
{
    /**
     * @var \Amasty\Prot\Model\Repository\Template
     */
    private $templateRepository;

    public function __construct(\Amasty\Prot\Model\Repository\Template $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param \Magento\Staging\Model\Operation\Update\CreateEntityVersion $subject
     * @param mixed $result
     * @param object $entity
     * @param array $arguments
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function afterExecute($subject, $result, $entity, $arguments)
    {
        if ($entity->getEntityId() && $entity instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            $origRowId = $entity->getOrigData('row_id');
            $templateIds = $this->templateRepository->getTemplateIdsByProduct($origRowId);

            $products = [$entity->getRowId()];
            foreach ($templateIds as $templateId) {
                $this->templateRepository->assignTemplateToIds($templateId, $products, $origRowId, true);
            }
        }
    }
}
