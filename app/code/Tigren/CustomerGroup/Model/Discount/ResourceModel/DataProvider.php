<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Model\Discount\ResourceModel;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Tigren\CustomerGroup\Model\CustomerGroup\ResourceModel\CustomerGroup\CollectionFactory as CustomerCollection;
use Tigren\CustomerGroup\Model\Discount\ResourceModel\Discount\CollectionFactory;

/**
 *
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var
     */
    protected $_customerGroup;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $contentCollectionFactory
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $contentCollectionFactory,
        CustomerCollection $customerGroup
    ) {
        $this->_customerGroup = $customerGroup->create();
        $this->collection = $contentCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = array();
        $CustomerGroup = array();
        foreach ($items as $content) {
            $id = $content->getId();
            $this->loadedData[$id] = $content->getData();
            foreach ($this->_customerGroup->getDataCustomerGroup($id) as $key => $value) {
                $CustomerGroup[$key] = $value;
            }
            $this->loadedData[$id]['id_customer_group'] = $CustomerGroup;
        }
        //        echo "<pre>";
        //        print_r($this->loadedData);
        //        die('***');
        return $this->loadedData;

    }
}
