<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

/**
 * @codingStandardsIgnoreFile
 */

namespace Amasty\Prot\Test\Unit\Plugin\Bundle\Ui\DataProvider\Product\Form\Modifier;

use Amasty\Prot\Test\Unit\Traits;
use Amasty\Prot\Plugin\Bundle\Ui\DataProvider\Product\Form\Modifier\BundleCustomOptionsPlugin;

/**
 * Class BundleCustomOptionsPluginTest
 * @see BundleCustomOptionsPlugin
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class BundleCustomOptionsPluginTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var BundleCustomOptionsPlugin
     */
    private $plugin;

    /**
     * @covers BundleCustomOptionsPlugin::getGroupCodeByField
     * @dataProvider getGroupCodeByFieldDataProvider
     */
    public function testGetGroupCodeByField($meta, $field, $result)
    {
        $this->plugin = $this->getObjectManager()->getObject(BundleCustomOptionsPlugin::class);
        $current = $this->invokeMethod($this->plugin, 'getGroupCodeByField', [$meta, $field]);
        $this->assertEquals($current, $result);
    }

    /**
     * @return array
     */
    public function getGroupCodeByFieldDataProvider()
    {
        return [
            [['test' => [], 'test1' => ['children' => ['findme' => 1]]], 'findme' , 'test1'],
            [['test' => [], 'test1' => ['children' => ['container_findme' => 1]]], 'findme' , 'test1'],
            [['test' => [], 'test1' => ['children' => ['container_findme' => 1]]], 'findme1234' , false],
        ];
    }
}
