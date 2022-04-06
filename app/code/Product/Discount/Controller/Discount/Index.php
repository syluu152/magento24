<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Product\Discount\Controller\Discount;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Product\Discount\Model\Discount\ResourceModel\Discount\Collection;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $_customerSession;
    protected $_discountCollection;
    protected $_pageFactory;
    protected $_discountProduct;

    public function __construct(
        Context $context,
        Session $customerSession,
        Collection $discountCollection,
        PageFactory $page
    ) {
        parent::__construct($context);
        //        if (!$customerSession->isLoggedIn()) {
        //            die('not login');
        //            $this->_redirect('customer/account/login');
        //        } else {
        //            $this->_customerSession = $customerSession;
        //        }

        $this->_pageFactory = $page;
        $this->_discountCollection = $discountCollection;
    }

    public function execute()
    {
        $customerGroupId = $this->_customerSession->getCustomerGroupId();

        $listDiscount = $this->_discountCollection->getListDiscount($customerGroupId);

        $resultPage = $this->_pageFactory->create();
        $block = $resultPage->getLayout()->getBlock('exam.discount.layout.listdiscount');
        $block->setData('discount', $listDiscount);

        return $resultPage;
    }
}
