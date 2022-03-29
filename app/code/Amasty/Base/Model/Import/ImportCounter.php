<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Import;

class ImportCounter
{
    /**
     * @var int
     */
    private $created = 0;

    /**
     * @var int
     */
    private $updated = 0;

    /**
     * @var int
     */
    private $deleted = 0;

    public function incrementCreated($incrementOn = 1)
    {
        $this->created += (int)$incrementOn;
    }

    public function incrementUpdated($incrementOn = 1)
    {
        $this->updated += (int)$incrementOn;
    }

    public function incrementDeleted($incrementOn = 1)
    {
        $this->deleted += (int)$incrementOn;
    }

    /**
     * @return int
     */
    public function getCreatedCount()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getUpdatedCount()
    {
        return $this->updated;
    }

    /**
     * @return int
     */
    public function getDeletedCount()
    {
        return $this->deleted;
    }
}
