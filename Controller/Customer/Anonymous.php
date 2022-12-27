<?php
/**
 * Class Anonymous
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

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Risecommerce\Gdpr\Model\ResourceModel\AnonymousAccount\CollectionFactory as AnonymousAccount;
use Risecommerce\Gdpr\Model\AnonymousAccountFactory;
use Risecommerce\Gdpr\Model\ResourceModel\AnonymousAccount as AnonymousResource;
use Magento\Framework\Math\Random;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerFactory;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as AddressFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderFactory;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory as QuoteAddressFactory;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Risecommerce\Gdpr\Helper\Data;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;


class Anonymous extends \Magento\Framework\App\Action\Action
{
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
     * AnonymousAccountCollection
     *
     * @var AnonymousAccount
     */
    protected $anonymousaccount;

    /**
     * AnonymousAccountModel
     *
     * @var AnonymousAccountFactory
     */
    protected $anonymousfactory;

    /**
     * AnonymousAccountResourceModel
     *
     * @var AnonymousResource
     */
    protected $anonymousResource;

    /**
     * Random
     *
     * @var Random
     */
    protected $random;

    /**
     * OrderAddressFactory
     *
     * @var CollectionFactory
     */
    protected $orderAddressFactory;

    /**
     * CustomerFactory
     *
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * CustomerAddressFactory
     *
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * OrderFactory
     *
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * QuoteFactory
     *
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * QuoteAddressFactory
     *
     * @var QuoteAddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * SubscriberFactory
     *
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * ScopeConfig
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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
     * ContactConfig
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
     * StoreManager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Anonymous constructor.
     *
     * @param SessionFactory             $customerSession     customerSession
     * @param EncryptorInterface         $encryptor           encryptor
     * @param AnonymousAccount           $anonymousaccount    anonymousaccount
     * @param AnonymousAccountFactory    $anonymousfactory    anonymousfactory
     * @param AnonymousResource          $anonymousResource   anonymousResource
     * @param Random                     $random              random
     * @param CollectionFactory          $orderAddressFactory orderAddressFactory
     * @param CustomerFactory            $customerFactory     customerFactory
     * @param AddressFactory             $addressFactory      addressFactory
     * @param OrderFactory               $orderFactory        orderFactory
     * @param QuoteFactory               $quoteFactory        quoteFactory
     * @param QuoteAddressFactory        $quoteAddressFactory quoteAddressFactory
     * @param SubscriberFactory          $subscriberFactory   subscriberFactory
     * @param ScopeConfigInterface       $scopeConfig         scopeConfig
     * @param Data                       $helper              helper
     * @param StateInterface             $inlineTranslation   inlineTranslation
     * @param TransportBuilder           $transportBuilder    transportBuilder
     * @param ConfigInterface            $contactsConfig      contactsConfig
     * @param ResultFactory              $resultFactory       resultFactory
     * @param Context                    $context             context
     * @param StoreManagerInterface|null $storeManager        storeManager
     */
    public function __construct(
        SessionFactory $customerSession,
        EncryptorInterface $encryptor,
        AnonymousAccount $anonymousaccount,
        AnonymousAccountFactory $anonymousfactory,
        AnonymousResource $anonymousResource,
        Random $random,
        CollectionFactory $orderAddressFactory,
        CustomerFactory $customerFactory,
        AddressFactory $addressFactory,
        OrderFactory $orderFactory,
        QuoteFactory $quoteFactory,
        QuoteAddressFactory $quoteAddressFactory,
        SubscriberFactory $subscriberFactory,
        ScopeConfigInterface $scopeConfig,
        Data $helper,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ConfigInterface $contactsConfig,
        ResultFactory $resultFactory,
        Context $context,
        StoreManagerInterface $storeManager = null
    ) {
        $this->customerSession = $customerSession->create();
        $this->encryptor = $encryptor;
        $this->anonymousaccount = $anonymousaccount;
        $this->anonymousfactory = $anonymousfactory;
        $this->anonymousResource = $anonymousResource;
        $this->random = $random;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->orderFactory = $orderFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->contactsConfig = $contactsConfig;
        $this->resultFactory = $resultFactory;
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
        $passwordValid = $this->encryptor->validateHash(
            $input['current_password_anonymous'],
            $customerPassword
        );

        if ($passwordValid) {
            $orderStatus = $this->helper->getCustomerConfig('anonymous_order_status');
            $orderStatus = explode(',', $orderStatus);
            foreach ($orderStatus as $orderStatusKey => $orderStatusValue) {
                if (empty($orderStatusValue)) {
                    unset($orderStatus[$orderStatusKey]);
                }
            }

            $orderCollection = $this->orderFactory->create();
            $orderData = $orderCollection
                ->addAttributeToSelect('entity_id')
                ->addAttributeToFilter('customer_id', ['eq' => $customerId])
                ->addFieldToFilter('status', ['nin' => $orderStatus]);
            if (count($orderData) == 0) {
                $anonymousCollection = $this->anonymousaccount->create()
                    ->addFieldToFilter('customer_id', $customerId);
                if (empty($anonymousCollection->getData())) {
                    $stringLength = random_int(8, 9);
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$%&';
                    $charactersLength = strlen($characters);
                    $password = '';
                    for ($i = 0; $i < $stringLength; $i++) {
                        $password .= $characters[rand(0, $charactersLength - 1)];
                    }

                    $newCustomerData = $this->setCustomerData($customerId, $password);

                    foreach ($newCustomerData as $data) {
                        $newCustomerEmail = $data->getData('email');
                        $newCustomerPassword = $password;
                    }

                    $newCustomerAddressData = $this->setCustomerAddressData($newCustomerData, $customerId);
                    $this->setSalesOrderData($newCustomerData, $customerId, $newCustomerEmail, $newCustomerAddressData);
                    $this->setQuoteData($newCustomerData, $customerId);
                    $this->setQuoteAddressData($newCustomerAddressData, $customerId, $newCustomerEmail);
                    $this->setNewsletterData($newCustomerData, $customerId);

                    $input['email'] = $customerEmail;
                    $input['customer_id'] = $customerId;
                    $input['is_anonymous'] = 1;
                    $anonymous = $this->anonymousfactory->create();
                    $anonymous->setData($input);
                    $this->anonymousResource->save($anonymous);
                    try {
                        $adminEmail = $this->scopeConfig
                            ->getValue(
                                'trans_email/ident_general/email',
                                ScopeInterface::SCOPE_STORE
                            );
                        $variables = [
                            'email' => $newCustomerEmail,
                            'password' => $newCustomerPassword,
                            'name' => $customerName
                        ];
                        $template = $this->helper->getCustomerConfig('anonymised_account_details_email_template');
                        $replyToName = !empty($variables['name']) ? $variables['name'] : null;
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
                                ->addTo($customerEmail)
                                ->setReplyTo($adminEmail, $replyToName)
                                ->getTransport();
                            $transport->sendMessage();
                        } finally {
                            $this->inlineTranslation->resume();
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }

                    $this->customerSession->logout();
                    $response = $this->resultFactory
                        ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                        ->setData(
                            [
                            'status'  => 1,
                            'message' => __("Account is successfully anonymised.")
                            ]
                        );
                    return $response;
                } else {
                    $response = $this->resultFactory
                        ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                        ->setData(
                            [
                            'status'  => 0,
                            'message' => __('Your account is already anonymised.')
                            ]
                        );
                    return $response;
                }
            } else {
                $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                ->setData(
                    [
                    'status'  => 0,
                    'message' => __('Your account can not be anonymised at this time as you have some incomplete orders. Please contact administrator for further details.')
                    ]
                );
                return $response;
            }

        } else {
            $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                ->setData(
                    [
                    'status'  => 0,
                    'message' => __('Password is not matched.')
                    ]
                );
            return $response;
        }
    }

    /**
     * SetCustomerData
     *
     * @param int    $customerId customerId
     * @param string $password   password
     *
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setCustomerData($customerId, $password)
    {
        $oldCustomerData = $this->customerFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['eq' => $customerId]);

        foreach ($oldCustomerData as $data) {
            $tlds = ["com", "net", "gov", "org", "edu", "biz", "info"];
            $stringLength = random_int(1, 9);
            $data->setData('firstname', $this->random->getRandomString($stringLength));
            $stringLength = random_int(1, 9);
            $data->setData('lastname', $this->random->getRandomString($stringLength));
            if ($data->getData('middlename')) {
                $stringLength = random_int(1, 9);
                $data->setData('middlename', $this->random->getRandomString($stringLength));
            }
            $stringLength = random_int(1, 9);
            $tempemail = $this->random->getRandomString($stringLength);
            $tempemail .= "@";
            $tempemail .= $this->random->getRandomString(5);
            $tempemail .= ".";
            $tempemail .= $tlds[random_int(0, (count($tlds) - 1))];
            $data->setData('email', $tempemail);
            $hashPassword = $this->encryptor->getHash($password);
            $data->setData('password_hash', $hashPassword);
        }
        $oldCustomerData->save();
        return $oldCustomerData;
    }

    /**
     * SetCustomerAddressData
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $newCustomerData newCustomerData
     * @param int                                                       $customerId      customerId
     *
     * @return \Magento\Customer\Model\ResourceModel\Address\Collection
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setCustomerAddressData($newCustomerData, $customerId)
    {
        $customerAddress = $this->addressFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('parent_id', ['eq' => $customerId]);
        $telephone = '';
        for ($i = 0; $i < 10; $i++) {
            $telephone .= random_int(0, 9);
        }
        foreach ($newCustomerData as $customerdata) {
            $customerAddress->setDataToAll('firstname', $customerdata->getData('firstname'));
            $customerAddress->setDataToAll('lastname', $customerdata->getData('lastname'));
            if ($customerdata->getData('middlename')) {
                $customerAddress->setDataToAll('middlename', $customerdata->getData('middlename'));
            }
            $customerAddress->setDataToAll('telephone', $telephone);
            $stringLength = random_int(1, 9);
            $customerAddress->setDataToAll('city', $this->random->getRandomString($stringLength));
            $stringLength = random_int(1, 9);
            $customerAddress->setDataToAll('street', $this->random->getRandomString($stringLength));
            $customerAddress->save();
        }
        return $customerAddress;
    }

    /**
     * Set Orderdata
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $newCustomerData        newCustomerData
     * @param int                                                       $customerId             customerId
     * @param string                                                    $newCustomerEmail       newCustomerEmail
     * @param \Magento\Customer\Model\ResourceModel\Address\Collection  $newCustomerAddressData newCustomerAddressData
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function setSalesOrderData($newCustomerData, $customerId, $newCustomerEmail, $newCustomerAddressData)
    {
        $orderStatus = $this->helper->getCustomerConfig('anonymous_order_status');
        $orderStatus = explode(',', $orderStatus);

        foreach ($orderStatus as $orderStatusKey => $orderStatusValue) {
            if (empty($orderStatusValue)) {
                unset($orderStatus[$orderStatusKey]);
            }
        }

        $salesOrder = $this->orderFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', ['in' => $orderStatus]);

        foreach ($newCustomerData as $customerdata) {
            $customer_fullname = $customerdata->getData('firstname') . ' ' . $customerdata->getData('lastname');
            foreach ($salesOrder as $order) {
                $orderAddress = $this->orderAddressFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter(
                        'parent_id',
                        ['eq' => $order->getEntityId()]
                    );

                foreach ($newCustomerAddressData as $customerdata) {
                    $orderAddress->setDataToAll('firstname', $customerdata->getData('firstname'));
                    $orderAddress->setDataToAll('lastname', $customerdata->getData('lastname'));
                    if ($customerdata->getData('middlename')) {
                        $orderAddress->setDataToAll('middlename', $customerdata->getData('middlename'));
                    }
                    $orderAddress->setDataToAll('telephone', $customerdata->getData('telephone'));
                    $orderAddress->setDataToAll('city', $customerdata->getData('city'));
                    $orderAddress->setDataToAll('street', $customerdata->getData('street'));
                    $orderAddress->setDataToAll('email', $newCustomerEmail);
                    $orderAddress->save();
                }

                $order->setData('customer_firstname', $customerdata->getData('firstname'));
                $order->setData('customer_lastname', $customerdata->getData('lastname'));
                if ($customerdata->getData('middlename')) {
                    $order->setDataToAll('customer_middlename', $customerdata->getData('middlename'));
                }
                $order->setData('customer_email', $newCustomerEmail);
                $order->setData('customer_name', $customer_fullname);
                $order->setData('remote_ip', '127.0.0.1');
                $order->save();

                $invoiceCollection = $order->getInvoiceCollection();
                if (count($invoiceCollection)) {
                    $invoiceCollection->setDataToAll('customer_name', $customer_fullname);
                    $invoiceCollection->setDataToAll('billing_name', $customer_fullname);
                    $invoiceCollection->save();
                }

                $shipments = $order->getShipmentsCollection();
                if (count($shipments)) {
                    $shipments->setDataToAll('customer_name', $customer_fullname);
                    $shipments->setDataToAll('billing_name', $customer_fullname);
                    $shipments->setDataToAll('shipping_name', $customer_fullname);
                    $shipments->save();
                }

                $creditmemos = $order->getCreditmemosCollection();
                if (count($creditmemos)) {
                    $creditmemos->setDataToAll('customer_name', $customer_fullname);
                    $creditmemos->setDataToAll('billing_name', $customer_fullname);
                    $creditmemos->save();
                }
            }
        }
        return $salesOrder;
    }

    /**
     * Set QuoteData
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $newCustomerData newCustomerData
     * @param inr                                                       $customerId      customerId
     *
     * @return \Magento\Quote\Model\ResourceModel\Quote\Collection
     */
    public function setQuoteData($newCustomerData, $customerId)
    {
        $quote = $this->quoteFactory->create()
            ->addFieldToFilter('customer_id', $customerId);

        foreach ($newCustomerData as $customerdata) {
            $quote->setDataToAll('customer_firstname', $customerdata->getData('firstname'));
            $quote->setDataToAll('customer_lastname', $customerdata->getData('lastname'));
            if ($customerdata->getData('middlename')) {
                $quote->setDataToAll('customer_middlename', $customerdata->getData('middlename'));
            }
            $quote->setDataToAll('customer_email', $customerdata->getData('email'));
            $quote->save();
        }
        return $quote;
    }

    /**
     * Set QuoteAddressData
     *
     * @param \Magento\Customer\Model\ResourceModel\Address\Collection $newCustomerAddressData newCustomerAddressData
     * @param int                                                      $customerId             customerId
     * @param string                                                   $newCustomerEmail       newCustomerEmail
     *
     * @return \Magento\Quote\Model\ResourceModel\Quote\Address\Collection
     */
    public function setQuoteAddressData($newCustomerAddressData, $customerId, $newCustomerEmail)
    {
        $quoteAddress = $this->quoteAddressFactory->create()
            ->addFieldToFilter('customer_id', $customerId);

        foreach ($newCustomerAddressData as $customerdata) {
            $quoteAddress->setDataToAll('firstname', $customerdata->getData('firstname'));
            $quoteAddress->setDataToAll('lastname', $customerdata->getData('lastname'));
            if ($customerdata->getData('middlename')) {
                $quoteAddress->setDataToAll('middlename', $customerdata->getData('middlename'));
            }
            $quoteAddress->setDataToAll('telephone', $customerdata->getData('telephone'));
            $quoteAddress->setDataToAll('city', $customerdata->getData('city'));
            $quoteAddress->setDataToAll('street', $customerdata->getData('street'));
        }
        $quoteAddress->setDataToAll('email', $newCustomerEmail);
        $quoteAddress->save();
        return $quoteAddress;
    }

    /**
     * Set NewsletterData
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $newCustomerData newCustomerData
     * @param int                                                       $customerId      customerId
     *
     * @return \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection
     */
    public function setNewsletterData($newCustomerData, $customerId)
    {
        $subscribers = $this->subscriberFactory->create()
            ->addFieldToFilter('customer_id', $customerId);

        foreach ($newCustomerData as $customerData) {
            $subscribers->setDataToAll('subscriber_email', $customerData->getData('email'));
            $subscribers->save();
        }
        return $subscribers;
    }
}
