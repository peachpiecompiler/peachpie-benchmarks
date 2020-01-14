extern alias transformations_O1;
using TransformationsO1 = transformations_O1::Peachpie.Benchmarks.Transformations;
using TransformationsRelease = Peachpie.Benchmarks.Transformations;

using System;
using System.Collections.Generic;
using System.Diagnostics;
using BenchmarkDotNet.Attributes;
using BenchmarkDotNet.Jobs;
using BenchmarkDotNet.Parameters;
using BenchmarkDotNet.Running;
using BenchmarkDotNet.Configs;
using BenchmarkDotNet.Reports;
using BenchmarkDotNet.Toolchains.InProcess.Emit;
using BenchmarkDotNet.Diagnosers;
using BenchmarkDotNet.Columns;
using BenchmarkDotNet.Exporters;

namespace Peachpie.Benchmarks
{
    class Program
    {
        static void Main(string[] args)
        {
            // Initialize common settings
            var job = Job.ShortRun
                .With(InProcessEmitToolchain.Instance)  // The current .NET Core toolchain requires the DLLs to be named exactly as their projects
                .WithBaseline(true);
            var parameters = new ParameterInstances(Array.Empty<ParameterInstance>());
            var config = DefaultConfig.Instance
                .With(ConfigOptions.DisableOptimizationsValidator)
                .With(MemoryDiagnoser.Default)
                .With(TargetMethodColumn.Type)
                .With(BenchmarkLogicalGroupRule.ByCategory)
                .CreateImmutableConfig();

            var o1Assembly = typeof(TransformationsO1.Helper).Assembly;
            var releaseAssembly = typeof(TransformationsRelease.Helper).Assembly;

            // Add a method from each class in both O1 (optimized, but without transformations) and Release version
            var cases = new List<BenchmarkCase>();
            foreach (var o1Type in o1Assembly.GetTypes())
            {
                var o1Method = o1Type.GetMethod("run");
                if (o1Method == null)
                    continue;

                var releaseType = releaseAssembly.GetType(o1Type.FullName);
                var releaseMethod = releaseType.GetMethod("run");

                cases.Add(BenchmarkCase.Create(new Descriptor(o1Type, o1Method), job, parameters, config));
                cases.Add(BenchmarkCase.Create(new Descriptor(releaseType, releaseMethod), job, parameters, config));
            }

            var runInfo = new BenchmarkRunInfo(cases.ToArray(), typeof(TransformationsO1.Helper), config);
            BenchmarkRunner.Run(runInfo);
        }
    }
}
