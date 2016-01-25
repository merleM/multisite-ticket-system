<?php
/** ABSPATH check which terminates the script if accessed outside of WordPress */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div>
    <form method="post">
        <?php if (isset($_POST['filterBy'])) { ?>
        <select name="category">
            <option value="0"><?php _e('Nach Kategorie filtern', 'multisite-ticket-system'); ?></option>
            <?php foreach( $categories as $category ) { ?>
              <option <?php if ($_POST['category'] == $category->id) { ?>selected="true" <?php }; ?>value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
            <?php } ?>
        </select>

        <select name="priority">
            <option <?php if ($_POST['priority'] == 'all') { ?>selected="true" <?php }; ?>value="all"><?php _e('Nach Priorität filtern', 'multisite-ticket-system'); ?></option>
            <option <?php if ($_POST['priority'] == __('Niedrig', 'multisite-ticket-system')) { ?>selected="true" <?php }; ?>value="<?php _e('Niedrig', 'multisite-ticket-system'); ?>"><?php _e('Niedrig', 'multisite-ticket-system'); ?></option>
            <option <?php if ($_POST['priority'] == __('Normal', 'multisite-ticket-system')) { ?>selected="true" <?php }; ?>value="<?php _e('Normal', 'multisite-ticket-system'); ?>"><?php _e('Normal', 'multisite-ticket-system'); ?></option>
            <option <?php if ($_POST['priority'] == __('Mittel', 'multisite-ticket-system')) { ?>selected="true" <?php }; ?>value="<?php _e('Mittel', 'multisite-ticket-system'); ?>"><?php _e('Mittel', 'multisite-ticket-system'); ?></option>
            <option <?php if ($_POST['priority'] == __('Hoch', 'multisite-ticket-system')) { ?>selected="true" <?php }; ?>value="<?php _e('Hoch', 'multisite-ticket-system'); ?>"><?php _e('Hoch', 'multisite-ticket-system'); ?></option>
        </select>

        <select name="status">
            <option <?php if ($_POST['status'] == 'all') { ?>selected="true" <?php }; ?>value="all"><?php _e('Nach Status filtern', 'multisite-ticket-system'); ?></option>
            <option <?php if ($_POST['status'] == __('Offen', 'multisite-ticket-system')) { ?>selected="true" <?php }; ?>value="<?php _e('Offen', 'multisite-ticket-system'); ?>"><?php _e('Offen', 'multisite-ticket-system'); ?></option>
            <option <?php if ($_POST['status'] == __('In Bearbeitung', 'multisite-ticket-system')) { ?>selected="true" <?php }; ?>value="<?php _e('In Bearbeitung', 'multisite-ticket-system'); ?>"><?php _e('In Bearbeitung', 'multisite-ticket-system'); ?></option>
            <option <?php if ($_POST['status'] == __('Geschlossen', 'multisite-ticket-system')) { ?>selected="true" <?php }; ?>value="<?php _e('Geschlossen', 'multisite-ticket-system'); ?>"><?php _e('Geschlossen', 'multisite-ticket-system'); ?></option>
        </select>

        <input type="submit" name="filterBy" class="button" value="<?php _e('Auswahl einschränken', 'multisite-ticket-system'); ?>">
        <?php } else { ?>
            <select name="category">
                <option value="0"><?php _e('Nach Kategorie filtern', 'multisite-ticket-system'); ?></option>
                <?php foreach( $categories as $category ) { ?>
                    <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                <?php } ?>
            </select>

            <select name="priority">
                <option value="all"><?php _e('Nach Priorität filtern', 'multisite-ticket-system'); ?></option>
                <option value="<?php _e('Niedrig', 'multisite-ticket-system'); ?>"><?php _e('Niedrig', 'multisite-ticket-system'); ?></option>
                <option value="<?php _e('Normal', 'multisite-ticket-system'); ?>"><?php _e('Normal', 'multisite-ticket-system'); ?></option>
                <option value="<?php _e('Mittel', 'multisite-ticket-system'); ?>"><?php _e('Mittel', 'multisite-ticket-system'); ?></option>
                <option value="<?php _e('Hoch', 'multisite-ticket-system'); ?>"><?php _e('Hoch', 'multisite-ticket-system'); ?></option>
            </select>

            <select name="status">
                <option value="all"><?php _e('Nach Status filtern', 'multisite-ticket-system'); ?></option>
                <option value="<?php _e('Offen', 'multisite-ticket-system'); ?>"><?php _e('Offen', 'multisite-ticket-system'); ?></option>
                <option value="<?php _e('In Bearbeitung', 'multisite-ticket-system'); ?>"><?php _e('In Bearbeitung', 'multisite-ticket-system'); ?></option>
                <option value="<?php _e('Geschlossen', 'multisite-ticket-system'); ?>"><?php _e('Geschlossen', 'multisite-ticket-system'); ?></option>
            </select>

            <input type="submit" name="filterBy" class="button" value="<?php _e('Auswahl einschränken', 'multisite-ticket-system'); ?>">


        <?php } ?>
    </form>
</div>