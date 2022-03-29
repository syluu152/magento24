<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Template\Grid;

use Amasty\Prot\Api\Data\ScheduleInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Framework\EntityManager\MetadataPool;

class Collection extends \Amasty\Prot\Model\ResourceModel\Template\Collection implements SearchResultInterface
{
    protected $aggregations;

    /**
     * @var array
     */
    private $mappedFields = [
        'template_id' => 'main_table.template_id',
        'qty' => 'count(DISTINCT(pe.entity_id))',
        'qty_pending' => 'count(DISTINCT(cpe.entity_id))',
        'status' => 'IF(count(DISTINCT(cpe.entity_id)) > 0 , 1, 0)'
    ];

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        MetadataPool $metadataPool,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->metadataPool = $metadataPool;
    }

    protected function _construct()
    {
        foreach ($this->mappedFields as $field => $mappedField) {
            $this->addFilterToMap($field, new \Zend_Db_Expr($mappedField));
        }
        parent::_construct();
    }

    /**
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     * @return void
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return \Amasty\Prot\Model\ResourceModel\Template\Collection
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @param array|null $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Compatibility with m2.1.8 - 2.1.9
     *
     * @param null $limit
     * @param null $offset
     * @return \Magento\Framework\DB\Select
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->joinOptionTable();
        $this->joinScheduleTable();
        parent::_renderFiltersBefore();
    }

    public function joinOptionTable()
    {
        $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $link = $productMetadata->getLinkField();

        $this->getSelect()->joinLeft(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.template_id = main_table.template_id',
            []
        )->joinLeft(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.parent_option_id = am_options.option_id',
            null
        )->joinLeft(
            ['po' => $this->getTable('catalog_product_option')],
            'prot_relation.option_id = po.option_id',
            null
        )->joinLeft(
            ['pe' => $this->getTable('catalog_product_entity')],
            sprintf('pe.%s = po.product_id', $link),
            ['qty' => 'count(DISTINCT(pe.entity_id))']
        );

        $this->getSelect()->group('main_table.template_id');
    }

    public function joinScheduleTable()
    {
        $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $link = $productMetadata->getLinkField();
        $this->getSelect()->joinLeft(
            ['schedule' => $this->getTable(ScheduleInterface::MAIN_TABLE)],
            'am_options.option_id = schedule.amasty_option_id',
            []
        )->joinLeft(
            ['cpe' => $this->getTable('catalog_product_entity')],
            sprintf('cpe.%s = schedule.product_id', $link),
            [
                'qty_pending' => 'count(DISTINCT(cpe.entity_id))',
                'status' => 'IF(count(DISTINCT(cpe.entity_id)) > 0 , 1, 0)'
            ]
        );
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return \Amasty\Prot\Model\ResourceModel\Template\Collection
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if (array_key_exists($field, $this->mappedFields)) {
            $field = $this->mappedFields[$field];
        }
        return parent::addOrder($field, $direction);
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return \Amasty\Prot\Model\ResourceModel\Template\Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if (array_key_exists($field, $this->mappedFields)) {
            $field = $this->mappedFields[$field];
        }
        return parent::setOrder($field, $direction);
    }
}
