<?php
/**
 * Class DeleteAccount
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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class DeleteAccount extends \Magento\Backend\App\Action
{
    /**
     * PageFactory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context     $context           context
     * @param PageFactory $resultPageFactory resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        /**
         * ResultPage
         *
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Risecommerce_Gdpr::risecommerce_gdpr_delete_account_history');
        $resultPage->getConfig()->getTitle()->prepend(__('Delete Account History'));

        return $resultPage;
    }
}
