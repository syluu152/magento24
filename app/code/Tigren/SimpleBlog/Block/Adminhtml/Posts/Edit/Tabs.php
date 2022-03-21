<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Block\Adminhtml\Posts\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 *
 */
class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('post_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Post Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'post_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Tigren\SimpleBlog\Block\Adminhtml\Posts\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );

        return parent::_beforeToHtml();
    }
}
