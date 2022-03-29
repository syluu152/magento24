<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

/**
 * @codingStandardsIgnoreFile
 */

namespace Amasty\Prot\Test\Unit\Block\Catalog\Block\Product\View\Type;

use Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select;
use Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select\Checkable as Checkable;
use Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select\CheckableFactory as CheckableFactory;
use Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select\Multiple as Multiple;
use Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select\MultipleFactory as MultipleFactory;
use Magento\Catalog\Model\Product\Option;
use Amasty\Prot\Test\Unit\Traits;

/**
 * Class SelectTest
 * @see Select
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class SelectTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var Select
     */
    private $block;

    /**
     * @covers Select::getOptionBlock
     * @dataProvider getOptionBlockDataProvider
     */
    public function testGetOptionBlock($type, $result)
    {
        $this->block = $this->getObjectManager()->getObject(Select::class);
        $checkable = $this->getObjectManager()->getObject(Checkable::class);
        $multiple = $this->getObjectManager()->getObject(Multiple::class);

        $multipleFactory = $this->getMockBuilder(MultipleFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $multipleFactory->expects($this->any())->method('create')->willReturn($multiple);

        $checkableFactory = $this->getMockBuilder(CheckableFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $checkableFactory->expects($this->any())->method('create')->willReturn($checkable);

        $this->setProperty($this->block, 'multipleFactory', $multipleFactory, Select::class);
        $this->setProperty($this->block, 'checkableFactory', $checkableFactory, Select::class);

        $currentResult = $this->invokeMethod($this->block, 'getOptionBlock', [$type]);
        if ($currentResult) {
            $this->assertEquals(get_class($currentResult), $result);
        } else {
            $this->assertEquals($currentResult, $result);
        }
    }

    /**
     * @return array
     */
    public function getOptionBlockDataProvider()
    {
        return [
            [Option::OPTION_TYPE_DROP_DOWN, Multiple::class],
            [Option::OPTION_TYPE_MULTIPLE, Multiple::class],
            [Option::OPTION_TYPE_RADIO, Checkable::class],
            [Option::OPTION_TYPE_CHECKBOX, Checkable::class],
            ['test', null],
        ];
    }
}
