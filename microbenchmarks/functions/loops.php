<?php

function for_empty() {
    for ($i = 0; $i < 1000; $i++) {
    }
}

function foreach_empty() {
    foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 0] as $x) {
    }
}
