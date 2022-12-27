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
namespace Risecommerce\Gdpr\Ui\Component\DataProvider;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use \Risecommerce\Gdpr\Model\ResourceModel\DeleteAccount\CollectionFactory;

/**
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
class DeleteAccount extends AbstractDataProvider
{
    /**
     * DeleteAccountFactory
     *
     * @var \Risecommerce\Gdpr\Model\ResourceModel\DeleteAccount\CollectionFactory
     */
    protected $collection;

    /**
     * DataPersistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * LoadedData
     *
     * @var array
     */
    protected $loadedData;

    /**
     * DeleteAccount Constructor
     *
     * @param string                 $name                  name
     * @param string                 $primaryFieldName      primaryFieldName
     * @param string                 $requestFieldName      requestFieldName
     * @param CollectionFactory      $pageCollectionFactory DeleteAccountFactory
     * @param DataPersistorInterface $dataPersistor         dataPersistor
     * @param array                  $meta                  meta
     * @param array                  $data                  data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $pageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->meta = $this->prepareMeta($this->meta);
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    /**
     * Prepares Meta
     *
     * @param array $meta meta
     *
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = [];

        foreach ($items as $contact) {
            $this->loadedData[$contact->getData('account_id')] = $contact->getData();
        }
        $data = $this->dataPersistor->get('gdpr_data');
        if (!empty($data)) {
            $contact = $this->collection->getNewEmptyItem();
            $contact->setData($data);
            $this->loadedData[$contact->getData('account_id')] = $contact->getData();
            $this->dataPersistor->clear('gdpr_data');
        }
        return $this->loadedData;
    }
}
