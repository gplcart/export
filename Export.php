<?php

/**
 * @package Exporter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\export;

use gplcart\core\Module;

/**
 * Main class for Exporter module
 */
class Export extends Module
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Module info
     * @return array
     */
    public function info()
    {
        return array(
            'name' => 'Exporter',
            'version' => '1.0.0-dev',
            'description' => 'Allows to export products to a CSV file',
            'author' => 'Iurii Makukh ',
            'core' => '1.x',
            'license' => 'GPL-3.0+',
            'configure' => 'admin/module/settings/export',
            'settings' => $this->getDefaultSettings()
        );
    }

    /**
     * Returns an array of default module settings
     * @return array
     */
    protected function getDefaultSettings()
    {
        return array(
            'limit' => 50,
            'delimiter' => ',',
            'multiple' => '|',
            'options' => array('store_id' => 1),
            'columns' => array(),
            'header' => array(
                'product_id' => 'Product ID', 'title' => 'Title', 'sku' => 'SKU',
                'price' => 'Price', 'currency' => 'Currency', 'stock' => 'Stock',
                'product_class_id' => 'Product class ID', 'store_id' => 'Store ID',
                'category_id' => 'Category ID', 'brand_category_id' => 'Brand category ID',
                'alias' => 'Alias', 'images' => 'Images',
                'status' => 'Enabled', 'description' => 'Description',
                'meta_title' => 'Meta title', 'meta_description' => 'Meta description',
                'related' => 'Related product ID', 'width' => 'Width', 'height' => 'Height',
                'length' => 'Length', 'size_unit' => 'Size unit',
                'weight' => 'Weight', 'weight_unit' => 'Weight unit',
            ),
        );
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        // Module settings page
        $routes['admin/module/settings/export'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\export\\controllers\\Settings', 'editSettings')
            )
        );

        // Export page
        $routes['admin/tool/export'] = array(
            'menu' => array('admin' => 'Export'),
            'access' => 'export_product',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\export\\controllers\\Export', 'doExport')
            )
        );
    }

    /**
     * Implements hook "cron"
     */
    public function hookCron()
    {
        // Automatically delete created files older than 1 day
        $lifespan = 86400;
        $directory = GC_PRIVATE_DOWNLOAD_DIR . '/export';
        if (is_dir($directory)) {
            gplcart_file_delete($directory, array('csv'), $lifespan);
        }
    }

    /**
     * Implements hook "job.handlers"
     * @param array $handlers
     */
    public function hookJobHandlers(array &$handlers)
    {
        $handlers['export_product'] = array(
            'handlers' => array(
                'process' => array('gplcart\\modules\\export\\handlers\\Export', 'process')
            ),
        );
    }

    /**
     * Implements hook "user.role.permissions"
     * @param array $permissions
     */
    public function hookUserRolePermissions(array &$permissions)
    {
        $permissions['export_product'] = 'Exporter: export products';
    }

}
