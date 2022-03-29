<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\BlogManager\Model\ResourceModel\Blog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tigren\BlogManager\Model\ResourceModel\Blog;

/**
 *
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Tigren\BlogManager\Model\Blog::class,
            Blog::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
