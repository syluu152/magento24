<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Setup\Operation;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeDependencyField
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->updateField($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function updateField(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $setup->getConnection()->changeColumn(
            $setup->getTable(TemplateOptionInterface::MAIN_TABLE),
            TemplateOptionInterface::DEPENDENCY,
            TemplateOptionInterface::DEPENDENCY,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false
            ]
        );

        $setup->endSetup();
    }
}
