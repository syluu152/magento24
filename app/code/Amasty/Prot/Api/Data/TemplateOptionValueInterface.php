<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Api\Data;

interface TemplateOptionValueInterface
{
    const MAIN_TABLE = 'amasty_prot_template_option_value';

    const ID = 'id';
    const TEMPLATE_OPTION_ID = 'template_option_id';
    const SWATCH_VALUE = 'swatch_value';

    const DATA_IDENTIFIER = 'template_option_value_id';

    const RELATION_TABLE = 'amasty_prot_option_value_relation';
    const RELATION_VALUE_ID = 'value_id';
    const RELATION_TEMPLATE_VALUE_ID = 'parent_value_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Prot\Api\Data\TemplateOptionValueInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getTemplateOptionId();

    /**
     * @param int $templateOptionId
     *
     * @return \Amasty\Prot\Api\Data\TemplateOptionValueInterface
     */
    public function setTemplateOptionId($templateOptionId);

    /**
     * @return int
     */
    public function getOptionValueId();

    /**
     * @param int $optionValueId
     *
     * @return \Amasty\Prot\Api\Data\TemplateOptionValueInterface
     */
    public function setOptionValueId($optionValueId);

    /**
     * @return string
     */
    public function getSwatchValue();

    /**
     * @param string $swatchvalue
     *
     * @return \Amasty\Prot\Api\Data\TemplateOptionValueInterface
     */
    public function setSwatchValue($swatchvalue);
}
