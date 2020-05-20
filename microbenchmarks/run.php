<?php

// Arguments: <dir1>/<benchmarkName1>[,..,<dirN>/<benchmarkNameN>] [iterationCount]
// Example:   functions/call_user_func_001 1000000

const EMPTY_BENCHMARK = 'basic/nothing';
const DEFAULT_ITERATION_COUNT = 100000;

$benchmarks = explode(",", $argv[1]);
$iterationCount = $argv[2] ?? DEFAULT_ITERATION_COUNT;
runAll($benchmarks, $iterationCount);

function runAll($benchmarks, $iterationCount) {
    // Always include the empty benchmark to measure the overhead
    if (!in_array(EMPTY_BENCHMARK, $benchmarks)) {
        $benchmarks[] = EMPTY_BENCHMARK;
    }

    // Include files, warm-up opcache, JIT, call-sites etc.
    foreach ($benchmarks as $benchmark) {
        require_once __DIR__ ."/". $benchmark .".php";
        runSingle($benchmark, 1);
    }

    // Run benchmark themselves and gather the rough results
    $results = [];
    foreach ($benchmarks as $benchmark) {
        $avg = runSingle($benchmark, $iterationCount);    // TODO: Consider finding the iteration count dynamically
        $results[$benchmark] = [
            'iterations' => $iterationCount,
            'rough_avg_ns' => $avg
        ];
    }

    // Clean up the results by calculating the overhead for test runs and deducing it
    $overheadAvg = $results[EMPTY_BENCHMARK]['rough_avg_ns'];
    foreach ($benchmarks as $benchmark) {
        $results[$benchmark]['clean_avg_ns'] = $results[$benchmark]['rough_avg_ns'] - $overheadAvg;
    }

    echo json_encode($results);
}

function runSingle($benchmark, $iterationCount) {
    // Convert the benchmark name to its namespace and call the function run()
    $ns = str_replace("/", "\\", $benchmark);
    $fn = $ns ."\\run";

    $start = hrtime(true);

    // Perform the operation repeatively, measuring the total time of the whole batch
    for ($i = 0; $i < $iterationCount; $i++) {
        $fn();
    }

    $duration = hrtime(true) - $start;
    $avg = $duration / $iterationCount;
    return $avg;
}
