<?php
/**
 * Class Save
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Controller\Adminhtml\History;

use Magento\Backend\App\Action;
use Risecommerce\Gdpr\Model\ResourceModel\DeleteAccount;
use Risecommerce\Gdpr\Model\DeleteAccountFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Contact\Model\ConfigInterface;
use Risecommerce\Gdpr\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderFactory;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory as TaxFactory;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberFactory;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;


class Save extends Action
{
    /**
     * DeleteAccountResource
     *
     * @var DeleteAccount
     */
    protected $deleteaccountResource;

    /**
     * DeleteAccountFactory
     *
     * @var DeleteAccountFactory
     */
    protected $deleteaccountFactory;

    /**
     * CustomerRepository
     *
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * CustomerSession
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ScopeConfig
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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
     * GdprHelper
     *
     * @var Data
     */
    protected $helper;

    /**
     * OrderFactory
     *
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * OrderTaxFactory
     *
     * @var TaxFactory
     */
    protected $orderTaxFactory;

    /**
     * SubscriberFactory
     *
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * QuoteFactory
     *
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * DataPersistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * StoreManager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param DeleteAccount $deleteaccountResource
     * @param DeleteAccountFactory $deleteaccountFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $customerSession
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ConfigInterface $contactsConfig
     * @param Data $helper
     * @param OrderFactory $orderFactory
     * @param TaxFactory $orderTaxFactory
     * @param SubscriberFactory $subscriberFactory
     * @param QuoteFactory $quoteFactory
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface|null $storeManager
     */
    public function __construct(
        Action\Context $context,
        DeleteAccount $deleteaccountResource,
        DeleteAccountFactory $deleteaccountFactory,
        CustomerRepositoryInterface $customerRepository,
        Session $customerSession,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ConfigInterface $contactsConfig,
        Data $helper,
        OrderFactory $orderFactory,
        TaxFactory $orderTaxFactory,
        SubscriberFactory $subscriberFactory,
        QuoteFactory $quoteFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager = null
    ) {
        $this->deleteaccountResource = $deleteaccountResource;
        $this->deleteaccountFactory = $deleteaccountFactory;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->contactsConfig = $contactsConfig;
        $this->helper = $helper;
        $this->orderFactory = $orderFactory;
        $this->orderTaxFactory = $orderTaxFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->quoteFactory = $quoteFactory;
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data['status'] == "Accepted") {
            $this->accepted($data['account_id']);
        } elseif ($data['status'] == "Rejected") {
            $this->rejeted($data['account_id']);
        }
        $resultRedirect->setPath('*/*/deleteaccount');
        return $resultRedirect;
    }

    /**
     * Accept Customer Delete Account Request
     *
     * @param $id
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function accepted($id)
    {
        $data = $this->getRequest()->getPostValue();
        $model = $this->deleteaccountFactory->create();
        $this->deleteaccountResource->load($model, $id);

        $customerId = $model->getData('customer_id');
        $deletecustomerData = $this->customerRepository->getById($customerId);
        if (!$id || empty($deletecustomerData)) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                __('This Customer is no longer exist.')
            );
            $resultRedirect->setPath('*/*/deleteaccount');
            return $resultRedirect;
        }

        try {
            $deleteInfo = $this->helper->getCustomerConfig('customer_account_delete_also_delete');
            $deleteInfo = explode(',', $deleteInfo);
            foreach ($deleteInfo as $deleteInfoKey => $deleteInfoValue) {
                if (empty($deleteInfoValue)) {
                    unset($deleteInfo[$deleteInfoKey]);
                }
            }

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
                $this->messageManager->addErrorMessage(
                    __('Account deletion request can not be accepted at this time as customer have some incomplete orders.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                return($resultRedirect->setPath('gdpr/history/deleteaccount'));
            }

            if (!empty($deleteInfo[0])) {
                foreach ($deleteInfo as $deleteElement) {
                    if ($deleteElement == "newsletter") {
                        $newsletterId = $this->deleteNewsletterData($customerId);
                        if (!empty($newsletterId)) {
                            $this->messageManager->addSuccessMessage(
                                __('Newsletter data has been deleted successfully.')
                            );
                        }
                    } elseif ($deleteElement == "order") {
                        $this->deleteOrderData($customerId);
                        $this->messageManager->addSuccessMessage(
                            __('Order, Invoice, Credit Memos and Shipment data has been deleted successfully.')
                        );
                    }
                }
            }

            $this->deleteQuoteData($customerId);
            $this->customerSession->logout();
            $this->customerRepository->deleteById($customerId);
            $adminEmail = $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE
            );
            $mailData = $data;
            $mailData['subject'] = 'Your account is deleted';
            $mailData = new DataObject($mailData);
            $this->send(
                $adminEmail,
                ['data' => $mailData->getData()]
            );
            $model->setData($data);
            $this->deleteaccountResource->save($model);
            $this->dataPersistor->clear('gdpr_data');
            $this->messageManager->addSuccessMessage(
                __('Account has been deleted successfully.')
            );
        } catch (\Exception $e) {
            $this->dataPersistor->set('gdpr_data', $data);
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(
                __('Something went wrong while deleting your account.')
            );
        }
    }

    /**
     * Reject Customer Delete Account Request
     *
     * @param $id
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function rejeted($id)
    {
        $data = $this->getRequest()->getPostValue();

        if (trim($data['admin_reason']) == null || trim($data['admin_reason']) == '') {
            $this->messageManager->addErrorMessage(
                __('Please specify reason to reject this request to submit to customer.')
            );
            $resultRedirect = $this->resultRedirectFactory->create();
            return($resultRedirect->setPath('gdpr/history/deleteaccount'));
        }

        $deleteaccount = $this->deleteaccountFactory->create();
        $this->deleteaccountResource->load($deleteaccount, $id);
        if (!$id || $deleteaccount->getData('status') == "Accepted") {
            $this->messageManager->addErrorMessage(
                __('Customer is no longer exits.')
            );
            $resultRedirect = $this->resultRedirectFactory->create();
            return($resultRedirect->setPath('gdpr/history/deleteaccount'));
        }

        try {
            $adminEmail = $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE
            );
            $mailData = $data;
            $mailData['subject'] = 'Your account deletion request is rejected';
            $mailData = new DataObject($mailData);
            $this->send(
                $adminEmail,
                ['data' => $mailData->getData()]
            );

            $deleteaccount->setData($data);
            $this->deleteaccountResource->save($deleteaccount);
            $this->dataPersistor->clear('gdpr_data');
            $this->messageManager->addSuccessMessage(
                __('Delete account request is rejected and email is sent to customer.')
            );

        } catch (\Exception $e) {
            $this->dataPersistor->set('gdpr_data', $data);
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * Delete Order Data
     *
     * @param $customerId
     * @return array
     * @throws \Exception
     */
    public function deleteOrderData($customerId)
    {
        try {
            $orderCollection = $this->orderFactory->create();

            $orderStatus = $this->helper->getCustomerConfig('order_status');
            $orderStatus = explode(',', $orderStatus);
            foreach ($orderStatus as $orderStatusKey => $orderStatusValue) {
                if (empty($orderStatusValue)) {
                    unset($orderStatus[$orderStatusKey]);
                }
            }
            $orderData = $orderCollection
                ->addAttributeToSelect('entity_id')
                ->addAttributeToFilter('customer_id', ['eq' => $customerId])
                ->addFieldToFilter('status', ['in' => $orderStatus]);
            $data = [];
            foreach ($orderData as $order) {
                $data[] = $order->getEntityId();

                $invoices = $order->getInvoiceCollection();
                if (count($invoices)) {
                    foreach ($invoices as $invoice) {
                        $invoice->delete();
                    }
                }

                $shipments = $order->getShipmentsCollection();
                if (count($shipments)) {
                    foreach ($shipments as $shipment) {
                        $shipment->delete();
                    }
                }

                $creditmemos = $order->getCreditmemosCollection();
                if (count($creditmemos)) {
                    foreach ($creditmemos as $creditmemo) {
                        $creditmemo->delete();
                    }
                }

                $taxFactory = $this->orderTaxFactory->create();
                $taxes = $taxFactory->loadByOrder($order);
                if (count($taxes)) {
                    foreach ($taxes as $tax) {
                        $tax->delete();
                    }
                }
                $order->delete();
            }
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete Newsletter Data
     *
     * @param $customerId
     * @return array
     * @throws \Exception
     */
    public function deleteNewsletterData($customerId)
    {
        try {
            $newsletterCollection = $this->subscriberFactory->create();
            $newsletterData = $newsletterCollection
                ->addFieldToSelect('subscriber_id')
                ->addFieldToFilter('customer_id', ['eq' => $customerId]);
            $data = [];
            foreach ($newsletterData as $newsletter) {
                $newsletter->delete();
                $data[] = $newsletter->getSubscriberId();
            }
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete Quote Data
     *
     * @param $customerId
     * @return array
     * @throws \Exception
     */
    public function deleteQuoteData($customerId)
    {
        try {
            $quoteCollection = $this->quoteFactory->create()
                ->addFieldToFilter('customer_id', $customerId);
            $data = [];
            foreach ($quoteCollection as $quote) {
                $quote->delete();
                $data[] = $quote->getEntityId();
            }
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send Account deletion Mail
     *
     * @param $replyTo
     * @param array $variables
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function send($replyTo, array $variables)
    {
        $template = $this->helper->getCustomerConfig('account_deletion_response_email_template');
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
                ->addTo($variables['data']['email'])
                ->setReplyTo($replyTo, $replyToName)
                ->getTransport();
            $transport->sendMessage();
        } finally {
            $this->inlineTranslation->resume();
        }
    }
}
