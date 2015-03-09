<?php

class WebPage {
    function beforeRoute() {
            echo 'in WebPage::beforeRoute()<br>';
    }
    function display() {
            echo 'Some text About this tool';
    }
    function bar() {
            echo 'bar()';
    }
}

?>
