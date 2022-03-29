<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Plugin\Adminhtml\Block\Widget\Form\Element;

use Magento\Backend\Block\Widget\Form\Element;

/**
 * Fix group dependence on old Magento
 */
class Dependence
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(\Magento\Framework\App\ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param Element\Dependence $subject
     * @param \Closure $proceed
     * @param $fieldName
     * @param $fieldNameFrom
     * @param $refField
     * @return Element\Dependence
     */
    public function aroundAddFieldDependence(
        Element\Dependence $subject,
        \Closure $proceed,
        $fieldName,
        $fieldNameFrom,
        $refField
    ) {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '<')
            && strpos($fieldName, 'groups[][fields]') !== false
        ) {
            return $subject;
        }

        return $proceed($fieldName, $fieldNameFrom, $refField);
    }
}
