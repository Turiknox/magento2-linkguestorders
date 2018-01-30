<?php
/*
 * Turiknox_LinkGuestOrders
 * @category   Turiknox
 * @package    Turiknox_LinkGuestOrders
 * @copyright  Copyright (c) 2018 Turiknox
 * @license    https://github.com/Turiknox/magento2-linkguestorders/blob/master/LICENSE.md
 * @version    1.0.0
 */
namespace Turiknox\LinkGuestOrders\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Turiknox\LinkGuestOrders\Model\LinkOrders as LinkOrdersModel;

class LinkOrders
{
    const XML_PATH_LINKORDERS_ENABLED = 'customer/linkorders/enable';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LinkOrdersModel
     */
    private $linkOrdersModel;

    /**
     * LinkOrders constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param LinkOrdersModel $linkOrders
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LinkOrdersModel $linkOrders
    ) {
        $this->scopeConfig        = $scopeConfig;
        $this->linkOrdersModel    = $linkOrders;
    }

    public function execute()
    {
        $isEnabled = $this->scopeConfig->isSetFlag(self::XML_PATH_LINKORDERS_ENABLED, ScopeInterface::SCOPE_STORE);
        if ($isEnabled) {
            $this->linkOrdersModel->assignOrders();
        }
    }
}
