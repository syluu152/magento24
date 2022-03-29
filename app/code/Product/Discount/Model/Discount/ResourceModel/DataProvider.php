<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Model\Discount\ResourceModel;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Product\Discount\Model\CustomerGroup\ResourceModel\CustomerGroup\Collection;
use Product\Discount\Model\CustomerGroup\ResourceModel\CustomerGroup\CollectionFactory as CustomerCollection;
use Product\Discount\Model\Discount\ResourceModel\Discount\CollectionFactory;

/**
 *
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $_customerGroup;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $contentCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $contentCollectionFactory,
        CustomerCollection $customerGroup,
        array $meta = [],
        array $data = []
    ) {
        $this->_customerGroup = $customerGroup->create();
        $this->collection = $contentCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        //        var_dump($this->loadedData);
        //        die();
        return $this->loadedData;

    }
}
