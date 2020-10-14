<?php

$enable  =  $markeaze_plgn_options['only_product_id']? ' checked="checked"' : '';
$disable = !$markeaze_plgn_options['only_product_id']? ' checked="checked"' : '';
?>
<div class="wrap">
  <div class="icon32" id="icon-options-general"></div>
  <h2><?php echo __("Markeaze Settings", 'markeaze'); ?></h2>

  <div
    id="message"
    class="updated fade" <?php if (!isset($_REQUEST['markeaze_plgn_form_submit']) || $message == "") echo "style=\"display:none\""; ?>
  >
    <p><?php echo $message; ?></p>
  </div>

  <div class="error" <?php if ("" == $error) echo "style=\"display:none\""; ?>>
    <p>
      <strong><?php echo $error; ?></strong>
    </p>
  </div>

  <div>
    <form name="form1" method="post" action="admin.php?page=markeaze" enctype="multipart/form-data">

      <table class="form-table">
        <tr valign="top">
          <th scope="row"><?php echo __("APP Key", 'markeaze'); ?></th>
          <td>
            <input
              class="regular-text code"
              name='markeaze_key'
              type='text'
              value='<?php echo $markeaze_plgn_options['markeaze_key']; ?>'
            />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><?php echo __("Currency exchange rate", 'markeaze'); ?></th>
          <td>
            <input
              class="regular-text code"
              name='currency_excange_rate'
              type='text'
              value='<?php echo $markeaze_plgn_options['currency_excange_rate']; ?>'
            />
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><?php echo __("Submit only product_id (not variation_id)", 'markeaze'); ?></th>
          <td>
            <label for="only_product_id_1"><?php echo __("Yes", 'markeaze'); ?></label>
            <input
              id="only_product_id_1"
              name='only_product_id'
              type='radio'
              value='1'<?php echo $enable; ?>
            />
            <label for="only_product_id_1"><?php echo __("No", 'markeaze'); ?></label>
            <input
              id="only_product_id_0"
              name='only_product_id'
              type='radio'
              value='0'<?php echo $disable; ?>
            />
          </td>
        </tr>
      </table>

      <input type="hidden" name="markeaze_plgn_form_submit" value="submit"/>
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>

      <?php wp_nonce_field(plugin_basename(dirname(__DIR__)), 'markeaze_plgn_nonce_name'); ?>
    </form>
  </div>
  <br/>
  <div class="link">
      <a class="button-secondary" href="https://auth.markeaze.com" target="_blank"><?php echo __("Go to Markeaze account", 'markeaze'); ?></a>
  </div>
</div>
