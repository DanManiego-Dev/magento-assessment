<?php declare(strict_types = 1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::THEME,
    "frontend/DevTeam/CustomTheme",
    __DIR__
);