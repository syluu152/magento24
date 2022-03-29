<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model;

use Amasty\Prot\Api\Data\ScheduleInterface;

class Schedule extends \Magento\Framework\Model\AbstractModel implements ScheduleInterface
{
    protected function _construct()
    {
        $this->_init(\Amasty\Prot\Model\ResourceModel\Schedule::class);
        $this->setIdFieldName(ScheduleInterface::SCHEDULE_ID);
    }

    /**
     * @inheritdoc
     */
    public function getScheduleId()
    {
        return $this->_getData(ScheduleInterface::SCHEDULE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setScheduleId($scheduleId)
    {
        $this->setData(ScheduleInterface::SCHEDULE_ID, $scheduleId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmastyOptionId()
    {
        return $this->_getData(ScheduleInterface::AMASTY_OPTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAmastyOptionId($optionId)
    {
        $this->setData(ScheduleInterface::AMASTY_OPTION_ID, $optionId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOriginalProductId()
    {
        return $this->_getData(ScheduleInterface::ORIGINAL_PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOriginalProductId($productId)
    {
        $this->setData(ScheduleInterface::ORIGINAL_PRODUCT_ID, $productId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->_getData(ScheduleInterface::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($productId)
    {
        $this->setData(ScheduleInterface::PRODUCT_ID, $productId);

        return $this;
    }
}
