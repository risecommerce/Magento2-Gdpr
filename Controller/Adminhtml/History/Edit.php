<?php
/**
 * Class Edit
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
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Risecommerce\Gdpr\Model\DeleteAccountFactory;
use Risecommerce\Gdpr\Model\ResourceModel\DeleteAccount;
use Magento\Backend\Model\Session;

class Edit extends Action
{
    /**
     * ResultPageFactory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * DeleteAccountFactory
     *
     * @var DeleteAccountFactory
     */
    protected $deleteAccountFactory;

    /**
     * DeleteAccountResource
     *
     * @var DeleteAccount
     */
    protected $deleteAccountResource;

    /**
     * BackendSession
     *
     * @var Session
     */
    protected $backendSession;

    /**
     * Edit constructor.
     *
     * @param Action\Context       $context               context
     * @param PageFactory          $resultPageFactory     resultPageFactory
     * @param DeleteAccountFactory $deleteAccountFactory  deleteAccountFactory
     * @param DeleteAccount        $deleteAccountResource deleteAccountResource
     * @param Session              $backendSession        backendSession
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        DeleteAccountFactory $deleteAccountFactory,
        DeleteAccount $deleteAccountResource,
        Session $backendSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->deleteAccountFactory = $deleteAccountFactory;
        $this->deleteAccountResource = $deleteAccountResource;
        $this->backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * Edit Delete Account
     *
     * @return                                  \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('account_id');
        $model = $this->deleteAccountFactory->create();

        /**
         * ResultRedirect
         *
         * \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $this->deleteAccountResource->load($model, $id);
            if (!$model->getId() || $model->getData('status') == 'Accepted') {
                $this->messageManager->addErrorMessage(
                    __('This customer no longer exists.')
                );
                return $resultRedirect->setPath('gdpr/history/deleteAccount');
            }
        } else {
            $this->messageManager->addErrorMessage(
                __('This customer no longer exists.')
            );
            return $resultRedirect->setPath('gdpr/history/deleteAccount');
        }

        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $resultPage = $this->initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('GDPR Delete Account History'));
        $resultPage->getConfig()->getTitle()->prepend(__('Delete Account Request From: ').$model->getName());
        return $resultPage;
    }

    /**
     * Init actions
     *
     * @return Page
     */
    protected function initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Risecommerce_Gdpr::deleteaccount')
            ->addBreadcrumb(__('Gdpr'), __('Gdpr'))
            ->addBreadcrumb(__('Manage Delete Account Requests'), __('Manage Delete Account Requests'));
        return $resultPage;
    }
}
