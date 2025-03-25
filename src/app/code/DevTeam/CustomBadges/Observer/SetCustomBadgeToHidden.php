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
    // Properties
    private readonly RequestInterface $request;
    private readonly Processor $mediaGalleryProcessor;
    private readonly ManagerInterface $messageManager;

    // Methods
    /**
     * Summary of __construct
     * 
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\Product\Gallery\Processor $mediaGalleryProcessor
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        Processor $mediaGalleryProcessor,
        ManagerInterface $messageManager,
    ) {
        $this->request = $request;
        $this->mediaGalleryProcessor = $mediaGalleryProcessor;
        $this->messageManager = $messageManager;
    }

    /**
     * Method for returning the custom badge ID
     * 
     * @param string $customBadgeImage
     * @param array $mediaGalleryImages
     * @return int|bool|string
     */
    private function getCustomBadgeID(
        string $customBadgeImage, 
        array $mediaGalleryImages
    ): int|bool|string {
        $customBadgeCheck = array_map(
            fn($item) => $item['file'] === $customBadgeImage, 
            $mediaGalleryImages
        );

        return array_search(true, $customBadgeCheck);
    }

    /**
     * Method for saving and automatically hiding the custom badge on the frontend media gallery
     * 
     * @param \Magento\Catalog\Model\Product $product
     * @param string $mediaGalleryImageFile
     * @param bool|null $isCustomBadgeDisabled
     * @return void
     */
    private function savingCustomBadge(
        Product $product, 
        string $mediaGalleryImageFile, 
        ?bool $isCustomBadgeDisabled
    ): void {
        // Checks if the custom badge is not yet disabled
        if($isCustomBadgeDisabled === false) {
            $this->mediaGalleryProcessor->updateImage(
                $product, 
                $mediaGalleryImageFile, 
                [
                    "disabled" => true,
                ]
            );
        }

        $this->messageManager->addSuccessMessage(__("Custom Badge has been saved."));
    }

    /**
     * Method for checking if the custom badge is assigned other roles
     * 
     * @param array $requestData
     * @param string $customBadgeImageFile
     * @return bool
     */
    private function imageRoleCheck(
        array $requestData, 
        string $customBadgeImageFile
    ): bool {
        $roleCheckArray = array_map(
            fn($item) => ($item === "no_selection") ? null : $item, 
            [
                $requestData['image'],
                $requestData['small_image'],
                $requestData['thumbnail'],
                $requestData['swatch_image']
            ]
        );

        return in_array($customBadgeImageFile, $roleCheckArray);
    }

    /**
     * Summary of execute
     * 
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute(Observer $observer): void {
        $requestData = $this->request->getParam("product");
        $customBadgeImageFile = $requestData['custom_badge'] ?? null;
        $mediaGalleryImages = $requestData["media_gallery"] ?? null;

        // Checks if the custom badge is given other roles
        if(
            !empty($customBadgeImageFile) 
            && $this->imageRoleCheck($requestData, $customBadgeImageFile)
        ) {
            throw new LocalizedException(__("The custom badge must not have other roles. Please try again."));
        }

        // Checks if the media gallery is empty
        if(!empty($mediaGalleryImages)) {
            $mediaGalleryImages = $requestData["media_gallery"]["images"];
            $customBadgeID = $this->getCustomBadgeID($customBadgeImageFile, $mediaGalleryImages);
        }

        // Checks if a custom badge is available
        if(!empty($customBadgeID)) {
            $product = $observer->getEvent()->getData("product");
            $isCustomBadgeDisabled = (bool)$mediaGalleryImages[$customBadgeID]["disabled"];

            $this->savingCustomBadge(
                $product, 
                $customBadgeImageFile, 
                $isCustomBadgeDisabled
            );
        }
    }
}