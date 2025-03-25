<?php declare(strict_types = 1);

namespace DevTeam\CustomBadges\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\ResourceModel\Attribute;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CustomBadgeAttribute implements DataPatchInterface
{
    // Properties
    const ATTRIBUTE_CODE = "custom_badge";
    const ENTITY_TYPE = "catalog_product";

    private readonly Config $config;
    private readonly Attribute $attribute;
    private readonly EavSetupFactory $eavSetupFactory;
    private readonly ModuleDataSetupInterface $moduleDataSetup;

    // Methods
    /**
     * Summary of __construct
     * 
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ResourceModel\Attribute $attribute
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        Config $config,
        Attribute $attribute,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->config = $config;
        $this->attribute = $attribute;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Summary of getDependencies
     * 
     * @return array
     */
    public static function getDependencies(): array {
        return [];
    }

    /**
     * Summary of getAliases
     * 
     * @return array
     */
    public function getAliases(): array {
        return [];
    }

    /**
     * Summary of apply
     * 
     * @return CustomBadgeAttribute
     */
    public function apply(): self {
        // For the EAV attribute creation
        $eavSetup = $this->eavSetupFactory->create([
            "setup" => $this->moduleDataSetup
        ]);

        $eavSetup->addAttribute(
            self::ENTITY_TYPE, 
            self::ATTRIBUTE_CODE, 
            [
                "type" => "varchar",
                "label" => "Custom Badge",
                "input" => "media_image",
                "required" => false,
                "system" => false,
                "visible" => true,
                "visible_on_front" => true,
                "used_in_product_listing" => true,
            ]
        );

        // Gets the eav attribute
        $attribute = $this->config->getAttribute(
            self::ENTITY_TYPE, 
            self::ATTRIBUTE_CODE
        );

        // Renders the input field inside the specified forms
        $attribute->setData('used_in_forms', [
            "product_form"
        ]);

        // Saves the new attribute and creates the form
        $this->attribute->save($attribute);

        return $this;
    }
}
