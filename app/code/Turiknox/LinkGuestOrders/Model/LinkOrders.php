<?php
/*
 * Turiknox_LinkGuestOrders
 * @category   Turiknox
 * @package    Turiknox_LinkGuestOrders
 * @copyright  Copyright (c) 2018 Turiknox
 * @license    https://github.com/Turiknox/magento2-linkguestorders/blob/master/LICENSE.md
 * @version    1.0.0
 */
namespace Turiknox\LinkGuestOrders\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class LinkOrders
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteria;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * LinkOrders constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteria
     * @param ManagerInterface $messageManager
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteria,
        ManagerInterface $messageManager,
        OrderRepositoryInterface $orderRepository,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteria = $searchCriteria;
        $this->messageManager = $messageManager;
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Get newly registered customers
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    public function getNewCustomers()
    {
        // Get registered customers within the last 24h
        $date = new \DateTime();
        $startDate = $date->modify('-1 day');
        $createdAtFilter = $this->filterBuilder->setField('created_at')
            ->setConditionType('gteq')
            ->setValue($startDate)
            ->create();

        $searchCriteria = $this->searchCriteria
            ->addFilter($createdAtFilter)
            ->create();

        try {
            $collection = $this->customerRepository->getList($searchCriteria);
            return $collection->getItems();
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        }
    }

    /**
     * Assign orders to newly registered customers
     */
    public function assignOrders()
    {
        $newCustomers = $this->getNewCustomers();
        foreach ($newCustomers as $customer) {
            $orderCollection = $this->orderCollectionFactory->create()
                ->addAttributeToFilter('customer_email', $customer->getEmail())
                ->addAttributeToFilter('customer_is_guest', 1);

            foreach ($orderCollection as $order) {
                $order->setCustomerId($customer->getId());
                $order->setCustomerFirstname($customer->getFirstname());
                $order->setCustomerLastname($customer->getLastname());
                $order->setCustomerGroupId($customer->getGroupId());
                $order->setCustomerIsGuest(0);
                $this->orderRepository->save($order);
            }
        }
    }
}
