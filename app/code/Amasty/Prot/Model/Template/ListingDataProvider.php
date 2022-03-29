<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Template;

use Magento\Framework\Api\Filter;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class ListingDataProvider extends DataProvider
{
    /**
     * @var array
     */
    private $mappedFields = [
        'template_id' => 'main_table.template_id'
    ];

    /**
     * @param Filter $filter
     * @return mixed|void
     */
    public function addFilter(Filter $filter)
    {
        if (array_key_exists($filter->getField(), $this->mappedFields)) {
            $mappedField = $this->mappedFields[$filter->getField()];
            $filter->setField($mappedField);
        }

        parent::addFilter($filter);
    }
}
