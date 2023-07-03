<?php

declare(strict_types=1);

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Helper\DefaultCategoryFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManager;

/**
 * Adds a simple product to default category
 */
class AddSimpleProduct implements DataPatchInterface
{
    /**
     * @var ProductInterface
     */
    protected ProductInterface $product;

    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var StoreManager
     */
    protected StoreManager $storeManager;

    /**
     * @var CategoryLinkManagementInterface
     */
    protected CategoryLinkManagementInterface $categoryLinkManagement;

    /**
     * @var DefaultCategoryFactory
     */
    protected DefaultCategoryFactory $defaultCategoryFactory;

    /**
     * @var State
     */
    protected State $appState;

    /**
     * @param ProductInterface $product
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param StoreManager $storeManager
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param DefaultCategoryFactory $defaultCategoryFactory
     * @param State $appState
     */
    public function __construct(
        ProductInterface $product,
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        StoreManager $storeManager,
        CategoryLinkManagementInterface $categoryLinkManagement,
        DefaultCategoryFactory $defaultCategoryFactory,
        State $appState
    ) {
        $this->product = $product;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->defaultCategoryFactory = $defaultCategoryFactory;
        $this->appState = $appState;
    }

    /**
     * @throws NoSuchEntityException
     * @throws StateException
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     */
    public function apply(): void
    {
        $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        $product = $this->productFactory->create();
        $product->setSku('sample_sku')
            ->setName('sample_name')
            ->setPrice(56.78)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED)
            ->setAttributeSetId($product->getDefaultAttributeSetId());
        $this->productRepository->save($product);
        $this->categoryLinkManagement->assignProductToCategories(
            $product->getSku(),
            [$this->defaultCategoryFactory->create()->getId()]
        );
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}