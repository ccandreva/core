<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

namespace ExtensionsModule\Controller;

use SecurityUtil, Zikula_View, ModUtil, LogUtil, ZLanguage, System, PluginUtil;
use ExtensionsModule\Util as ExtensionsUtil;
use Zikula_AbstractPlugin;
use Zikula_Controller_AbstractPlugin;
use Zikula_Plugin_ConfigurableInterface;

/**
 * Extensions_Plugin controller.
 */
class AdminpluginController extends \Zikula\Framework\Controller\AbstractController
{
    /**
     * Plugin instance.
     *
     * @var Zikula_AbstractPlugin
     */
    protected $plugin;

    /**
     * Plugin controller instance.
     *
     * @var Zikula_Controller_AbstractPlugin
     */
    protected $pluginController;

    /**
     * initialise.
     *
     * @return void
     */
    protected function initialize()
    {
        // Do not setupt a view for this controller.
    }

    /**
     * Dispatch a module view request.
     *
     * @return mixed
     */
    public function dispatchAction()
    {
        if (!SecurityUtil::checkPermission('Extensions::', '::', ACCESS_ADMIN)) {
            throw new \Zikula\Framework\Exception\ForbiddenException();
        }

        // Get input.
        $moduleName = $this->request->query->filter('_module', null, FILTER_SANITIZE_STRING);
        $pluginName = $this->request->query->filter('_plugin', null, FILTER_SANITIZE_STRING);
        $action = $this->request->query->filter('_action', null, FILTER_SANITIZE_STRING);

        // Load plugins.
        if (!$moduleName) {
            $type = 'SystemPlugin';
            PluginUtil::loadAllSystemPlugins();
        } else {
            $type = 'ModulePlugin';
            PluginUtil::loadAllModulePlugins();
        }

        if ($moduleName) {
            $serviceId = PluginUtil::getServiceId("{$type}_{$moduleName}_{$pluginName}_Plugin");
        } else {
            $serviceId = PluginUtil::getServiceId("{$type}_{$pluginName}_Plugin");
        }

        $this->throwNotFoundUnless($this->container->has($serviceId));

        $this->plugin = $this->container->get($serviceId);

        // Sanity checks.
        $this->throwNotFoundUnless($this->plugin->isInstalled(), __f('Plugin "%s" is not installed', $this->plugin->getMetaDisplayName()));
        $this->throwForbiddenUnless($this->plugin instanceof Zikula_Plugin_ConfigurableInterface, __f('Plugin "%s" is not configurable', $this->plugin->getMetaDisplayName()));

        $this->pluginController = $this->plugin->getConfigurationController();
        $this->throwNotFoundUnless($this->pluginController->getReflection()->hasMethod($action));

        return $this->pluginController->$action();
    }
}
