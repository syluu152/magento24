<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Amasty\Prot\Model\Repository\Template as Repository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Amasty\Prot\Controller\Adminhtml\Templates;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

abstract class AbstractMassAction extends Templates
{
    const PARAM_NAME = 'amasty_paction_field';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        Repository $repository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action for group
     *
     * @param int $templateId
     * @param array $productIds
     */
    abstract protected function itemAction($templateId, $productIds);

    /**
     * Mass action execution
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $templateId = (int)$this->getRequest()->getParam(self::PARAM_NAME);
        $productIds = $collection->getAllIds();

        if ($productIds && $templateId) {
            try {
                $this->itemAction($templateId, $productIds);

                $this->messageManager->addSuccessMessage($this->getSuccessMessage(count($productIds)));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('catalog/*/');

        return $resultRedirect;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
