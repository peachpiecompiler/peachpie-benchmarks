<?php

// Arguments: <benchmarkFilter> <iterations>
// Example:   "functions/*" 1000000

class Runner
{
    const CHUNK = 100;
    const EMPTY_FUNCTION = "empty_func";

    static function run(string $benchmarkFilter, int $iterations): array
    {
        $benchmarks = self::collectBechmarks($benchmarkFilter);
        $results = [];

        // run benchmarks, collect results
        foreach ($benchmarks as $benchmark) {

            // warmup
            $time = self::runSingle($benchmark, self::CHUNK);

            //
            $time = self::runSingle($benchmark, $iterations);
            $times = [];

            $blocks = $iterations / self::CHUNK;
            for ($i = 0; $i < $blocks; $i++)
            {
                $times[] = self::runSingle($benchmark, self::CHUNK) * $blocks;
            }

            sort($times);

            $results[$benchmark->getName()] =
            [
                "time" => $time,
                //"avg" => $time / $iterations,
                "min" => $times[0],
                "max" => end($times),
                "med" => $times[count($times) / 2],
            ];
        }

        // Clean up the results by calculating the overhead for test runs and deducing it
        $empty = $results[self::EMPTY_FUNCTION]["time"];
        foreach ($results as $name => $r)
        {
            $results[$name] =
            [
                "iterations" => $iterations,
                "time_ms" => (int)(($r["time"] - $empty) / 1000000),
                //"avg_ms" => (int)(($r["time"] - $empty) / $iterations / 1000000 ),
                "min_ms" => (int)(($r["min"] - $empty) / 1000000 ),
                "max_ms" => (int)(($r["max"] - $empty) / 1000000 ),
                "med_ms" => (int)(($r["med"] - $empty) / 1000000 ),
            ];
        }

        //
        return $results;
    }

    static function collectBechmarks(string $benchmarkFilter)
    {
        // Include all the files with benchmarks according to the filter
        $files = [];
        foreach (glob($benchmarkFilter, GLOB_BRACE) as $file)
        {
            if (pathinfo($file, PATHINFO_EXTENSION) == "php")
            {
                if (false !== require_once $file)
                {
                    $files[] = realpath($file);
                }
            }
        }

        // Find all the benchmarking functions in the included files
        $benchmarks = [
            new ReflectionFunction(self::EMPTY_FUNCTION)
        ];

        foreach (get_defined_functions()['user'] as $fnName)
        {
            $fn = new ReflectionFunction($fnName);

            if (in_array($fn->getFileName(), $files) && $fn->getShortName()[0] != '_')
            {
                $benchmarks[] = $fn;
            }
        }

        //
        return $benchmarks;
    }

    private static function runSingle(ReflectionFunction $benchmark, int $iterations): int {
        $start = hrtime(true);

        // Perform the operation repeatively,
        // measuring the total time of the whole batch
        for ($i = 0; $i < $iterations; $i++) {
            $benchmark->invoke(null);
        }

        return hrtime(true) - $start;
    }

    private static function getStandardDeviation($values, $avg) {
        $variance = 0.0;
        foreach ($values as $value) {
            $variance += ($value - $avg) * ($value - $avg);
        }

        return sqrt($variance / count($values));
    }
}

function empty_func()
{
}

//
echo json_encode(Runner::run(@$argv[1] ?? "*/*", @$argv[2] ?? 100000));
