<?php

namespace Dealer4dealer\Xcore\Model;

class PriceListRepository implements \Dealer4dealer\Xcore\Api\PriceListRepositoryInterface
{
    protected $logger;
    protected $priceListFactory;
    protected $priceListCollectionFactory;
    protected $searchResultsFactory;
    protected $priceListItemRepository;

    /**
     * @param \Psr\Log\LoggerInterface                                                                                       $logger
     * @param \Dealer4dealer\Xcore\Api\Data\PriceListInterfaceFactory|\Magento\Framework\App\Config\BaseFactory              $priceListFactory
     * @param \Dealer4dealer\Xcore\Model\ResourceModel\PriceList\CollectionFactory|\Magento\Framework\App\Config\BaseFactory $priceListCollectionFactory
     * @param \Dealer4dealer\Xcore\Api\Data\PriceListSearchResultsInterfaceFactory|\Magento\Framework\App\Config\BaseFactory $searchResultsFactory
     * @param \Dealer4dealer\Xcore\Api\PriceListItemRepositoryInterface                                                      $priceListItemRepository
     */
    public function __construct(\Psr\Log\LoggerInterface $logger,
                                \Dealer4dealer\Xcore\Api\Data\PriceListInterfaceFactory $priceListFactory,
                                \Dealer4dealer\Xcore\Model\ResourceModel\PriceList\CollectionFactory $priceListCollectionFactory,
                                \Dealer4dealer\Xcore\Api\Data\PriceListSearchResultsInterfaceFactory $searchResultsFactory,
                                \Dealer4dealer\Xcore\Api\PriceListItemRepositoryInterface $priceListItemRepository)
    {
        $this->logger                     = $logger;
        $this->priceListFactory           = $priceListFactory;
        $this->priceListCollectionFactory = $priceListCollectionFactory;
        $this->searchResultsFactory       = $searchResultsFactory;
        $this->priceListItemRepository    = $priceListItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Dealer4dealer\Xcore\Api\Data\PriceListInterface $priceList)
    {
        try {
            /** @var PriceList $priceList */
            $priceList->getResource()->save($priceList);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__(sprintf('Could not save the Price List: %s', $exception->getMessage())));
        }
        return $priceList;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($priceListId)
    {
        /** @var PriceList $priceList */
        $priceList = $this->priceListFactory->create();
        $priceList->getResource()->load($priceList, $priceListId);
        if (!$priceList->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__(sprintf('Price List with id %s does not exist', $priceListId)));
        }

        return $priceList;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Dealer4dealer\Xcore\Model\ResourceModel\PriceList\Collection $collection */
        $collection = $this->priceListCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
//                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        /** @var \Dealer4dealer\Xcore\Api\Data\PriceListSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Dealer4dealer\Xcore\Api\Data\PriceListInterface $priceList)
    {
        try {
            /** @var PriceList $priceList */
            $priceList->getResource()->delete($priceList);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__(sprintf('Could not delete the Price List: %s', $exception->getMessage())));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($priceListId)
    {
        return $this->delete($this->getById($priceListId));
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonList()
    {
        /** @var \Dealer4dealer\Xcore\Model\ResourceModel\PriceList\Collection $collection */
        $collection = $this->getList(new \Magento\Framework\Api\SearchCriteria());

        $result = [];

        /** @var \Dealer4dealer\Xcore\Api\Data\PriceListInterface $priceList */
        foreach ($collection->getItems() as $priceList) {
//            $priceList->setItems($this->priceListItemRepository->getByPriceListId($priceList->getId()));

            $result[] = $priceList;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonById($id, $withItems = true)
    {
        /** @var PriceList $priceList */
        $priceList = $this->priceListFactory->create();
        $priceList->getResource()->load($priceList, $id);
        if (!$priceList->getId()) {
            return null;
        }

        if ($withItems)
            $priceList->setItems($this->priceListItemRepository->getByPriceListId($priceList->getId()));

        return $priceList;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonByGuid($guid, $withItems = true)
    {
        /** @var PriceList $priceList */
        $priceList = $this->priceListFactory->create();
        $priceList->getResource()->load($priceList, $guid, 'guid');
        if (!$priceList->getGuid()) {
            return null;
        }

        if ($withItems)
            $priceList->setItems($this->priceListItemRepository->getByPriceListId($priceList->getId()));

        return $priceList;
    }

    /**
     * {@inheritdoc}
     */
    public function saveJson(\Dealer4dealer\Xcore\Api\Data\PriceListInterface $price_list)
    {
        $priceList = $this->getOrSavePriceList($price_list);

        if ($price_list->getItems()) {
            /** @var \Dealer4dealer\Xcore\Api\Data\PriceListItemInterface[] $items */
            $items = [];

            /** @var \Dealer4dealer\Xcore\Api\Data\PriceListItemInterface $item */
            foreach ($price_list->getItems() as $item) {
                $addedItem = $this->getOrSavePriceListItem($priceList->getId(), $item);
                if ($addedItem) $items[] = $addedItem;
            }

            $priceList->setItems($items);
        }

        return $priceList;
    }

    /**
     * @param \Dealer4dealer\Xcore\Api\Data\PriceListInterface $newPriceList
     * @return PriceList
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function getOrSavePriceList(\Dealer4dealer\Xcore\Api\Data\PriceListInterface $newPriceList)
    {
        /** @var PriceList $priceList */
        $priceList = $this->priceListFactory->create();
        $priceList->getResource()->load($priceList, $newPriceList->getGuid(), 'guid');

        $priceList->setGuid($newPriceList->getGuid());
        $priceList->setCode($newPriceList->getCode());

        $priceList->getResource()->save($priceList);

        return $priceList;
    }

    /**
     * @param int                                                  $priceListId
     * @param \Dealer4dealer\Xcore\Api\Data\PriceListItemInterface $item
     * @return PriceListItem
     */
    private function getOrSavePriceListItem($priceListId, \Dealer4dealer\Xcore\Api\Data\PriceListItemInterface $item)
    {
        try {
            /** @var PriceListItem $priceListItem */
            $priceListItem = $this->priceListItemRepository->getUniqueRow($item->getProductSku(), $item->getQty(), $priceListId);

            // Set the guid and code (overwrite code if previous price list existed)
            $priceListItem->setPriceListId($priceListId);
            $priceListItem->setProductSku($item->getProductSku());
            $priceListItem->setQty($item->getQty());
            $priceListItem->setPrice($item->getPrice());
            $priceListItem->setStartDate($item->getStartDate());
            $priceListItem->setEndDate($item->getEndDate());
            $priceListItem->setProcessed(0);

            $priceListItem->getResource()->save($priceListItem);

            return $priceListItem;
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Failed to get or save price list item: %s', $exception->getMessage()));
        }
    }
}
