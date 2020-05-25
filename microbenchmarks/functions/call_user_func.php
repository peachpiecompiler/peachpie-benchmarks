<?php

function _bar() {}

function call_user_func_string() {
    call_user_func(__NAMESPACE__ ."\\_bar");
}

class CallableObject {
    function __invoke() { }
}

function call_user_func_object() {
    call_user_func(new CallableObject);
}
