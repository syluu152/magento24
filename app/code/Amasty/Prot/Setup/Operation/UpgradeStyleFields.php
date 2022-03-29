<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Setup\Operation;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeStyleFields
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $this->updateOptionFields($setup);
        $this->updateValueFields($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function updateOptionFields(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable(TemplateOptionInterface::MAIN_TABLE);

        foreach (['swatch_attribute', 'swatch_match', 'font_text', 'display_type'] as $field) {
            $connection->dropColumn(
                $table,
                $field
            );
        }

        $connection->addColumn(
            $table,
            TemplateOptionInterface::OPTIONS_LIST_TYPE,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Option List Type'
            ]
        );
        $connection->addColumn(
            $table,
            TemplateOptionInterface::USE_SWATCHES,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Use Swatches'
            ]
        );
        $setup->getConnection()->changeColumn(
            $table,
            TemplateOptionInterface::FONT_SIZE,
            TemplateOptionInterface::FONT_SIZE,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'Font Size'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function updateValueFields(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(TemplateOptionValueInterface::MAIN_TABLE),
            TemplateOptionValueInterface::SWATCH_VALUE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'size' => 200,
                'comment' => 'Swatch Value If Used'
            ]
        );
    }
}
