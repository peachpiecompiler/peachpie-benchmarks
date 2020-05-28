# Web Application Benchmarks

All the web applications available for benchmarking are stored under the `apps` directory.
Each of the `server-*.Dockerfile`s contains a definition of an environment and a webserver to run any of the applications. The `config` directory contains helper files for the servers.
The `client.Dockerfile` defines a simple client which measures the throughput of the web applications using Apache Bench.
To execute the benchmarks, run the following script in Powershell:

```
./run.ps1 [serverDockerFilter] [appFilter] [seconds] [concurrency] [outputFile]
```

- `serverDockerFilter`: Glob pattern to match definitions of server docker images (only those matching `server-*.Dockerfile` are considered). Default: `"server-*"`
- `appFilter`: Glob pattern to match the web application subdirectories in the `apps` directory. Default: `"*"`
- `seconds`: How long to run each test while measuring the number of the processed requests. Default: `30`
- `concurrency`: How many requests to span concurrently, i.e. how many pending requests can there be in one time. Default: `20`
- `outputFile`: Where to store the JSON with the results. Default: `results.json`

## Adding new application

Add a new subdirectory to the `apps` directory with the following contents (see `apps/helloworld` for the simplest example):

- `benchmarkConfig.json`: Configuration of benchmark parameters, simple JSON object with the following property (might be extended in the future):
    - `requestPath`: The path portion of a HTTP request with the leading `/`, e.g. `"/hello.php"`.
- `install.sh`: A shell script to be executed when building server docker images. It is expected to be run from the current directory and obtain a directory to put the application as the only command line argument.
- *TODO: optional `.Dockerfile` for database.*
- Any other files used to successfuly initialize the application, such as PHP source files.

## Adding new webserver

Add a new `server-*.Dockerfile` which accepts a single build argument `app`, installs it using `install.sh` and enables to serve HTTP request to it on port `80` after startup.
See the existing `.Dockerfile`s for reference.
