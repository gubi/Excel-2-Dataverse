<?php
/**
 *
 *
 * Available parameters:
 * @var debug           Set debug mode. NOTE: debug mode does not save local files
 * @var only_fields     Display only fields in `dataset > results > data > latestVersion > metadataBlocks > citation > fields`
 * @var
 */

ini_set("memory_limit", "1G");
// if(isset($_GET["debug"])) {
    header("Content-type: text/plain");
// }

function output($data, $json = false) {
    if($json) {
        // Display the output as json
        header("Content-type: application/json");
        // print_r(json_encode($changes, JSON_PRETTY_PRINT));
        print_r(json_encode($data, JSON_PRETTY_PRINT));
    } else {
        header("Content-type: text/plain");
        print_r($data);
    }
}

require_once("vendor/autoload.php");
require_once("common/classes/Obj.php");
require_once("common/classes/Agrovoc.php");

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


define("FILENAME", "resultAgrovoc_filled_20181108.xlsx");


$parse_row = (isset($_GET["row"])) ? $_GET["row"] : null;
$check_file = (!is_null($parse_row)) ? "row_" . $parse_row : "output";

// Check whether the output file exists (speed up and separate jobs)
if(file_exists(getcwd() . "/export/{$check_file}.json")) {
    /**
    * Load the JSON exported file
    */
    $json = json_decode(file_get_contents(getcwd() . "/export/{$check_file}.json"));
    output($json, true);

    foreach($json->{FILENAME}->rows->visible->contents as $row_name => $row_data) {
        if(!isset($row_data->dataset->results->data)) {
            save($row_name . " with new keyword \"" . $row_data->_keywords->value . "\" does not exists in Dataverse\nCheck the dataset url schema!\n\n", "report");
        }
    }
} else {
    /**
    * Parse the excel file
    */
    $data = Agrovoc::parse_xml(FILENAME, $parse_row);
    output($data, true);
}
?>
