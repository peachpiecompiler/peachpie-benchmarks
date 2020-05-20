<?php
namespace functions\call_user_func_001;

function bar() {}

function run() {
    call_user_func(__NAMESPACE__ ."\\bar");
}
