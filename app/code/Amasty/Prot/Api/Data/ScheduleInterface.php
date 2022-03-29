<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Api\Data;

/**
 * @api
 */
interface ScheduleInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const MAIN_TABLE = 'amasty_prot_schedule';

    const SCHEDULE_ID = 'schedule_id';
    const AMASTY_OPTION_ID = 'amasty_option_id';
    const ORIGINAL_PRODUCT_ID = 'original_product_id';
    const PRODUCT_ID = 'product_id';
    const CREATED_AT = 'created_at';
    const BATCH_SIZE = 100;

    /**#@-*/

    /**
     * @return int
     */
    public function getScheduleId();

    /**
     * @param int $scheduleId
     *
     * @return ScheduleInterface
     */
    public function setScheduleId($scheduleId);

    /**
     * @return int
     */
    public function getAmastyOptionId();

    /**
     * @param int $optionId
     *
     * @return ScheduleInterface
     */
    public function setAmastyOptionId($optionId);

    /**
     * @return int
     */
    public function getOriginalProductId();

    /**
     * @param int $productId
     *
     * @return ScheduleInterface
     */
    public function setOriginalProductId($productId);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $productId
     *
     * @return ScheduleInterface
     */
    public function setProductId($productId);
}
