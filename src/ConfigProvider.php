<?php
/**
 * @link      http://github.com/zetta-repo/tss-authentication for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace TSS\Authentication;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Return configuration for this component.
     *
     * @return array
     */
    public function __invoke()
    {
        return $this->getConfig();
    }
}
