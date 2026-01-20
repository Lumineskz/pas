<?php
// adopt_request.php - Redirect to adoption form
require_once 'config.php';
requireLogin();

if (isset($_GET['pet_id'])) {
    header("Location: adoption_form.php?pet_id=" . intval($_GET['pet_id']));
} else {
    header("Location: index.php");
}
exit();
?>
