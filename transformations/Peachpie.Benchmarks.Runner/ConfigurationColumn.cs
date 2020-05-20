using BenchmarkDotNet.Columns;
using BenchmarkDotNet.Reports;
using BenchmarkDotNet.Running;
using System;
using System.Collections.Generic;
using System.IO;
using System.Text;

namespace Peachpie.Benchmarks.Runner
{
    /// <summary>
    /// Provides the name of the configuration under which the assembly of the particular method was compiled.
    /// Expects the assembly name to be of the form <c>My.Assembly.Name.ConfigurationName.dll</c>.
    /// </summary>
    class ConfigurationColumn : IColumn
    {
        public static readonly ConfigurationColumn Instance = new ConfigurationColumn();

        private ConfigurationColumn() {}

        public string Id => nameof(ConfigurationColumn);

        public string ColumnName => "Configuration";

        public bool AlwaysShow => false;

        public ColumnCategory Category => ColumnCategory.Job;

        public int PriorityInCategory => 1;

        public bool IsNumeric => false;

        public UnitType UnitType => UnitType.Dimensionless;

        public string Legend => "";

        public string GetValue(Summary summary, BenchmarkCase benchmarkCase)
        {
            string assemblyPath = benchmarkCase.Descriptor.Type.Assembly.Location;
            var assemblyNameParts = Path.GetFileNameWithoutExtension(assemblyPath).Split('.');

            if (assemblyNameParts.Length > 1)
            {
                // MyAssembly.Debug.dll
                return assemblyNameParts[assemblyNameParts.Length - 1];
            }
            else
            {
                // MyAssembly.dll
                return "";
            }
        }

        public string GetValue(Summary summary, BenchmarkCase benchmarkCase, SummaryStyle style) => GetValue(summary, benchmarkCase);

        public bool IsAvailable(Summary summary) => true;

        public bool IsDefault(Summary summary, BenchmarkCase benchmarkCase) => false;
    }
}
