<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 *
 */
class Posts extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        // tigren_blog is table name and id is Primary of Table
        $this->_init('tigren_blog', 'id');
    }
}
