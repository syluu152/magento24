<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Schedule;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Amasty\Prot\Api\Data\ScheduleInterface;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(ScheduleInterface::SCHEDULE_ID);
        $this->_init(
            \Amasty\Prot\Model\Schedule::class,
            \Amasty\Prot\Model\ResourceModel\Schedule::class
        );
    }

    /**
     * @param int $templateId
     *
     * @return $this
     */
    public function applyTemplateIdFilter(int $templateId)
    {
        $this->getSelect()->join(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.option_id = main_table.amasty_option_id AND am_options.template_id=' . $templateId,
            []
        );

        return $this;
    }
}
