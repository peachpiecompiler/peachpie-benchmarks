FROM mcr.microsoft.com/dotnet/core/sdk:3.1

COPY . /bench
WORKDIR /bench

RUN echo "{ \"msbuild-sdks\": { \"Peachpie.NET.Sdk\": \"0.9.981\" } }" > global.json
RUN dotnet restore
RUN dotnet build -c Release

ENTRYPOINT ["dotnet", "run", "-c", "Release"]
