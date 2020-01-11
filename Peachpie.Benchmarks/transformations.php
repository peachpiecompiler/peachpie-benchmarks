<?php
namespace Peachpie\Benchmarks\Transformations;

class Helper {}

class Ord {
    private final function ord_string_int(string $s, int $i) {
      return ord($s[$i]);
    }

    public final function run() {
        return $this->ord_string_int("foo", 1);
    }
}

class CallableThis {
    private function bar() {
        return 42;
    }

    public final function run() {
        return call_user_func([$this, "bar"]);
    }
}
