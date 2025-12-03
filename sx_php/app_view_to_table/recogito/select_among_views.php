<?php
$arrWhite_list_views = [
    "views__events_by_alphabet",
    "views__events_by_section",
    "views__events_within_grouped_sections",
    "views__events_grouped_within_sections",
    "views__persons_by_alphabet",
    "views__persons_by_section",
    "views__persons_within_grouped_sections",
    "views__persons_grouped_within_sections",
    "views__places_by_alphabet",
    "views__places_by_section",
    "views__places_within_grouped_sections",
    "views__places_grouped_within_sections",
    "views_books_grouped_by_chapter_sections",
    "views_wiki_persons"
];
/*
    "view_animals",
    "view_artworks",
    "view_attributes",
    "view_epithets",
    "view_focalisations",
    "view_interventions",
    "view_materials",
    "view_measures",
    "view_movements",
    "view_objects",
    "view_quotes",
    "view_transformations",
    "view_txs",
    "view_works",
    */


$selected_view = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['view'])) {
    $selected_view = htmlspecialchars($_POST['view']);
    if (!in_array($selected_view, $arrWhite_list_views)) {
        $selected_view = '';
    }
}

// Query to get all views from the information_schema.VIEWS table
$query = "SELECT TABLE_NAME FROM information_schema.VIEWS 
    WHERE TABLE_SCHEMA = :database ORDER BY TABLE_NAME ASC";

// Prepare and execute the statement
$stmt = $conn->prepare($query);
$stmt->execute([sx_TABLE_SCHEMA]);

// Fetch the views into an array
$views = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<form id="anchoreForm" class="wide_select_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']); ?>#anchoreForm" method="post">
    <fieldset>
        <label for="views">Select the View of an Annotation Type:</label>
        <select name="view" id="views">
            <option value="">Select a View</option>
            <?php
            if (!empty($views)) {
                foreach ($views as $view) {
                    $strView = $view['TABLE_NAME'];
                    if (str_contains($strView, 'views_')) {
                        $strSelected = '';
                        if ($selected_view == $strView) {
                            $strSelected = ' selected';
                        } ?>
                        <option value="<?php echo htmlspecialchars($strView); ?>" <?php echo $strSelected ?>>
                            <?php echo htmlspecialchars(sx_get_title_from_string($strView)); ?>
                        </option>
                <?php
                    }
                }
            } else { ?>
                <option value="">No views available</option>
            <?php
            } ?>
        </select>
        <input type="submit" value="Submit">
    </fieldset>
</form>