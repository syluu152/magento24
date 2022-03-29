<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Setup;

use Amasty\Prot\Api\Data\TemplateInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\ScheduleInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tablesToDrop = [
            TemplateInterface::MAIN_TABLE,
            TemplateOptionInterface::MAIN_TABLE,
            TemplateOptionInterface::RELATION_TABLE,
            ScheduleInterface::MAIN_TABLE
        ];

        foreach ($tablesToDrop as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }

        $setup->endSetup();
    }
}
