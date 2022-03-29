<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Test\Unit\Model\SysInfo\Formatter;

use Amasty\Base\Model\SysInfo\Formatter\Xml;
use Magento\Framework\Xml\Generator as XmlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class XmlTest extends TestCase
{
    /**
     * @var Xml
     */
    private $model;

    /**
     * @var XmlGenerator|MockObject
     */
    private $xmlGeneratorMock;

    protected function setUp(): void
    {
        $this->xmlGeneratorMock = $this->createMock(XmlGenerator::class);
    }

    public function testGetContent(): void
    {
        $data = [];
        $rootNodeName = 'node';
        $content = 'content';
        $this->model = new Xml($this->xmlGeneratorMock, $data, $rootNodeName);
        $domDocument = $this->createMock(\DOMDocument::class);

        $this->xmlGeneratorMock
            ->expects($this->once())
            ->method('arrayToXml')
            ->willReturnSelf();
        $this->xmlGeneratorMock
            ->expects($this->once())
            ->method('arrayToXml')
            ->willReturnSelf();
        $this->xmlGeneratorMock
            ->expects($this->once())
            ->method('getDom')
            ->willReturn($domDocument);
        $domDocument
            ->expects($this->once())
            ->method('saveXML')
            ->willReturn($content);

        $this->assertEquals($content, $this->model->getContent());
    }
}
