<?php
/**
 * Class MassDelete
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
use Magento\Ui\Component\MassAction\Filter;
use Risecommerce\Gdpr\Model\ResourceModel\ConsentHistory\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;


class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * ConsentCollectionFactory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     *
     * @param Context           $context           context
     * @param Filter            $filter            filter
     * @param CollectionFactory $collectionFactory collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $item->delete();
        }

        $this->messageManager->addSuccessMessage(
            __(
                'A total of %1 record(s) have been deleted.',
                $collectionSize
            )
        );

        /**
         * ResultRedirect
         *
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
