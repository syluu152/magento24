<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Templates;

use Amasty\Prot\Api\TemplateRepositoryInterface;
use Amasty\Prot\Controller\Adminhtml\Templates;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Templates
{

    /**
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * @var \Amasty\Prot\Model\Catalog\Locator\RegistryLocator
     */
    private $registryLocator;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterfaceFactory
     */
    private $productFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\Prot\Api\TemplateRepositoryInterface $templateRepository,
        \Amasty\Prot\Model\Catalog\Locator\RegistryLocator $registryLocator,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory
    ) {
        parent::__construct($context);
        $this->templateRepository = $templateRepository;
        $this->registryLocator = $registryLocator;
        $this->productFactory = $productFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $templateId = (int)$this->getRequest()->getParam('id');
        if ($templateId) {
            try {
                $this->templateRepository->get($templateId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));
                $this->_redirect('*/*/index');

                return;
            }
        }

        $product = $this->productFactory->create();
        $product->setTypeId('simple');
        $product->setStoreId(0);
        $this->registryLocator->setProduct($product);

        $this->initAction();

        // set title and breadcrumbs
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Template'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $templateId ?
                __('Edit Template # %1', $templateId)
                : __('New Template')
        );

        $breadcrumb = $templateId ? __('EditTemplate # %1', $templateId) : __('New Template');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);

        $this->_view->renderLayout();
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    private function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(self::ADMIN_RESOURCE)
            ->_addBreadcrumb(__('Templates'), __('Templates'));

        return $this;
    }
}
