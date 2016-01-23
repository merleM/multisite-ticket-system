<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h2><?php _e('Ticket', 'multisite-ticket-system'); ?> #<?php echo $ticket->id; ?> <?php echo sprintf('<a href="?page=%s" class="page-title-action">%s</a>', esc_attr( $_REQUEST['page'] ), $titleUrl); ?></h2>
        <div id="poststuff">
            <form method="post">
                <div class="titlewrap">
                    <table class="form-table">
                    <tbody>

                    <tr>
                        <th scope="row"><label for="title"><?php _e('Betreff', 'multisite-ticket-system'); ?></label></th>
                        <td><?php echo $ticket->title;?></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="message"><?php _e('Nachricht', 'multisite-ticket-system'); ?></label></th>
                        <td><textarea readonly cols="50" rows="5"><?php echo $ticket->admin_message;?></textarea></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="admin"><?php _e('Von', 'multisite-ticket-system'); ?></label></th>
                        <td><?php echo $ticket->admin_name;?></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="blog"><?php _e('Blogtitel', 'multisite-ticket-system'); ?></label></th>
                        <td><?php echo $ticket->blog_name;?></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="date"><?php _e('Erstellt am', 'multisite-ticket-system'); ?></label></th>
                        <td><?php echo date_format(date_create($ticket->date), 'd.m.Y H:i');?></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="category"><?php _e('Kategorie', 'multisite-ticket-system'); ?></label></th>
                        <td><?php echo $ticket->category." (".$ticket->cat_recipient.")";?></td>

                    </tr>

                    <tr>
                        <th scope="row"><label for="status"><?php _e('Status', 'multisite-ticket-system'); ?></label></th>
                        <td><select id="ticket_status" name="ticket_status" style="margin-top: 10px;">
                            <option value="<?php echo $msts_status_open; ?>" <?php echo ( $ticket->status == $msts_status_open ) ? 'selected="selected"':'';?>><?php echo $msts_status_open; ?></option>
                            <option value="<?php echo $msts_status_pending; ?>" <?php echo ( $ticket->status == $msts_status_pending )? 'selected="selected"':'';?>><?php echo $msts_status_pending; ?></option>
                            <option value="<?php echo $msts_status_closed; ?>" <?php echo ( $ticket->status == $msts_status_closed )? 'selected="selected"':'';?>><?php echo $msts_status_closed; ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="priority"><?php _e('Priorität', 'multisite-ticket-system'); ?></label></th>
                        <td><select id="priority" name="priority" style="margin-top: 10px;">
                            <option value="<?php echo $msts_prio_normal; ?>" <?php echo ( $ticket->priority == $msts_prio_normal ) ? 'selected="selected"':'';?>><?php echo $msts_prio_normal; ?></option>
                            <option value="<?php echo $msts_prio_high; ?>" <?php echo ( $ticket->priority == $msts_prio_high ) ? 'selected="selected"':'';?>><?php echo $msts_prio_high; ?></option>
                            <option value="<?php echo $msts_prio_medium; ?>" <?php echo ( $ticket->priority == $msts_prio_medium ) ? 'selected="selected"':'';?>><?php echo $msts_prio_medium; ?></option>
                            <option value="<?php echo $msts_prio_low; ?>" <?php echo ( $ticket->priority == $msts_prio_low ) ? 'selected="selected"':'';?>><?php echo $msts_prio_low; ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="comment"><?php _e('Kommentar', 'multisite-ticket-system'); ?></label></th>
                        <?php if( !is_super_admin() ) {
                        ?>
                            <td><textarea id="comment" name="comment" cols="50" rows="5" required></textarea></td>
                        <?php } else {?>
                            <td><textarea id="comment" name="comment" cols="50" rows="5"></textarea></td>
                        <?php } ?>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>">
                <input type="hidden" name="user_name" value="<?php echo $current_user->user_login; ?>">
                <input type="submit" name="submit" class="button button-primary" value="<?php _e('Speichern', 'multisite-ticket-system'); ?>">
                <input type="button" class="button button-primary" value="<?php _e('Zurücksetzen', 'multisite-ticket-system'); ?>" onClick="this.form.reset()" />
                </div>
            </form>
        </div>
</div>
