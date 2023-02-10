<?php
require "class/itemLoader.php";

//define('PAGE_SIZE', intval(MEMORY_AVAILABLE/AVERAGE_OBJ_SIZE));
define('PAGE_SIZE', 5);
define('DEBUG', TRUE);

$itemLoader = new ItemLoader;

foreach ($itemLoader as $treatmentResult) {
    if (is_object($treatmentResult) && $treatmentResult instanceof parallel\Future) {
        // ->value() Shall return (and if necessary wait for)
        // try to call value() as late as possible
        print_r($treatmentResult->value());
        //release memory
        unset($treatmentResult);
    }
}
// really poor debug tool :)
function my_print($msg){
    if (DEBUG) {
        echo $msg . "\n";
    }
}