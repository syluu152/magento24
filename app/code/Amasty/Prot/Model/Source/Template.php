<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Template implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options = null;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Template\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(\Amasty\Prot\Model\ResourceModel\Template\CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];

            foreach ($this->getTemplates() as $template) {
                $this->options[] = [
                    'value' => $template->getId(),
                    'label' => $template->getName()
                ];
            }
        }

        return $this->options;
    }

    /**
     * @return mixed
     */
    protected function getTemplates()
    {
        return $this->collectionFactory->create()->getItems();
    }
}
