<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Formatter;

use Magento\Framework\Xml\Generator as XmlGenerator;

class Xml implements FormatterInterface
{
    public const FILE_EXTENSION = 'xml';

    /**
     * @var XmlGenerator
     */
    private $xmlGenerator;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $rootNodeName;

    public function __construct(
        XmlGenerator $xmlGenerator,
        array $data,
        string $rootNodeName
    ) {
        $this->xmlGenerator = $xmlGenerator;
        $this->data = $data;
        $this->rootNodeName = $rootNodeName;
    }

    public function getContent(): string
    {
        $content = $this->xmlGenerator
            ->arrayToXml([$this->rootNodeName => $this->data])
            ->getDom()
            ->saveXML();

        return $content;
    }

    public function getExtension(): string
    {
        return self::FILE_EXTENSION;
    }
}
