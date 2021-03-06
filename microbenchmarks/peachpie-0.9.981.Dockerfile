FROM mcr.microsoft.com/dotnet/sdk:5.0

COPY . /bench
WORKDIR /bench

RUN echo "{ \"msbuild-sdks\": { \"Peachpie.NET.Sdk\": \"0.9.981\" } }" > global.json
RUN dotnet build -c Release

ENTRYPOINT ["dotnet", "run", "--no-build", "-c", "Release"]
