<?php
/**
 * Class SaveButton
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'on_click' => sprintf("location.href = '%s';", $this->getSaveUrl()),
            'data_attribute' => [
                'mage-init' => ['button' => ['deleteaccount' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }

    /**
     * Return Url for onclick
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('gdpr/*/save');
    }
}
