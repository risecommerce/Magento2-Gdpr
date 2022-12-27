<?php
/**
 * Class Data
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Path to configuration
     */
    public const CONFIG_MODULE_PATH = 'gdpr/';

    /**
     * Get system configuration value
     *
     * @param string   $field   field
     * @param int|null $storeId storeId
     *
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get system configuration value of customer policy
     *
     * @param string   $code    code
     * @param int|null $storeId storeId
     *
     * @return mixed
     */
    public function getCustomerConfig($code, $storeId = null)
    {
        return $this->getConfigValue(
            self::CONFIG_MODULE_PATH .'customer/'. $code,
            $storeId
        );
    }

    /**
     * Get system configuration value of cookie policy
     *
     * @param string   $code    code
     * @param int|null $storeId storeId
     *
     * @return mixed
     */
    public function getCookieConfig($code, $storeId = null)
    {
        return $this->getConfigValue(
            self::CONFIG_MODULE_PATH .'cookie/'. $code,
            $storeId
        );
    }

    /**
     * Get system configuration value of consent checkbox
     *
     * @param string   $code    code
     * @param int|null $storeId storeId
     *
     * @return mixed
     */
    public function getConsentConfig($code, $storeId = null)
    {
        return $this->getConfigValue(
            self::CONFIG_MODULE_PATH .'consent/'. $code,
            $storeId
        );
    }
}
