
<script src='https://cdn.jsdelivr.net/npm/apexcharts'></script>
<div id='chart'></div>

> [Source repository](https://github.com/peachpiecompiler/peachpie-benchmarks)


<script>
  var options = {
    series: [
{
    name: "php-7.4.6-cli",
    data: [89,124,2,8880,277,338,395,196,149,]},
   ],
    chart: {
      type: 'bar',
      height: 430
    },
    plotOptions: {
      bar: {
        horizontal: true,
        dataLabels: {
          position: 'bottom'
        },
      },
    },
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) { return val + ' ms' },
      offsetX: 10,
      style: {
        fontSize: '12px',
        colors: ['#222']
      }
    },
    stroke: {
      show: false,
      width: 1,
      colors: ['#fff']
    },
    xaxis: {
      categories: ["call_user_func_string","call_user_func_object","empty_call","for_empty","foreach_empty","pcre_is_match","pcre_matches","number_ops","string_concat_1"],
    },
    yaxis: {
      min: 0,
    }
  };
  var chart = new ApexCharts(document.querySelector('#chart'), options);
  chart.render();
</script>

