<?php
    include_once "../../ASEngine/AS.php";
    header('Content-Type: application/javascript');
?>

var $_lang = <?php echo ASLang::all(); ?>;
var _data = {};
_data["<?= ASCsrf::TOKEN_NAME ?>"] = "<?= ASCsrf::getToken() ?>";
jQuery.ajaxSetup({ data: _data, type: "POST" });
