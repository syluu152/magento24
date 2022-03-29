<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\MassUpdate;

use Amasty\Prot\Model\ResourceModel\Template\Grid\Collection as TemplateCollection;
use Magento\Backend\Block\Template;

class Content extends Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'mass_apply.phtml';

    /**
     * @var TemplateCollection
     */
    private $templateCollection;

    public function __construct(
        TemplateCollection $templateCollection,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->templateCollection = $templateCollection;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTemplates()
    {
        return $this->templateCollection->getItems();
    }

    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->toHtml();
    }
}
