<?php
/**
 * Class DeleteMail
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */

namespace Risecommerce\Gdpr\Controller\Customer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Risecommerce\Gdpr\Model\DeleteAccountFactory;
use Risecommerce\Gdpr\Model\ResourceModel\DeleteAccount\CollectionFactory;
use Risecommerce\Gdpr\Model\ResourceModel\DeleteAccount as DeleteAccountResource;
use Risecommerce\Gdpr\Helper\Data;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderFactory;


class DeleteMail extends \Magento\Framework\App\Action\Action
{
    /**
     * ScopeConfig
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * CustomerSession
     *
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * Encryptor
     *
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * DeleteAccountCollectionFactory
     *
     * @var CollectionFactory
     */
    protected $deleteaccountfactory;

    /**
     * DeleteAccount Model
     *
     * @var DeleteAccount
     */
    protected $deleteaccountModel;

    /**
     * DeleteAccount ResourceModel
     *
     * @var DeleteAccountResource
     */
    protected $deleteAccountResource;

    /**
     * GdprHelper
     *
     * @var Data
     */
    protected $helper;

    /**
     * InlineTranslation
     *
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * TransportBuilder
     *
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * ContactsConfig
     *
     * @var ConfigInterface
     */
    protected $contactsConfig;

    /**
     * ResultFactory
     *
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * Order Collection
     *
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * StoreManager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DeleteMail constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param SessionFactory $customerSession
     * @param EncryptorInterface $encryptor
     * @param DeleteAccountFactory $deleteaccountModel
     * @param CollectionFactory $deleteaccountfactory
     * @param DeleteAccountResource $deleteAccountResource
     * @param Data $helper
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param Context $context
     * @param ConfigInterface $contactsConfig
     * @param ResultFactory $resultFactory
     * @param OrderFactory $orderFactory
     * @param StoreManagerInterface|null $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SessionFactory $customerSession,
        EncryptorInterface $encryptor,
        DeleteAccountFactory $deleteaccountModel,
        CollectionFactory $deleteaccountfactory,
        DeleteAccountResource $deleteAccountResource,
        Data $helper,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        Context $context,
        ConfigInterface $contactsConfig,
        ResultFactory $resultFactory,
        OrderFactory $orderFactory,
        StoreManagerInterface $storeManager = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession->create();
        $this->encryptor = $encryptor;
        $this->deleteaccountModel = $deleteaccountModel;
        $this->deleteaccountfactory = $deleteaccountfactory;
        $this->deleteAccountResource = $deleteAccountResource;
        $this->helper = $helper;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->contactsConfig = $contactsConfig;
        $this->resultFactory = $resultFactory;
        $this->orderFactory = $orderFactory;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
        return parent::__construct($context);
    }

    /**
     * Delete customer account action
     *
     * @return void
     */
    public function execute()
    {
        $customerPassword = $this->customerSession->getCustomer()->getPasswordHash();
        $customerEmail = $this->customerSession->getCustomer()->getEmail();
        $customerName = $this->customerSession->getCustomer()->getName();
        $customerId = $this->customerSession->getCustomer()->getId();

        $input = $this->getRequest()->getPostValue();
        $input['name'] = $customerName;
        $input['email'] = $customerEmail;
        $input['customer_id'] = $customerId;
        $input['status'] = "Pending";

        $passwordValid = $this->encryptor->validateHash(
            $input['current_password_delete_account'],
            $customerPassword
        );

        if ($passwordValid) {
            if (trim($input['customer_reason']) == null || trim($input['customer_reason']) == '') {
                $response = $this->resultFactory
                    ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                    ->setData([
                        'status'  => 0,
                        'message' => "Please specify reason to delete an account."
                    ]);
                return $response;
            } else {
                $orderStatus = $this->helper->getCustomerConfig('order_status');
                $orderStatus = explode(',', $orderStatus);
                foreach ($orderStatus as $orderStatusKey => $orderStatusValue) {
                    if (empty($orderStatusValue)) {
                        unset($orderStatus[$orderStatusKey]);
                    }
                }

                $orderCollection = $this->orderFactory->create();
                if (empty($orderStatus)) {
                    $orderData = $orderCollection
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToFilter('customer_id', ['eq' => $customerId]);
                } else {
                    $orderData = $orderCollection
                        ->addAttributeToSelect('entity_id')
                        ->addAttributeToFilter('customer_id', ['eq' => $customerId])
                        ->addFieldToFilter('status', ['nin' => $orderStatus]);
                }

                if (count($orderData) > 0) {
                    $response = $this->resultFactory
                        ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                        ->setData(
                            [
                            'status'  => 0,
                            'message' => __("Your account can not be deleted at this time as you have some incomplete orders. Please contact administrator for further details.")
                            ]
                        );
                    return $response;
                }

                $deleteaccountCollection = $this->deleteaccountfactory->create()
                    ->addFieldToFilter('customer_id', $input['customer_id'])
                    ->addFieldToFilter('status', 'Pending');

                if (count($deleteaccountCollection) > 0) {
                    $response = $this->resultFactory
                        ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                        ->setData([
                            'status'  => 0,
                            'message' => "Account deletion request is already sent. Admin will respond to you soon."
                        ]);
                    return $response;
                }
                try {
                    $variables = ['data' => new DataObject($input)];
                    $replyTo = $input['email'];
                    $adminEmail = $this->scopeConfig->getValue(
                        'trans_email/ident_general/email',
                        ScopeInterface::SCOPE_STORE
                    );
                    $template = $this->helper->getCustomerConfig('account_deletion_request_email_template');
                    $replyToName = !empty($variables['data']['name']) ? $variables['data']['name'] : null;
                    $this->inlineTranslation->suspend();
                    try {
                        $transport = $this->transportBuilder
                            ->setTemplateIdentifier($template)
                            ->setTemplateOptions(
                                [
                                    'area' => Area::AREA_FRONTEND,
                                    'store' => $this->storeManager->getStore()->getId()
                                ]
                            )
                            ->setTemplateVars($variables)
                            ->setFrom($this->contactsConfig->emailSender())
                            ->addTo($adminEmail)
                            ->setReplyTo($replyTo, $replyToName)
                            ->getTransport();
                        $transport->sendMessage();
                    } finally {
                        $this->inlineTranslation->resume();
                    }

                    $rejectedRequestCollection = $this->deleteaccountfactory->create()
                        ->addFieldToFilter('email', $input['email'])
                        ->addFieldToFilter('status', 'Rejected');

                    $deleteaccount = $this->deleteaccountModel->create();
                    $deleteaccount->setData($input);
                    $this->deleteAccountResource->save($deleteaccount);
                    if (count($rejectedRequestCollection) > 0) {
                        $response = $this->resultFactory
                            ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                            ->setData([
                                'status' => 1,
                                'message' => __("Your request is rejected before. New request is sent to the admin. Admin will respond to you soon.")
                            ]);
                    } else {
                        $response = $this->resultFactory
                            ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                            ->setData([
                                'status'  => 1,
                                'message' => __("Your request is sent to the admin. Admin will respond you soon.")
                            ]);
                    }
                    return $response;
                } catch (\Exception $e) {
                    $response = $this->resultFactory
                        ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                        ->setData(
                            [
                            'status'  => 0,
                            'message' => $e->getMessage()
                            ]
                        );
                    return $response;
                }
            }
        } else {
            $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                ->setData(
                    [
                    'status'  => 0,
                    'message' => __("Password is not matched.")
                    ]
                );
            return $response;
        }
    }
}
