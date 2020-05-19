<?php
namespace Peachpie\Benchmarks\Transformations;

class Helper {}

class DoubleNegate {
  private final function bar($a) {
    return !!$a;
  }

  public final function run() {
    return $this->bar([]);
  }
}

class MinusOneMultiply {
  private final function bar($x) {
    return -1 * $x;
  }

  public final function run() {
    return $this->bar(42);
  }
}

class EmptyRemoval {
  public final function run() {
    if (empty($x)) {
      return 42;
    } else {
      return 666;
    }
  }
}

class DirnameSimplify {
  public final function run() {
    return dirname(__FILE__);
  }
}

class CompoundAssignment {
  private final function bar($s) {
    //$s = $s . "bar";
    $s = $s + 24;
    return $s;
  }

  public final function run() {
    return $this->bar("42");
  }
}

class ParamCopyRemoval {
  private final function bar(array $a) {
    return 0;
  }

  private static $a = [1, 2, 3];

  public final function run() {
    return $this->bar(self::$a);
  }
}

class AssignCopyRemoval {
  private final function bar($a) {
    $b = $a;
    return 0;
  }

  private static $a = [1, 2, 3];

  public final function run() {
    return $this->bar(self::$a);
  }
}

class ReturnCopyRemoval {
  private final function bar($a) {
    $b = $a;
    $b[0] = 42;
    return $b;
  }

  private static $a = [1, 2, 3];

  public final function run() {
    return $this->bar(self::$a);
  }
}

class OrdStringInt {
    private final function ord_string_int(string $s, int $i) {
    return ord($s[$i]);
    }

    public final function run() {
        return $this->ord_string_int("foo", 1);
    }
}

class OrdAny {
    private final function ord_string_int($s, $i) {
    return ord($s[$i]);
    }

    public final function run() {
        return $this->ord_string_int("foo", 1);
    }
}

class TryGetItem {
  private final function bar($a) {
    return (isset($a['foo']) ? $a['foo'] : 'baz');
  }

  private static $a = ['foo' => 'bar'];

  public final function run() {
    return $this->bar(self::$a);
  }
}

define("DEFINED_CONST", "defined_value");

class DefineConst {
  public final function run() {
    return DEFINED_CONST;
  }
}

class CallableThis {
    private final function bar() {
        return 42;
    }

    public final function run() {
        return call_user_func([$this, "bar"]);
    }
}

class CallableStatic {
  public static function bar() {
    return 42;
  }

  public final function run() {
    return call_user_func([__NAMESPACE__ .'\CallableStatic', 'bar']);
  }
}

class CallableFunction {
  public final function run() {
    return call_user_func('bar');
  }
}

function bar() {
  return 42;
}
