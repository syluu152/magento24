<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Templates;

use Amasty\Prot\Model\Catalog\Product\SaveHandler as SaveHandler;
use Amasty\Prot\Model\Repository\Template as TemplateRepository;
use Amasty\Prot\Model\Repository\TemplateOption as TemplateOptionRepository;
use Magento\Backend\App\Action;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action
{
    const ACTION = 'edit';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var SaveHandler
     */
    private $saveHandler;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    /**
     * @var Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Template
     */
    private $resourceTemplate;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var TemplateOptionRepository
     */
    private $templateOptionRepository;

    public function __construct(
        Action\Context $context,
        SaveHandler $saveHandler,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Psr\Log\LoggerInterface $logger,
        TemplateRepository $templateRepository,
        TemplateOptionRepository $templateOptionRepository,
        \Amasty\Prot\Model\ResourceModel\Template $resourceTemplate,
        MetadataPool $metadataPool
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->saveHandler = $saveHandler;
        $this->templateRepository = $templateRepository;
        $this->productRepository = $productRepository;
        $this->resourceTemplate = $resourceTemplate;
        $this->metadataPool = $metadataPool;
        $this->templateOptionRepository = $templateOptionRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $productIds = isset($data['products_list_container'])
            ? array_column($data['products_list_container'], 'entity_id')
            : [];
        $templates = $data['data']['product']['templates'] ?? [];
        $templateId = 0;

        if ($templates && !isset($templates[0]['options'])) {
            $this->messageManager->addErrorMessage(
                __('Please assign at least one customizable option to the template.')
            );
            $this->redirect(0);

            return;
        }

        if ($productIds && $templates) {
            try {
                $templateId = (int)reset($templates)['template_id'] ?? 0;
                $firstProduct = $this->assignFirstProduct(reset($productIds), $templates);
                if ($firstProduct->getData(SaveHandler::TEMPLATE_SAVED_FLAG)) {
                    $this->messageManager->addSuccessMessage(__('Product template was saved.'));
                }

                $productTemplates = $firstProduct->getTemplates();
                $this->massApply(reset($productTemplates), $productIds);
                $templateId = (int)reset($productTemplates)['template_id'];

                if (!$this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/index');
                    return;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Please assign at least one product to the template.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
                $this->messageManager->addErrorMessage(__('Something went wrong.'));
            }
        }

        $this->redirect($templateId);
    }

    /**
     * @param int $productId
     * @param array $templates
     * @return \Magento\Catalog\Api\Data\ProductInterface|object
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function assignFirstProduct(int $productId, $templates = [])
    {
        $firstProduct = $this->productRepository->getById($productId);
        $firstProduct->setData('templates', $templates);

        $entity = $this->saveHandler->execute($firstProduct, ['action' => self::ACTION]);
        $this->templateOptionRepository->updateEntityOptionsValues(0, [$entity->getId()]);

        return $entity;
    }

    /**
     * @param object $templates
     * @param array $productIds
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function massApply($template, $productIds = [])
    {
        $templateId = $template['template_id'];
        if ($templateId) {
            $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
            $assignedProducts = $this->resourceTemplate->getTemplateEntityIds(
                $templateId,
                $productMetadata->getLinkField()
            );

            $productsToRemove = array_diff($assignedProducts, $productIds);
            $diff = array_diff($productIds, $assignedProducts);

            if ($diff) {
                $isApplied = $this->templateRepository->assignTemplateToIds(
                    $templateId,
                    $diff
                );
                $this->messageManager->addSuccessMessage(
                    $this->templateRepository->getSuccessMessage(count($diff), $isApplied)
                );
            }

            if ($productsToRemove) {
                $this->templateRepository->removeFromIds($templateId, $productsToRemove);
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been removed from template.', count($productsToRemove))
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Something went wrong during saving the template.'));
        }
    }

    /**
     * @param int $templateId
     */
    private function redirect(int $templateId)
    {
        if ($templateId) {
            $this->_redirect('*/*/edit', ['id' => $templateId]);
        } else {
            $this->_redirect('*/*/newAction');
        }
    }
}
