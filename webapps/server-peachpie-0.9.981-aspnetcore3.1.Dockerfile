FROM mcr.microsoft.com/dotnet/core/sdk:3.1

ARG app

COPY ./config/peachpie /peachpie

COPY ./apps/$app /app
WORKDIR /app
RUN ["/app/install.sh", "/peachpie/Website"]

WORKDIR /peachpie
RUN echo "{ \"msbuild-sdks\": { \"Peachpie.NET.Sdk\": \"0.9.981\" } }" > global.json
RUN dotnet restore
RUN dotnet build -c Release

WORKDIR /peachpie/Server
ENTRYPOINT ["dotnet", "run", "--no-build", "-c", "Release"]
