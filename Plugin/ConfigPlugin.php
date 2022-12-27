<?php
/**
 * Class ConfigPlugin
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Config\Model\Config;


class ConfigPlugin
{
    /**
     * Request
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * ConfigWriter
     *
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * ConfigPlugin constructor.
     *
     * @param RequestInterface $request      request
     * @param WriterInterface  $configWriter configWriter
     */
    public function __construct(
        RequestInterface $request,
        WriterInterface $configWriter
    ) {
        $this->request = $request;
        $this->configWriter = $configWriter;
    }

    /**
     * Before save of config method
     *
     * @param Config $subject subject
     *
     * @return void
     */
    public function beforeSave(
        Config $subject
    ) {
        $cookieParams = $this->request->getParam('groups');
        if (isset($cookieParams['cookie']['fields']['cookie_restriction']['value'])) {
            $value = $cookieParams['cookie']['fields']['cookie_restriction']['value'];
            $this->configWriter->save('web/cookie/cookie_restriction', $value);
            $this->configWriter->save('gdpr/cookie/enabled', $value);
        }
        if (isset($cookieParams['cookie']['fields']['enabled']['value'])) {
            $value = $cookieParams['cookie']['fields']['enabled']['value'];
            $this->configWriter->save('web/cookie/cookie_restriction', $value);
            $this->configWriter->save('gdpr/cookie/enabled', $value);
        }
    }
}
