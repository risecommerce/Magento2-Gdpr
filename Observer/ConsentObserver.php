<?php
/**
 * Class ConsentObserver
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Observer;

use Magento\Framework\Event\ObserverInterface;


class ConsentObserver implements ObserverInterface
{
    /**
     * @var \Risecommerce\Gdpr\Model\ConsentHistoryFactory
     */
    protected $postFactory;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * ConsentObserver constructor.
     * @param \Risecommerce\Gdpr\Model\ConsentHistoryFactory $postFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Risecommerce\Gdpr\Model\ConsentHistoryFactory $postFactory,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->postFactory = $postFactory;
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $ipAddress = $this->remoteAddress->getRemoteAddress();
        $input = $this->request->getPostValue();
        $post = $this->postFactory->create();
        if (isset($input['risecommerce_consent_checkbox'])==1) {
            if (isset($input['firstname'])) {
                $name = $input['firstname']." ".$input['lastname'];
                $input["name"] = trim($name);
            }
            $input["ip_address"] = $ipAddress;
            $post->setData($input)->save();
        }
    }
}
