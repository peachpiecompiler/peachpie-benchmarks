param (
  [string] $resultjson='results.json',
  [string] $outputFile='results.html'
)

"
<script src='https://cdn.jsdelivr.net/npm/apexcharts'></script>
<div id='chart'></div>" |
  Out-File -FilePath $outputFile

$results = Get-Content -Raw -Path $resultjson | ConvertFrom-Json

foreach ($r in $results.PSObject.Properties) {
    
    $categories = @() # tests quoted
    $tests = @() # test names
    foreach ($t in $r.Value.PSObject.Properties) {
        $categories += """" + $t.Name + """"
        $tests += $t.Name
    }

    break;
}

# tests results

$series = @() # data

foreach ($r in $results.PSObject.Properties) {
  $row = "
    name: ""$($r.Name)"",
    data: ["

  foreach ($t in $tests) {
    $row += $r.Value.$t.time_ms
    $row += ","
  }
  $row += "]"

  $series += ,$row
}

##

"<script>
  var options = {
    series: [" |
  Out-File -FilePath $outputFile -Append

foreach ($row in $series) {
   "{$row}," | Out-File -FilePath $outputFile -Append
}

"     ],
        chart: {
          type: 'bar',
          height: 430
        },
        plotOptions: {
          bar: {
            horizontal: true,
            dataLabels: {
              position: 'top',
            },
          }
        },
        dataLabels: {
          enabled: true,
          offsetX: -6,
          style: {
            fontSize: '12px',
            colors: ['#fff']
          }
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['#fff']
        },
        xaxis: {
          categories: [$([system.String]::Join(",", $categories))],
    },
  };
  var chart = new ApexCharts(document.querySelector('#chart'), options);
  chart.render();
</script>
" |
  Out-File -FilePath $outputFile -Append
