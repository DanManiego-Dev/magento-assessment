<?php declare(strict_types = 1);

namespace DevTeam\CustomBadges\ViewModel\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Media\ConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomBadge implements ArgumentInterface
{
    // Properties
    CONST ATTRIBUTE_CODE = "custom_badge";

    // Methods
    /**
     * Summary of __construct
     * 
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Product\Media\ConfigInterface $configInterface
     * @param int|null $productID
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ConfigInterface $configInterface,
        private ?int $productID = null
    ) {}

    /**
     * Sets the product ID
     * 
     * @param int $ID
     * @return void
     */
    public function setProductID(int $ID): void {
        $this->productID = $ID;
    }
    
    /**
     * Method to get the custom badge of the product
     * 
     * @param int|null $productID
     * @return string|null
     */
    public function getCustomBadge(?int $productID): ?string {
        $productID = $productID ?? $this->productID;
        $product = $this->productRepository->getById($productID);
        $customBadge = $product->getData(self::ATTRIBUTE_CODE);

        // Checks if no custom badge is found
        if(empty($customBadge) || $customBadge === "no_selection") {
            return null;
        }

        // Returns the resolved image url
        return $this->configInterface->getMediaUrl($customBadge);
    }
}
