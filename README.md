# Peachpie Benchmarks

This repository uses [BenchmarkDotNet](https://benchmarkdotnet.org) to benchmark impact of the transformations performed as optimizations during the compilation of PHP code by Peachpie.
To run them, perform the following steps:

1. Set the version of Peachpie SDK in ```global.json``` to the one you want to benchmark.
2. Navigate to the root folder of the solution and run the following command: ```dotnet run -c Release -p Peachpie.Benchmarks.Runner``` (or run the project in the ```Release``` configuration from Visual Studio).

After a while of benchmarking, a table similar to this one will be retrieved (the log is also stored in the ```BenchmarkDotNet.Artifacts``` folder):

```
BenchmarkDotNet=v0.12.0, OS=Windows 10.0.17763.914 (1809/October2018Update/Redstone5)
Intel Core2 Quad CPU Q9300 2.50GHz, 1 CPU, 4 logical and 4 physical cores
.NET Core SDK=3.0.100
  [Host] : .NET Core 3.0.0 (CoreCLR 4.700.19.46205, CoreFX 4.700.19.46214), X64 RyuJIT

Job=ShortRun  Toolchain=InProcessEmitToolchain  IterationCount=3
LaunchCount=1  WarmupCount=3

|              Type | Method | Configuration |         Mean |      Error |    StdDev | Ratio | RatioSD |  Gen 0 | Gen 1 | Gen 2 | Allocated |
|------------------ |------- |-------------- |-------------:|-----------:|----------:|------:|--------:|-------:|------:|------:|----------:|
| AssignCopyRemoval |    run |            O1 |    129.13 ns |  64.285 ns |  3.524 ns |  1.00 |    0.00 | 0.0408 |     - |     - |      64 B |
| AssignCopyRemoval |    run |       Release |    123.78 ns |  29.941 ns |  1.641 ns |  0.96 |    0.04 | 0.0408 |     - |     - |      64 B |
|                   |        |               |              |            |           |       |         |        |       |       |           |
|    CallableStatic |    run |            O1 |  1,511.73 ns | 184.250 ns | 10.099 ns |  1.00 |    0.00 | 0.5035 |     - |     - |     792 B |
|    CallableStatic |    run |       Release |    857.02 ns |  68.217 ns |  3.739 ns |  0.57 |    0.00 | 0.1268 |     - |     - |     200 B |
|                   |        |               |              |            |           |       |         |        |       |       |           |
...
```

Each pair of lines represents the results of calling the method ```run``` on an instance of the type given in ```Type```.
The ```Configuration``` column captures the configuration under which the project was built.
```O1``` is a configuration artificially added to ```Peachpie.Benchmarks```.
It is similar to ```Release```, but the transformations are not performed during it.
```O1``` serves as a baseline for the ```Ratio``` column to compare with the transformations turned on in the ```Release``` mode.
