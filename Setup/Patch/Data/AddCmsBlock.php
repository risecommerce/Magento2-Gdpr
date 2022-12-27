<?php
/**
 * Class AddCmsBlock
 *
 * PHP version 8
 *
 * @category Risecommerce
 * @package  Risecommerce_Gdpr
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\Gdpr\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Cms\Model\BlockFactory;


class AddCmsBlock implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * AddCmsBlock constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        BlockFactory $blockFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->blockFactory = $blockFactory;
    }

    /**
     * @return AddCmsBlock|void
     */
    public function apply()
    {
        $setup = $this->moduleDataSetup->getConnection()->startSetup();
        $cmsBlock = $this->blockFactory->create()
            ->load('cookie_notice', 'identifier');
        if (!$cmsBlock->getId()) {

            $cmsBlockData = [
                'title' => 'Cookie Notice',
                'identifier' => 'cookie_notice',
                'content' => "<h3 style='text-align: center;'>Cookie</h3>
                            <p>&nbsp;</p>
                            <p style='text-align: left;'>
                            This website requires cookies to provide all of its features.
                            For more information on what data is contained in the cookies,
                            please see our <a href=\"{{store url='privacy-policy-cookie-restriction-mode'}}\">
                            Privacy Policy page</a>. To accept cookies from this site, please click the
                            Accept button below.</p>
                            <p style='text-align: left;'>&nbsp;</p>",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];

            $this->blockFactory->create()->setData($cmsBlockData)->save();
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
