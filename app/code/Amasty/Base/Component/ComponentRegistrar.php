<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Component;

/**
 * ComponentRegistrar class is necessary for external sample files.
 * By default Magento_ImportExport Download Controller checks only Magento_ImportExport sample files folder.
 */
class ComponentRegistrar extends \Magento\Framework\Component\ComponentRegistrar
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $samples;

    /**
     * ComponentRegistrar constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\DataObject $samples
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\DataObject $samples
    ) {
        $this->request = $request;
        $this->samples = $samples;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($type, $componentName)
    {
        $moduleName = $this->samples->getData($this->request->getParam('filename'));
        if ($moduleName) {
            $componentName = $moduleName;
        }
        return parent::getPath($type, $componentName);
    }
}
