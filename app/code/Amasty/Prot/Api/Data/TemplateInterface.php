<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Api\Data;

/**
 * @api
 */
interface TemplateInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const MAIN_TABLE = 'amasty_prot_template';

    const TEMPLATE_ID = 'template_id';
    const NAME = 'name';

    /**#@-*/

    /**
     * @return int
     */
    public function getTemplateId();

    /**
     * @param int $templateId
     *
     * @return TemplateInterface
     */
    public function setTemplateId($templateId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return TemplateInterface
     */
    public function setName($name);
}
