param (
  [string] $dockerFilter='*',
  [string] $benchmarkFilter='*/*',
  [int] $iterationCount=100000,
  [string] $outputFile='results.json'
)

# Helper function to create a reasonably named tag for the generated image
function Get-TagName {
  param ($dockerFile)
  $baseName = $dockerFile.Basename.ToLower()
  return "microbenchmark-${basename}:latest"
}

# Gather *.Dockerfile files according to the filter
$dockerFiles = Get-ChildItem . -Filter $dockerFilter | Where-Object {$_.Name -like "*.Dockerfile"}

# Generate the docker images in question (potential re-generation is quick thanks to Docker caching)
Foreach ($dockerFile in $dockerFiles) {
  $tag = Get-TagName $dockerFile
  & docker build -f $dockerFile --tag $tag .
}

# Run the benchmarks and gather their results
$results = @{}
Foreach ($dockerFile in $dockerFiles) {
  $tag = Get-TagName $dockerFile
  $tagResultsJson = (& docker run $tag $benchmarkFilter $iterationCount) | Out-String
  $results[$dockerFile.Basename] = ConvertFrom-Json $tagResultsJson
}

# Print the merged result to the given output file
ConvertTo-Json -Compress $results | Out-File $outputFile
