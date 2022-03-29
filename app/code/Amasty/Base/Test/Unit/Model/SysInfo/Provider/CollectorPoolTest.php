<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Test\Unit\Model\SysInfo\Provider;

use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;
use Amasty\Base\Model\SysInfo\Provider\CollectorPool;
use Magento\Framework\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class CollectorPoolTest extends TestCase
{
    /**
     * @var CollectorPool
     */
    private $model;

    public function testCheckProviderInstance(): void
    {
        $collectorMock = $this->createMock(CollectorInterface::class);
        $collectors = ['collectorGroup' => ['collectorName' => $collectorMock]];
        $this->model = new CollectorPool($collectors);
    }

    public function testCheckProviderInstanceOnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collectors = ['collectorGroup' => ['collectorName' => 'collectorClass']];
        $this->model = new CollectorPool($collectors);
    }

    public function testGet(): void
    {
        $groupName = 'collectorGroup';
        $collectorMock = $this->createMock(CollectorInterface::class);
        $collectors = [$groupName => ['collectorName' => $collectorMock]];
        $this->model = new CollectorPool($collectors);

        $this->assertEquals($collectors[$groupName], $this->model->get($groupName));
    }

    public function testGetOnException(): void
    {
        $collectorMock = $this->createMock(CollectorInterface::class);
        $collectors = ['collectorGroup' => ['collectorName' => $collectorMock]];
        $this->model = new CollectorPool($collectors);

        $this->expectException(NotFoundException::class);
        $this->model->get('collectorGroup1');
    }
}
