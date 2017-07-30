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
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/module/settings/export'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\export\\controllers\\Settings', 'editSettings')
            )
        );

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
