<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>


<h2><?php _e('Kommentare', 'multisite-ticket-system'); ?>:</h2>
<?php foreach ( $comments as $comment ) {
    $status_color='';
    switch ($comment->update_status){
        case $msts_status_open:
            $status_color='open';
            break;
        case $msts_status_pending:
            $status_color='pending';
            break;
        case $msts_status_closed:
            $status_color='closed';
            break;
    }

    $priority_color='';
    switch ($comment->update_priority){
        case $msts_prio_low:
            $priority_color='low';
            break;
        case $msts_prio_normal:
            $priority_color='normal';
            break;
        case $msts_prio_medium:
            $priority_color='medium';
            break;
        case $msts_prio_high:
            $priority_color='high';
            break;
    }
    ?>
    <div><p><strong><?php _e('Kommentar', 'multisite-ticket-system'); ?></strong><br> <?php _e('erstellt von', 'multisite-ticket-system'); ?> <i><?php echo $comment->user_name; ?></i> <?php _e('am', 'multisite-ticket-system'); ?> <i><?php echo date_format(date_create($comment->update_date), 'd.m.Y H:i:s'); ?></i></p>
        <?php
        if ( !empty ($comment->update_message)) {
            ?>
            <p class="commentBubble"><?php echo $comment->update_message; ?></p>
        <?php }  else { ?>
            <p class="commentBubble"><i><?php echo $superAdmin_comment; ?></i></p>
        <?php }?>
    </div>
    <p><?php _e('Status', 'multisite-ticket-system'); ?>: <span class="label status-<?php echo $status_color;?>"><?php echo $comment->update_status; ?></span> <?php _e('Priorität', 'multisite-ticket-system'); ?>:<span class="label priority-<?php echo $priority_color;?>"><?php echo $comment->update_priority; ?></span></p>
    <hr class="comment-line">
<?php }