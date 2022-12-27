<?php
/**
 * Class DeleteAccountActions
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
class DeleteAccountActions extends Column
{
    /**
     * Url path
     */
    private const BLOG_URL_PATH_EDIT = 'gdpr/history/edit';

    /**
     * UrlBuilder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Url for Edit deleteaccount request
     *
     * @var string
     */
    protected $editUrl;

    /**
     * DeleteAccountActions constructor.
     *
     * @param ContextInterface   $context            context
     * @param UiComponentFactory $uiComponentFactory uiComponentFactory
     * @param UrlInterface       $urlBuilder         urlBuilder
     * @param array              $components         components
     * @param array              $data               data
     * @param string             $editUrl            editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::BLOG_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        $this->editUrl,
                        ['account_id' => $item['account_id']]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
            }
        }
        return $dataSource;
    }
}
