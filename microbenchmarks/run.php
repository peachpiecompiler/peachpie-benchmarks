<?php

// Arguments: <benchmarkFilter> <iterationCount>
// Example:   "functions/*" 1000000

const EMPTY_BENCHMARK = 'emptyBenchmark';

const CHUNK_COUNT = 100;

$benchmarkFilter = $argv[1];
$iterationCount = $argv[2];
runAll($benchmarkFilter, $iterationCount);

function runAll($benchmarkFilter, $iterationCount) {
    // Include all the files with benchmarks according to the filter
    foreach (glob($benchmarkFilter, GLOB_BRACE) as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
            require_once $file;
        }
    }

    // Find all the benchmarking functions in the included files
    $benchmarks = [];
    foreach (get_defined_functions()['user'] as $fnName) {
        $fn = new ReflectionFunction($fnName);

        // From this file, include only EMPTY_BENCHMARK
        if ($fn->getFileName() == __FILE__ && $fn->getName() != EMPTY_BENCHMARK) {
            continue;
        }

        // Exclude helper functions starting with _
        if ($fn->getShortName()[0] == '_') {
            continue;
        }

        $benchmarks[] = $fn->getName();
    }

    // Warm-up opcache, JIT, call-sites etc.
    foreach ($benchmarks as $benchmark) {
        runSingle($benchmark, 1);
    }

    // Run benchmark themselves and gather the rough results
    $results = [];
    foreach ($benchmarks as $benchmark) {
        // Perform all the measurements in chunks
        $chunkIterationCount = $iterationCount / CHUNK_COUNT;       // TODO: Consider finding the iteration count dynamically
        $chunkAvgs = [];
        for ($i = 0; $i < CHUNK_COUNT; $i++) {
            $chunkAvgs[] = runSingle($benchmark, $chunkIterationCount) / $chunkIterationCount;
        }

        // Find average time
        $avg = array_sum($chunkAvgs) / count($chunkAvgs);

        $results[$benchmark] = [
            'iterations' => $iterationCount,
            'rough_avg_ns' => $avg,
            'rough_std_dev_ns' => getStandardDeviation($chunkAvgs, $avg)
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
    $start = hrtime(true);

    // Perform the operation repeatively, measuring the total time of the whole batch
    for ($i = 0; $i < $iterationCount; $i++) {
        $benchmark();
    }

    return hrtime(true) - $start;
}

function getStandardDeviation($values, $avg) {
    $variance = 0.0;
    foreach ($values as $value) {
        $variance += ($value - $avg) * ($value - $avg);
    }

    return sqrt($variance / count($values));
}

function emptyBenchmark() {}
