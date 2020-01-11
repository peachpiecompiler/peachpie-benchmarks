extern alias transformations_Debug;
using TransformationsDebug = transformations_Debug::Peachpie.Benchmarks.Transformations;
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

            var debugAssembly = typeof(TransformationsDebug.Helper).Assembly;
            var releaseAssembly = typeof(TransformationsRelease.Helper).Assembly;

            // Add a method from each class in both Debug and Release version
            var cases = new List<BenchmarkCase>();
            foreach (var debugType in debugAssembly.GetTypes())
            {
                var debugMethod = debugType.GetMethod("run");
                if (debugMethod == null)
                    continue;

                var releaseType = releaseAssembly.GetType(debugType.FullName);
                var releaseMethod = releaseType.GetMethod("run");

                cases.Add(BenchmarkCase.Create(new Descriptor(debugType, debugMethod), job, parameters, config));
                cases.Add(BenchmarkCase.Create(new Descriptor(releaseType, releaseMethod), job, parameters, config));
            }

            var runInfo = new BenchmarkRunInfo(cases.ToArray(), typeof(TransformationsDebug.Helper), config);
            BenchmarkRunner.Run(runInfo);
        }
    }
}
