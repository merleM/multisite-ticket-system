<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h2><?php _e('Ticket erstellen', 'multisite-ticket-system');
        echo sprintf('<a href="?page=msts_plugin" class="page-title-action">%s</a>', $titleUrl); ?></h2>
    <div id="poststuff">
        <form method="post">
            <div class="titlewrap">
                <table class="form-table">
                 <tbody>
                 <tr>
                    <th scope="row"><label for="message_title"><?php _e('Betreff', 'multisite-ticket-system'); ?></label></th>
                    <td><input type="text" name="message_title" id="message_title" size="50" required></input></td>
                 </tr>

                 <tr>
                    <th scope="row"><label for="user_message"><?php _e('Nachricht', 'multisite-ticket-system'); ?></label></th>
                     <td><textarea cols="50" rows="5" name="user_message" id="user_message" required></textarea></td>
                </tr>

                <tr>
                    <th scope="row"><label for="user_name"><?php _e('Von', 'multisite-ticket-system'); ?></label></th>
                    <td><input type="hidden" name="user_name" value="<?php echo $current_user->user_login;?>"><?php echo $current_user->user_login; ?></td>
                </tr>

                 <tr>
                     <th scope="row"><label for="blog_name"><?php _e('Blogtitel', 'multisite-ticket-system'); ?></label></th>
                     <td><input type="hidden" name="blog_name" value="<?php echo $msts_blog_name;?>"><?php echo $msts_blog_name ?></td>
                 </tr>

                 <tr>
                    <th scope="row"><label for="category"><?php _e('Kategorie', 'multisite-ticket-system'); ?></label></th>
                    <td><select name="category">
                            <?php foreach( $categories as $category ) {
                                    if ( $category->id == 1 ) { ?>
                                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; echo " ("; _e('Superadministrator', 'multisite-ticket-system'); ?>)</option>
                                    <?php } else if( $category->recipient_email != null ) {?>
                                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; echo " ("; _e('via Mail an', 'multisite-ticket-system'); echo $category->recipient; ?>)</option>
                                    <?php
                                    } else { ?>
                                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?> (<?php echo $category->recipient; ?>)</option>
                                    <?php }
                    } ?>
                    </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="status"><?php _e('Status', 'multisite-ticket-system'); ?></label></th>
                    <td><?php echo $msts_status_open; ?></td>
                </tr>

                 <tr>
                    <th scope="row"><label for="priority"><?php _e('Priorität', 'multisite-ticket-system'); ?></label></th>
                    <td><select id="priority" name="priority" style="margin-top: 10px;">
                        <option value="<?php echo $msts_prio_normal; ?>"><?php echo $msts_prio_normal; ?></option>
                        <option value="<?php echo $msts_prio_low; ?>"><?php echo $msts_prio_low; ?></option>
                        <option value="<?php echo $msts_prio_medium; ?>"><?php echo $msts_prio_medium; ?></option>
                        <option value="<?php echo $msts_prio_high; ?>"><?php echo $msts_prio_high; ?></option>
                        </select>
                    </td>
                </tr>
                </tbody>
                </table>
                <input type="hidden" name="status" value="<?php echo $msts_status_open; ?>">
                <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>">
                <input type="submit" name="submit" class="button button-primary" value="<?php _e('Speichern', 'multisite-ticket-system'); ?>">
                <input type="button" class="button button-primary" value="<?php _e('Zurücksetzen', 'multisite-ticket-system'); ?>" onClick="this.form.reset()" />
            </div>
        </form>
    </div>
</div>