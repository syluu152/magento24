<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection;

class Exists extends \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection
{
    /**
     * @param \SimpleXMLElement $item
     * @return bool
     */
    public function execute(\SimpleXMLElement $item)
    {
        $this->addFieldToFilter('url', (string)$item->link);

        return $this->getSize() > 0;
    }
}
