<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Import\Mapping;

/**
 * @since 1.4.6
 */
interface MappingInterface
{
    /**
     * @return array
     */
    public function getValidColumnNames();

    /**
     * @param string $columnName
     *
     * @throws \Amasty\Base\Exceptions\MappingColumnDoesntExist
     * @return string|bool
     */
    public function getMappedField($columnName);

    /**
     * @throws \Amasty\Base\Exceptions\MasterAttributeCodeDoesntSet
     * @return string
     */
    public function getMasterAttributeCode();
}
