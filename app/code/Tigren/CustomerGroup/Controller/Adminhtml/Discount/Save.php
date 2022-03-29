<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Controller\Adminhtml\Discount;

use Tigren\CustomerGroup\Model\CustomerGroup\CustomerGroupFactory;
use Tigren\CustomerGroup\Model\CustomerGroup\ResourceModel\CustomerGroup\CollectionFactory;

/**
 *
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionCustomerGroup;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var \Tigren\CustomerGroup\Model\Discount\DiscountFactory
     */
    protected $_contentFactory;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    /**
     * @var CustomerGroupFactory
     */
    protected $_customerGroupFactory;

    /**
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Tigren\CustomerGroup\Model\Discount\DiscountFactory $contentFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param CustomerGroupFactory $customerGroup
     * @param CollectionFactory $collectionCustomerGroup
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
        \Tigren\CustomerGroup\Model\Discount\DiscountFactory $contentFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        CustomerGroupFactory $customerGroup,
        CollectionFactory $collectionCustomerGroup
    ) {
        $this->_collectionCustomerGroup = $collectionCustomerGroup;
        $this->_customerGroupFactory = $customerGroup;
        $this->_messageManager = $messageManager;
        $this->_contentFactory = $contentFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);

    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $model = $this->_contentFactory->create();
        $modelCustomerGroup = $this->_customerGroupFactory->create();
        $collectionCustomerGroup = $this->_collectionCustomerGroup->create();

        $valueModelDiscount = $this->getRequest()->getPostValue();
        $valueCustomerGroup = $valueModelDiscount['id_customer_group'];

        unset($valueModelDiscount['id_customer_group']);

        $today = date('Y/m/d h:i:s');
        $valueModelDiscount['create_at'] = $today;

        $dataDiscount = [
            'discount_amount' => $valueModelDiscount['discount_amount'],
            'name' => $valueModelDiscount['name'],
            'priority' => $valueModelDiscount['priority'],
            'active' => $valueModelDiscount['active'],
            'start_time' => $valueModelDiscount['start_time'],
            'end_time' => $valueModelDiscount['end_time'],
            'create_at' => $valueModelDiscount['create_at']
        ];

        if (!empty($valueModelDiscount['id'])) {
            $model->load($valueModelDiscount['id']);
            $model->addData($dataDiscount);
            $model->save();

            //delete old select customer group
            $idMultiSelect = $collectionCustomerGroup->getIdByDiscount($valueModelDiscount['id']);

            foreach ($idMultiSelect as $value) {
                $modelCustomerGroup->load($value)->delete();
            }

            // data multiselect new
            foreach ($valueCustomerGroup as $value) {
                $modelCustomerGroup = $this->_customerGroupFactory->create();
                $data = [
                    'id_discount' => $valueModelDiscount['id'],
                    'id_customer_group' => $value,
                    'create_at' => $today
                ];
                $modelCustomerGroup->addData($data)->save();
            }
            $message = 'Đã sửa thành công';
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('discount_admin/discount/edit', ['id' => $model->getId()]);
                return;
            }
            $this->_messageManager->addSuccessMessage($message);
            return $this->_redirect('discount_admin/discount/view', [$resultPage]);
        } else {
            try {
                $model->addData($dataDiscount)->save();

                $idDiscount = $model->getData('id');

                foreach ($valueCustomerGroup as $value) {
                    $modelCustomerGroup = $this->_customerGroupFactory->create();
                    $data = [
                        'id_discount' => $idDiscount,
                        'id_customer_group' => $value,
                        'create_at' => $today
                    ];
                    $modelCustomerGroup->addData($data)->save();
                }
                $message = 'Đã thêm thành công';
                $this->_messageManager->addSuccessMessage($message);
                return $this->_redirect('discount_admin/discount/view', [$resultPage]);
            } catch (\Exception $e) {
                $this->_messageManager->addErrorMessage($e);
                return $this->_redirect('discount_admin/discount/view', [$resultPage]);
            }
        }
    }
}
