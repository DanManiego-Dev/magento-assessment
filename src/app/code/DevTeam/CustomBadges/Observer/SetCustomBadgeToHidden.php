<?php declare(strict_types = 1);

namespace DevTeam\CustomBadges\Observer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Processor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class SetCustomBadgeToHidden implements ObserverInterface
{
    // Methods
    /**
     * Summary of __construct
     * 
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\Product\Gallery\Processor $mediaGalleryProcessor
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly Processor $mediaGalleryProcessor,
        private readonly ManagerInterface $messageManager,
    ) {}

    /**
     * Method for returning the custom badge ID
     * 
     * @param string $customBadgeImage
     * @param array $mediaGalleryImages
     * @return bool|int|string
     */
    private function getCustomBadgeID(string $customBadgeImage, array $mediaGalleryImages): int|bool|string
    {
        // Returns an array of the file names
        $customBadgeCheck = array_map(
            fn($item) => $item['file'] === $customBadgeImage, 
            $mediaGalleryImages
        );

        return array_search(true, $customBadgeCheck);
    }

    /**
     * Method for automatically hiding the custom badge on the frontend media gallery
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param string $mediaGalleryImageFile
     * @return void
     */
    private function hideCustomBadge(Product $product, string $mediaGalleryImageFile): ManagerInterface
    {
        $this->mediaGalleryProcessor->updateImage(
            $product, 
            $mediaGalleryImageFile, 
            [
                "disabled" => true,
            ]
        );

        return $this->messageManager->addSuccessMessage(__("Custom Badge has been saved."));
    }

    /**
     * Method for checking if the custom badge is assigned other roles
     * 
     * @param array $requestData
     * @param string $customBadgeImageFile
     * @return bool
     */
    private function imageRoleCheck(array $requestData, string $customBadgeImageFile): bool
    {
        $roleCheckArray = [
            $requestData['image'],
            $requestData['small_image'],
            $requestData['thumbnail'],
            $requestData['swatch_image']
        ];

        return in_array($customBadgeImageFile, $roleCheckArray);
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getData("product");
        $requestData = $this->request->getParam("product");
        $customBadgeImageFile = $requestData['custom_badge'];
        $mediaGalleryImages = $requestData["media_gallery"]["images"];
        $customBadgeID = $this->getCustomBadgeID($customBadgeImageFile, $mediaGalleryImages);
        $isCustomBadgeDisabled = (bool)$mediaGalleryImages[$customBadgeID]["disabled"];

        // Checks if the custom badge is given other roles
        if($this->imageRoleCheck($requestData, $customBadgeImageFile)) {
            throw new LocalizedException(__("The custom badge must not have other roles. Please try assigning the custom badge again."));
        }

        // Checks if a custom badge is already assigned and if it is already disabled
        if(!empty($customBadgeID) && $isCustomBadgeDisabled === false) {
            return $this->hideCustomBadge($product, $customBadgeImageFile);
        }
    }
}