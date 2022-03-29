<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\ProtGraphQl\Model;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\Entity\MapperInterface;

class CustomizableOptionTypeResolver implements TypeResolverInterface
{
    const ENTITY_TYPE = 'amprot_customizable_options';

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @param MapperInterface $mapper
     */
    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function resolveType(array $data) : string
    {
        $map = $this->mapper->getMappedTypes(self::ENTITY_TYPE);
        if (isset($map[$data['type']])) {
            return $map[$data['type']];
        }

        return '';
    }
}
