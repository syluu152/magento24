<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 *
 */
class Posts extends Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_posts';
        $this->_blockGroup = 'Tigren_SimpleBlog';
        $this->_headerText = __('Tigren Manage Posts');
        $this->_addButtonLabel = __('Add New Post');
        parent::_construct();
    }
}
