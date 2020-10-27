<?php

/*

Copyright (c) Markeaze Inc. https://markeaze.com

This file is part of the markeaze-for-woocommerce plugin created by Markeaze.

Repository: https://github.com/markeaze/markeaze-woocommerce
Documentation: https://github.com/markeaze/markeaze-woocommerce/blob/master/README.md

*/

?>
<div class="wrap">
  <div class="icon32" id="icon-options-general"></div>
  <h2><?php esc_html_e('Markeaze Settings', 'markeaze') ?></h2>

  <?php if (!empty($message)): ?>
    <div id="message" class="updated fade">
      <p><?= esc_html($message) ?></p>
    </div>
  <?php endif; ?>

  <div>
    <form name="form1" method="post" action="admin.php?page=markeaze" enctype="multipart/form-data">

      <table class="form-table">
        <tr valign="top">
          <th scope="row"><?php esc_html_e('APP Key', 'markeaze') ?></th>
          <td>
            <input
              class="regular-text code"
              name='markeaze_key'
              type='text'
              value='<?= esc_attr($markeaze_plgn_options['markeaze_key']) ?>'
            />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><?php esc_html_e('Submit only product_id (not variation_id)', 'markeaze') ?></th>
          <td>
            <label for="only_product_id_1"><?php esc_html_e('Yes', 'markeaze') ?></label>
            <input
              id="only_product_id_1"
              name='only_product_id'
              type='radio'
              value='1'
              <?= $markeaze_plgn_options['only_product_id'] ? 'checked="checked"' : '' ?>
            />
            <label for="only_product_id_1"><?php esc_html_e('No', 'markeaze') ?></label>
            <input
              id="only_product_id_0"
              name='only_product_id'
              type='radio'
              value='0'
              <?= !$markeaze_plgn_options['only_product_id'] ? 'checked="checked"' : '' ?>
            />
          </td>
        </tr>
      </table>

      <input type="hidden" name="markeaze_plgn_form_submit" value="submit" />
      <input type="submit" class="button-primary" value="<?= _e('Save Changes') ?>" />
      <a class="button-secondary" href="https://auth.markeaze.com?utm_source=opencart&utm_medium=referral&utm_campaign=Opencart_plugin" target="_blank">
        <?php esc_html_e('Go to Markeaze account', 'markeaze') ?>
      </a>

      <?php wp_nonce_field(plugin_basename(dirname(__DIR__)), 'markeaze_plgn_nonce_name'); ?>
    </form>
  </div>
</div>
