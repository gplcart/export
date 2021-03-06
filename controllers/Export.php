<?php

/**
 * @package Exporter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\export\controllers;

use gplcart\core\controllers\backend\Controller;
use gplcart\core\models\Product;

/**
 * Handles incoming requests and outputs data related to Expor module
 */
class Export extends Controller
{

    /**
     * Product model class instance
     * @var \gplcart\core\models\Product $product
     */
    protected $product;

    /**
     * Export constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        parent::__construct();

        $this->product = $product;
    }

    /**
     * Route callback to display the export page
     */
    public function doExport()
    {
        $this->downloadCsvExport();

        $settings = $this->module->getSettings('export');

        if (empty($settings['columns'])) {
            $settings['columns'] = array_keys($settings['header']);
        }

        $this->setData('settings', $settings);
        $this->setData('columns', $settings['header']);
        $this->setData('stores', $this->store->getList());

        $this->submitExport();
        $this->setTitleDoExport();
        $this->setBreadcrumbDoExport();

        $this->outputDoExport();
    }

    /**
     * Sets titles on the export page
     */
    protected function setTitleDoExport()
    {
        $this->setTitle($this->text('Export'));
    }

    /**
     * Sets breadcrumbs on the export page
     */
    protected function setBreadcrumbDoExport()
    {
        $breadcrumb = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $this->setBreadcrumb($breadcrumb);
    }

    /**
     * Renders the export page templates
     */
    protected function outputDoExport()
    {
        $this->output('export|export');
    }

    /**
     * Start export
     */
    protected function submitExport()
    {
        if ($this->isPosted('export') && $this->validateExport()) {
            $this->setJobExport();
        }
    }

    /**
     * Validates submitted export data
     * @return boolean
     */
    protected function validateExport()
    {
        $this->setSubmitted('settings');
        $this->validateElement('columns', 'required');
        $this->validateFileExport();

        return !$this->hasErrors();
    }

    /**
     * Validates destination directory and file
     * @return boolean
     */
    protected function validateFileExport()
    {
        $directory = gplcart_file_private_module('export');

        if (!file_exists($directory) && !mkdir($directory, 0775, true)) {
            $this->setError('file', $this->text('Unable to create @name', array('@name' => $directory)));
            return false;
        }

        $date = date('d-m-Y--H-i');
        $file = gplcart_file_unique("$directory/$date.csv");

        if (file_put_contents($file, '') === false) {
            $this->setError('file', $this->text('Unable to create @name', array('@name' => $file)));
            return false;
        }

        $this->setSubmitted('file', $file);
        return true;
    }

    /**
     * Sets up export job
     */
    protected function setJobExport()
    {
        $submitted = $this->getSubmitted();
        $settings = $this->module->getSettings('export');

        $settings['columns'] = $submitted['columns'];
        $settings['options'] = $submitted['options'];

        $this->module->setSettings('export', $settings);

        $data = array_merge($settings, $submitted);

        $data['header'] = array_intersect_key($data['header'], array_flip($data['columns']));
        gplcart_file_csv($data['file'], $data['header'], $data['delimiter']);

        $hash = gplcart_string_encode($data['file']);
        $total = $this->getTotalProductExport($data['options']);

        $vars = array('@url' => $this->url('', array('download' => $hash)), '@num' => $total);
        $finish = $this->text('Exported @num items. <a href="@url">Download</a>', $vars);

        $job = array(
            'data' => $data,
            'total' => $total,
            'id' => 'export_product',
            'redirect_message' => array('finish' => $finish)
        );

        $this->job->submit($job);
    }

    /**
     * Download a created CSV file
     */
    protected function downloadCsvExport()
    {
        $file = $this->getQuery('download');

        if (!empty($file)) {
            $this->download(gplcart_string_decode($file));
        }
    }

    /**
     * Returns a total number of products found for the given conditions
     * @param array $options
     * @return integer
     */
    protected function getTotalProductExport(array $options)
    {
        $options['count'] = true;
        return (int) $this->product->getList($options);
    }

}
