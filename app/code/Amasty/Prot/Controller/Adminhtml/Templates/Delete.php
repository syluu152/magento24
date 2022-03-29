<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Templates;

use Amasty\Prot\Controller\Adminhtml\Templates;
use Amasty\Prot\Model\Repository\Template as Repository;
use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface as OptionRepository;
use Psr\Log\LoggerInterface;
use Amasty\Prot\Api\Data\TemplateInterface;

class Delete extends Templates
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\Prot\Model\Repository\TemplateOption
     */
    private $templateOptionRepository;

    /**
     * @var OptionRepository
     */
    private $optionRepository;

    public function __construct(
        Repository $repository,
        \Amasty\Prot\Model\Repository\TemplateOption $templateOptionRepository,
        OptionRepository $optionRepository,
        LoggerInterface $logger,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->logger = $logger;
        $this->templateOptionRepository = $templateOptionRepository;
        $this->optionRepository = $optionRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($templateId = $this->getRequest()->getParam('id')) {
            try {
                $template = $this->repository->get($templateId);
                $options = $this->templateOptionRepository->getTemplateProductsOptions($templateId);
                $this->repository->delete($template);
                foreach ($options as $option) {
                    $this->optionRepository->delete($option);
                }
                $this->messageManager->addSuccessMessage(__('You have deleted the template.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }
        $this->_redirect('*/*/');
    }
}
