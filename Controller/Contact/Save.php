<?php
/**
 * Class ContactSave
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Controller\Contact;

use Magento\Framework\App\Action\Context;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Risecommerce\Gdpr\Model\ConsentHistoryFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Psr\Log\LoggerInterface;

class Save extends \Magento\Contact\Controller\Index\Post
{
    /**
     * @var ConsentHistoryFactory
     */
    protected $postFactory;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * Save constructor.
     * @param Context $context
     * @param ConfigInterface $contactsConfig
     * @param MailInterface $mail
     * @param DataPersistorInterface $dataPersistor
     * @param ConsentHistoryFactory $postFactory
     * @param RemoteAddress $remoteAddress
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        ConfigInterface $contactsConfig,
        MailInterface $mail,
        DataPersistorInterface $dataPersistor,
        ConsentHistoryFactory $postFactory,
        RemoteAddress $remoteAddress,
        LoggerInterface $logger = null
    ) {
        parent::__construct(
            $context,
            $contactsConfig,
            $mail,
            $dataPersistor,
            $logger
        );
        $this->postFactory = $postFactory;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @return Redirect
     * @throws \Exception
     */
    public function execute()
    {
        $ipAddress = $this->remoteAddress->getRemoteAddress();
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } else {
            $input = $this->getRequest()->getPostValue();
            $post = $this->postFactory->create();
            if (isset($input['risecommerce_consent_checkbox'])==1) {
                $input["ip_address"] = $ipAddress;
                $input["action"] = $input["gdpr_action"];
                $post->setData($input)->save();
            }
        }

        return parent::execute();
    }
}
