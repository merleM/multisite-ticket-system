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
            <option value="0">Nach Kategorie filtern</option>
            <?php foreach( $categories as $category ) { ?>
              <option <?php if ($_POST['category'] == $category->id) { ?>selected="true" <?php }; ?>value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
            <?php } ?>
        </select>

        <select name="priority">
            <option <?php if ($_POST['priority'] == 'all') { ?>selected="true" <?php }; ?>value="Alle">Nach Priorität filtern</option>
            <option <?php if ($_POST['priority'] == 'Niedrig') { ?>selected="true" <?php }; ?>value="Niedrig">Niedrig</option>
            <option <?php if ($_POST['priority'] == 'Normal') { ?>selected="true" <?php }; ?>value="Normal">Normal</option>
            <option <?php if ($_POST['priority'] == 'Mittel') { ?>selected="true" <?php }; ?>value="Mittel">Mittel</option>
            <option <?php if ($_POST['priority'] == 'Hoch') { ?>selected="true" <?php }; ?>value="Hoch">Hoch</option>
        </select>

        <select name="status">
            <option <?php if ($_POST['status'] == 'all') { ?>selected="true" <?php }; ?>value="Alle">Nach Status filtern</option>
            <option <?php if ($_POST['status'] == 'Offen') { ?>selected="true" <?php }; ?>value="Offen">Offen</option>
            <option <?php if ($_POST['status'] == 'In Bearbeitung') { ?>selected="true" <?php }; ?>value="In Bearbeitung">In Bearbeitung</option>
            <option <?php if ($_POST['status'] == 'Geschlossen') { ?>selected="true" <?php }; ?>value="Geschlossen">Geschlossen</option>
        </select>

        <input type="submit" name="filterBy" class="button" value="Auswahl einschränken">
        <?php } else { ?>
            <select name="category">
                <option value="0">Nach Kategorie filtern</option>
                <?php foreach( $categories as $category ) { ?>
                    <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                <?php } ?>
            </select>

            <select name="priority">
                <option value="Alle">Nach Priorität filtern</option>
                <option value="Niedrig">Niedrig</option>
                <option value="Normal">Normal</option>
                <option value="Mittel">Mittel</option>
                <option value="Hoch">Hoch</option>
            </select>

            <select name="status">
                <option value="Alle">Nach Status filtern</option>
                <option value="Offen">Offen</option>
                <option value="In Bearbeitung">In Bearbeitung</option>
                <option value="Geschlossen">Geschlossen</option>
            </select>

            <input type="submit" name="filterBy" class="button" value="Auswahl einschränken">


        <?php } ?>
    </form>
</div>