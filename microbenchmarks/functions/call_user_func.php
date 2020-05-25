<?php

function _bar() {}

function call_user_func_test() {
    call_user_func(__NAMESPACE__ ."\\_bar");
}
