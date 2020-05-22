# Microbenchmarks

Apart from `run.php`, all the functions in PHP files in this directory are subject to benchmarking in the environments defined in `.Dockerfile`s.
To run the benchmarks, execute the following script in Powershell:

```
./Run.ps1 [dockerFilter] [benchmarkFilter] [iterationCount] [outputFile]
```

- `dockerFilter`: Glob pattern to match definitions of docker images (only those ending with `.Dockerfile` are considered). Default: `"*"`
- `benchmarkFilter`: Glob pattern to match PHP files containing the benchmarks to run (only those ending with `.php` are considered). It's possible to use `{benchmark,benchmark2}` for selection. Default: `"*/*"`
- `iterationCount`: How many times each of the benchmarks should run. Default: `100000`
- `outputFile`: Where to store the JSON with the results. Default: `results.json`

## Adding new benchmark

Just add a `.php` file in any subfolder.
If matched by the filter, it will be included and all its functions will be considered as benchmarks.
To prevent a helper function from being selected as one, start its name by `_`, e.g. `_bar`.

## Adding new environment

Add a new `.Dockerfile` with an `ENTRYPOINT` so that the arguments to `run.php` can be passed directly to `docker run`.
