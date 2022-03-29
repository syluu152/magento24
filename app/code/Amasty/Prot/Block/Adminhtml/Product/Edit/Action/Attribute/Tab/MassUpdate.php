<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Block\Adminhtml\Product\Edit\Action\Attribute\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Block\Adminhtml\Form;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class MassUpdate extends Form implements TabInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->productMetadata = $productMetadata;
    }

    /**
     * Tab settings
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Assign/Remove Custom Option Templates');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getLegend()
    {
        return __('Assign/Remove Custom Option Templates');
    }

    /**
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setFieldNameSuffix('attributes');
        $form->addFieldset('amproduct_templates', ['legend' => $this->getLegend()]);
        $form->getElement(
            'amproduct_templates'
        )->setRenderer(
            $this->getLayout()
                ->createBlock(
                    \Amasty\Prot\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\MassUpdate\Content::class
                )
        );
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
