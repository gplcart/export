<?php

/**
 * @package Exporter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\export;

/**
 * Main class for Exporter module
 */
class Main
{

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
            'menu' => array('admin' => /* @text */'Export'),
            'access' => 'export_product',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\export\\controllers\\Export', 'doExport')
            )
        );
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
        $permissions['export_product'] = /* @text */'Exporter: export products';
    }

    /**
     * Implements hook "cron"
     */
    public function hookCron()
    {
        gplcart_file_empty(gplcart_file_private_module('export'), array('csv'), 24 * 60 * 60);
    }

}
