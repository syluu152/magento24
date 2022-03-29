<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model;

use \Amasty\Prot\Api\Data\TemplateInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Template extends \Magento\Framework\Model\AbstractModel implements TemplateInterface, IdentityInterface
{
    const CACHE_TAG = 'am_template_template';

    const PERSIST_NAME = 'am_template_template';

    protected function _construct()
    {
        $this->_init(\Amasty\Prot\Model\ResourceModel\Template::class);
        $this->setIdFieldName(TemplateInterface::TEMPLATE_ID);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getTemplateId()];
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId()
    {
        return $this->_getData(TemplateInterface::TEMPLATE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTemplateId($templateId)
    {
        $this->setData(TemplateInterface::TEMPLATE_ID, $templateId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(TemplateInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(TemplateInterface::NAME, $name);

        return $this;
    }
}
