<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\SysInfoService;

use Amasty\Base\Model\SysInfo\Command\SysInfoService\Download;
use Amasty\Base\Model\SysInfo\Formatter\Xml;
use Amasty\Base\Model\SysInfo\Formatter\XmlFactory;
use Amasty\Base\Model\SysInfo\Provider\Collector;
use Amasty\Base\Model\SysInfo\Provider\CollectorPool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DownloadTest extends TestCase
{
    /**
     * @var Download
     */
    private $model;

    /**
     * @var Collector|MockObject
     */
    private $collectorMock;

    /**
     * @var XmlFactory|MockObject
     */
    private $xmlFactoryMock;

    protected function setUp(): void
    {
        $this->collectorMock = $this->createMock(Collector::class);
        $this->xmlFactoryMock = $this->createPartialMock(XmlFactory::class, ['create']);

        $this->model = new Download(
            $this->collectorMock,
            $this->xmlFactoryMock
        );
    }

    public function testExecute(): void
    {
        $data = [];
        $xmlMock = $this->createMock(Xml::class);

        $this->collectorMock
            ->expects($this->once())
            ->method('collect')
            ->with(CollectorPool::SYS_INFO_SERVICE_GROUP)
            ->willReturn($data);

        $this->xmlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(['data' => $data, 'rootNodeName' => 'info'])
            ->willReturn($xmlMock);

        $this->assertEquals($xmlMock, $this->model->execute());
    }
}
