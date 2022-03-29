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
interface TemplateOptionInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const MAIN_TABLE = 'amasty_prot_template_option';
    const RELATION_TABLE = 'amasty_prot_option_relation';
    const OPTION_ID = 'option_id';
    const TEMPLATE_ID = 'template_id';
    const DEPENDENCY = 'dependency';
    const FONT_COLOR = 'font_color';
    const FONT_SIZE = 'font_size';
    const PARENT_OPTION_ID = 'parent_option_id';
    const RELATION_OPTION_ID = 'option_id';
    const OPTIONS_LIST_TYPE = 'option_list_type';
    const USE_SWATCHES = 'use_swatches';
    /**#@-*/

    /**
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $optionId
     *
     * @return TemplateOptionInterface
     */
    public function setOptionId($optionId);

    /**
     * @return int
     */
    public function getTemplateId();

    /**
     * @param int $templateId
     *
     * @return TemplateOptionInterface
     */
    public function setTemplateId($templateId);

    /**
     * @return string
     */
    public function getDependency();

    /**
     * @param string $dependency
     *
     * @return TemplateOptionInterface
     */
    public function setDependency($dependency);

    /**
     * @return string
     */
    public function getFontColor();

    /**
     * @param string $color
     *
     * @return TemplateOptionInterface
     */
    public function setFontColor($color);

    /**
     * @return int
     */
    public function getFontSize();

    /**
     * @param int $size
     *
     * @return TemplateOptionInterface
     */
    public function setFontSize($size);

    /**
     * @return int
     */
    public function getOptionListType();

    /**
     * @param int $type
     *
     * @return TemplateOptionInterface
     */
    public function setOptionListType($type);

    /**
     * @return int
     */
    public function getUseSwatches();

    /**
     * @param int $value
     *
     * @return TemplateOptionInterface
     */
    public function setUseSwatches($value);
}
