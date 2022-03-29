<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Controller\Adminhtml\Product\Action\Attribute;

use \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save;
use Amasty\Prot\Model\Repository\Template as Repository;

class SavePlugin
{
    const ADD_PARAM_NAME = 'amprot_add_amtemplate';

    const REMOVE_PARAM_NAME = 'amprot_remove_amtemplate';
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    private $attributeHelper;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper,
        Repository $repository,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->attributeHelper = $attributeHelper;
        $this->repository = $repository;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Save $subject
     * @return array
     */
    public function beforeExecute(Save $subject)
    {
        $productIds = $this->attributeHelper->getProductIds();
        if ($productIds && is_array($productIds)) {
            $removeTemplates = $this->request->getParam(self::REMOVE_PARAM_NAME, []);
            $addTemplates = $this->request->getParam(self::ADD_PARAM_NAME, []);
            $both = array_intersect($addTemplates, $removeTemplates);

            //remove duplicated checkboxes
            $removeTemplates = array_diff($removeTemplates, $both);
            $addTemplates = array_diff($addTemplates, $both);

            if ($removeTemplates) {
                $idsToRemove = $productIds;
                foreach ($removeTemplates as $templateId) {
                    $this->repository->removeFromIds((int)$templateId, $idsToRemove);
                }

                $this->messageManager->addSuccessMessage(
                    __(
                        'A total of %1 template(s) were removed for %2 record(s).',
                        count($removeTemplates),
                        count($productIds)
                    )
                );
            }

            if ($addTemplates) {
                foreach ($addTemplates as $templateId) {
                    $this->repository->assignTemplateToIds((int)$templateId, $productIds);
                }

                $this->messageManager->addSuccessMessage(
                    __(
                        'A total of %1 template(s) were applied for %2 record(s).',
                        count($addTemplates),
                        count($productIds)
                    )
                );
            }
        }

        return [];
    }
}
