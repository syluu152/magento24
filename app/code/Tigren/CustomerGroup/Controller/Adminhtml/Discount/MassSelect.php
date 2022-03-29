<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Controller\Adminhtml\Discount;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Tigren\CustomerGroup\Model\DiscountProduct\DiscountProductFactory;

/**
 *
 */
class MassSelect extends Action
{
    /**
     * @var DiscountProductFactory
     */
    protected $_discountProduct;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param DiscountProductFactory $discountProduct
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DiscountProductFactory $discountProduct
    ) {
        $this->_discountProduct = $discountProduct;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $idDiscount = $_SESSION['id_discount'];

        $countSuccess = 0;
        $countError = 0;

        $today = date('Y/m/d h:i:s');
        foreach ($collection as $item) {
            try {
                $modelDiscountProduct = $this->_discountProduct->create();
                $data =
                    [
                        'id_discount' => $idDiscount,
                        'id_product' => $item->getId(),
                        'create_at' => $today
                    ];
                $modelDiscountProduct->addData($data)->save();
                $countSuccess++;
            } catch (Exception $e) {
                $countError++;
            }
        }
        unset($_SESSION['id_discount']);
        if ($countSuccess) {
            $this->messageManager->addSuccessMessage(__('%1 Sản phẩm đã được thêm discount.', $countSuccess));
        }

        if ($countError) {
            $this->messageManager->addErrorMessage(__('%1 Sản phẩm đã có discount này.', $countError));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('discount_admin/discount/view');
    }
}
