<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h2><?php $titleUrl = __('Zurück zu Tickets', 'multisite-ticket-system');
        _e('Einstellungen', 'multisite-ticket-system');
        echo sprintf('<a href="?page=msts_plugin" class="page-title-action">%s</a>', $titleUrl); ?></h2>
        <div class="settings">
            <div id="message" class="updated notice is-dismissible"><p><strong><?php _e('Hinweis', 'multisite-ticket-system'); ?></strong>: <?php _e('Bei Löschung einer Kategorie werden alle Tickets dieser Kategorie nach "WordPress Support" verschoben.', 'multisite-ticket-system'); ?></p></div>
            <form method="post">
            <table class="wp-list-table widefat fixed striped">
                <tbody>
                <tr>
                    <th><?php _e('Kategorie', 'multisite-ticket-system'); ?></th>
                    <th><?php _e('Name', 'multisite-ticket-system'); ?></th>
                    <th><?php _e('E-Mail-Empfänger', 'multisite-ticket-system'); ?></th>
                    <th><?php _e('Ändern', 'multisite-ticket-system'); ?></th>
                    <th><?php _e('Löschen', 'multisite-ticket-system'); ?></th>
                </tr>
                <?php foreach ( $categories as $category ){
                        $deleteCat_nonce = wp_create_nonce('delete_cat');
                        $editCat_nonce = wp_create_nonce('update_cat');
                ?>
                <tr>
                    <td><?php echo $category->name; ?></td>
                    <td><?php echo $category->recipient; ?></td>
                    <td><?php echo $category->recipient_email; ?></td>
                    <input type="hidden" name="cat_id" value="<?php echo $category->id; ?>">
                    <td>
                        <?php
                        $titleChange = __('Kategorie ändern', 'multisite-ticket-system');
                        $titleDel = __('Kategorie löschen', 'multisite-ticket-system');
                        echo sprintf('<a href="?page=%s&action=%s&category=%s&_wpnonce=%s"><img title="%s" src="' . MSTS_PLUGIN_URL. 'assets/images/editImg.png" /></a>', esc_attr($_REQUEST['page']), 'edit', absint($category->id), $editCat_nonce, $titleChange);
                        ?>
                        </td>
                    <?php if($category->id != 1) { ?>
                    <td>
                        <?php
                        echo sprintf('<a href="?page=%s&action=%s&category=%s&_wpnonce=%s"><img title="%s" src="' . MSTS_PLUGIN_URL . 'assets/images/deleteImg.png" /></a></span>', esc_attr($_REQUEST['page']), 'delete', absint($category->id), $deleteCat_nonce, $titleDel);
                        } ?>
                    </td>
                <?php
                   }
                ?>
                 <tr>
                    <td><input type="text" name="new_cat_name" size="25" value="" placeholder="<?php _e('Neue Kategorie', 'multisite-ticket-system'); ?>" required></td>
                    <td><input type="text" name="new_cat_recipient" size="25" value="" placeholder="<?php _e('Empfänger', 'multisite-ticket-system'); ?>" required></td>
                    <td><input type="text" name="new_cat_email" size="25" value="" placeholder="<?php _e('E-Mail-Adresse (optional)', 'multisite-ticket-system'); ?>"></td>
                 </tr>
                 </tbody>
            </table>
            <div class="buttons">
                <input type="submit" name="submit" class="button button-primary" value="<?php _e('Hinzufügen', 'multisite-ticket-system'); ?>">
                <input type="button" class="button button-primary" value="<?php _e('Zurücksetzen', 'multisite-ticket-system'); ?>" onClick="this.form.reset()" />
            </div>
            </form>
        </div>

</div>