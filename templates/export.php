<?php
/**
 * @package Exporter
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="form-group">
        <label class="col-md-2 control-label">
        <?php echo $this->text('Store'); ?>
        </label>
        <div class="col-md-4">
          <select class="form-control" name="settings[options][store_id]">
            <?php foreach ($stores as $store_id => $store) { ?>
            <option value="<?php echo $store_id; ?>"<?php echo $settings['options']['store_id'] == $store_id ? ' selected' : ''; ?>><?php echo $this->escape($store['name']); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <?php if (!$this->error(null, true)) { ?>
      <div class="form-group">
        <div class="col-md-4 col-md-offset-2">
          <a href="#export-columns" data-toggle="collapse"><?php echo $this->text('Columns'); ?> <span class="caret"></span></a>
        </div>
      </div>
      <?php } ?>
      <div id="export-columns" class="<?php echo $this->error(null, '', 'collapse'); ?>">
      <div class="form-group<?php echo $this->error('columns', ' has-error'); ?>">
        <div class="col-md-4 col-md-offset-2">
          <?php foreach ($columns as $field => $label) { ?>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="settings[columns][]" value="<?php echo $this->e($field); ?>"<?php echo !empty($settings['columns']) && in_array($field, $settings['columns']) ? ' checked' : ''; ?>> <?php echo $this->e($label); ?>
            </label>
          </div>
          <?php } ?>
          <div class="help-block"><?php echo $this->error('columns'); ?></div>
        </div>
      </div>
      </div>
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <button class="btn btn-default" name="export" value="1"><?php echo $this->text('Export'); ?></button>
        </div>
      </div>
    </div>
  </div>
</form>
<?php echo $_job; ?>
