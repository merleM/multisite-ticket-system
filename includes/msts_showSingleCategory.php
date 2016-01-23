<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h3 class="category-title"><?php _e('Kategorie ändern', 'multisite-ticket-system'); ?></h3>
        <div class="settings">
            <form method="post">
            <table class="wp-list-table widefat">
                <tbody>
                <tr>
                    <th><?php _e('Kategorie', 'multisite-ticket-system'); ?></th>
                    <th><?php _e('Name', 'multisite-ticket-system'); ?></th>
                    <th><?php _e('E-Mail-Empfänger', 'multisite-ticket-system'); ?></th>
                </tr>
                <tr>
                 <?php foreach ( $categories as $category ){
                        if ( $category->id == $_GET['category'] ){
                            if ($_GET['category'] == 1){
                 ?>
                                <td><input type="hidden" name="cat_name" value="<?php echo $category->name; ?>"><?php echo $category->name; ?></td>
                                <td><input type="text" name="cat_recipient" size="25" value="<?php echo $category->recipient; ?>" required>
                                </td>
                                <td><input type="text" name="cat_email" size="25" value="<?php echo $category->recipient_email; ?>" required>
                                </td>
                                <input type="hidden" name="cat_id" value="<?php echo $_GET['category']; ?>">
                  <?php
                             } else{
                 ?>
                                <td><input type="text" name="cat_name" size="25" value="<?php echo $category->name; ?>" required></td>
                                <td><input type="text" name="cat_recipient" size="25" value="<?php echo $category->recipient; ?>" required>
                                </td>
                                <td><input type="text" name="cat_email" size="25" value="<?php echo $category->recipient_email; ?>">
                                </td>
                                <input type="hidden" name="cat_id" value="<?php echo $_GET['category']; ?>">
                 <?php
                  }
                         }
                 }
                ?>
                 </tr>
                 </tbody>
            </table>
            <div class="buttons">
                <input type="submit" name="submitCat" class="button button-primary" value="<?php _e('Speichern', 'multisite-ticket-system'); ?>">
                <input type="button" class="button button-primary" value="<?php _e('Zurücksetzen', 'multisite-ticket-system'); ?>" onClick="this.form.reset()" />
            </div>
            </form>
            </div>

</div>