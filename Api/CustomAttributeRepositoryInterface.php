<?php


namespace Dealer4dealer\Xcore\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CustomAttributeRepositoryInterface
{


    /**
     * Save CustomAttribute
     * @param \Dealer4dealer\Xcore\Api\Data\CustomAttributeInterface $customAttribute
     * @return \Dealer4dealer\Xcore\Api\Data\CustomAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(
        \Dealer4dealer\Xcore\Api\Data\CustomAttributeInterface $customAttribute
    );

    /**
     * Retrieve CustomAttribute
     * @param string $customattributeId
     * @return \Dealer4dealer\Xcore\Api\Data\CustomAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($customattributeId);

    /**
     * Retrieve CustomAttribute matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Dealer4dealer\Xcore\Api\Data\CustomAttributeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CustomAttribute
     * @param \Dealer4dealer\Xcore\Api\Data\CustomAttributeInterface $customAttribute
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \Dealer4dealer\Xcore\Api\Data\CustomAttributeInterface $customAttribute
    );

    /**
     * Delete CustomAttribute by ID
     * @param string $customattributeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($customattributeId);
}