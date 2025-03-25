<?php declare(strict_types = 1);

namespace DevTeam\CustomBadges\Plugin;

use DevTeam\CustomBadges\ViewModel\Product\CustomBadge;
use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\Data\Collection;

class GetProductData
{
    // Properties
    private readonly CustomBadge $customBadgeViewModel;

    // Methods
    /**
     * Summary of __construct
     * 
     * @param \DevTeam\CustomBadges\ViewModel\Product\CustomBadge $customBadgeViewModel
     */
    public function __construct(
        CustomBadge $customBadgeViewModel,
    ) {
        $this->customBadgeViewModel = $customBadgeViewModel;
    }

    /**
     * Method to get the product ID
     * 
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param mixed $result
     * @return \Magento\Framework\Data\Collection
     */
    public function afterGetGalleryImages(
        Gallery $subject, 
        $result
    ): Collection {
        $productID = $subject->getProduct()->getId();
        $this->customBadgeViewModel->setProductID((int)$productID);
        
        return $result;
    }
}
