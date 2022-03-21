<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 *
 */
class InstallData implements InstallDataInterface
{

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableName = $setup->getTable('tigren_blog');
        //Check for the existence of the table
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $data = [
                [
                    'title' => 'How to Speed Up Magento 2 Website',
                    'short_description' => 'Customers will feel satisfied when your site responds quickly',
                    'content' => 'Speeding up your Magento 2 website is very important, it affects user experience. Customers will feel satisfied when your site responds quickly',
                    'author' => 'John',
                    'created_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'title' => 'Optimize SEO for Magento Website',
                    'short_description' => 'Optimize SEO for Magento Website Short Description',
                    'content' => 'One of the important reasons why many people choose Magento 2 for their website is the ability to create SEO friendly',
                    'author' => 'Peter',
                    'created_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'title' => 'Top 10 eCommerce Websites',
                    'short_description' => 'With very large revenue contributing to the world economy',
                    'content' => 'These are the websites of famous e-commerce corporations in the world. With very large revenue contributing to the world economy',
                    'author' => 'Philip',
                    'created_at' => date('Y-m-d H:i:s'),
                ],
            ];
            foreach ($data as $item) {
                //Insert data
                $setup->getConnection()->insert($tableName, $item);
            }
        }
        $setup->endSetup();
    }
}
