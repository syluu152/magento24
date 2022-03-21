<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Block\Adminhtml\Posts\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Tigren\SimpleBlog\Model\PostsFactory;
use Tigren\SimpleBlog\Model\System\Config\Status;

/**
 *
 */
class Info extends Generic implements TabInterface
{
    /**
     * @var Config
     */
    protected $_wysiwygConfig;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        Context     $context,
        Registry    $registry,
        FormFactory $formFactory,
        Config      $wysiwygConfig,
        Status      $status,
        array       $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Posts Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Posts Info');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model PostsFactory */
        $model = $this->_coreRegistry->registry('tigren_blog');

        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('post');
        // new filed

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'short_description',
            'text',
            [
                'name' => 'short_description',
                'label' => __('Short Description'),
                'title' => __('Short Description'),
                'required' => true
            ]
        );
        $fieldset->addField('content', 'editor', [
            'name' => 'content',
            'label' => 'Content',
            'config' => $this->_wysiwygConfig->getConfig(),
            'wysiwyg' => true,
            'required' => false
        ]);
        $fieldset->addField(
            'author',
            'text',
            [
                'name' => 'author',
                'label' => __('Author'),
                'title' => __('Author'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'options' => $this->_status->toOptionArray()
            ]
        );

        $fieldset->addField(
            'image',
            'image',
            array(
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            )
        );

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
