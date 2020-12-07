# About

This project exposes a command that will process a local or remote XML file and push the data of that XML file to a Google Spreadsheet via the [Google Sheets API](https://developers.google.com/sheets/)

## Set up

1) As mentioned before, we use Google Sheets API. We need to provide a Service Account configuration in order to connect to the API via the console command. More info about [creating the service account here](https://github.com/googleapis/google-api-php-client/blob/master/docs/oauth-server.md#creating-a-service-account). Once you downloaded the json file, move it to the project root and rename it to `service-account.json` (or rename `GOOGLE_AUTH_CONFIG_FILE` variable in `.env`).

2) If you want to read XML files from a remote FTP, you need to fill the required env variables in `.env`: `XML_FILES_HOST`, `XML_FILES_USER` and `XML_FILES_PASSWORD`.

3) Docker

    Build docker image:
    ```
    docker build -t xml-to-google-sheet-image .
    ```
    
    Run image:
    ```
    docker run --name xml-to-google-sheet xml-to-google-sheet-image
    ```

4) Tests

    You can check that all tests are passing:
    ```
    docker exec xml-to-google-sheet vendor/bin/phpunit
    ```

5) Run command

    For local XML file:
    ```
    docker exec xml-to-google-sheet bin/console push:xml:google-sheet --source local data/coffee_feed.xml
    ```
    
    For remote(FTP) XML file:
    ```
    docker exec xml-to-google-sheet bin/console push:xml:google-sheet --source remote coffee_feed.xml
    ```

## Considerations

* Google Sheets and Drive API's have to be enabled (for the purpose of this project, we need Drive's API to give permissions to the sheet, so anyone can read and we can check everything is ok). In a real world scenario we probably wouldn't need it.

* I created some factories for Google services (like Sheets and Drive) so I can inject the dependency directly and not having to do a new in the `XmlExporterToGoogleSheet` constructor. That way [tests are easier](tests/Unit/Infrastructure/XmlExporter/XmlExporterToGoogleSheetTest.php).

* Normally it's not a good idea to test any service from Infrastructure (such as repositories). That's because we shouldn't test the interaction with an external components. But the case in `XmlExporterToGoogleSheet` is special as we have some export logic (write headers, data, etc).

* I assumed that, when we want to use the command to transform XML's from the FTP, we would use always _the same FTP_. That's why I added some env variables (`XML_FILES_HOST`, `XML_FILES_USER`, `XML_FILES_PASSWORD`).

## Logs

Whenever made sense, I added some logs. That is a good practice to follow as it helps
everyone involved in a project to find out why something is happening, if it should be that
way/it's a bug and to solve any issues.

I used them at application level (in the created command). In case of anything is not going as expected in domain logic services or infrastructure, I think it's better to trigger an exception and later, in an upper level, catch it and log what happened.

Errors in dev environment are written in `var/log/dev_errors.log`

## To optimize:

* I'm using file_get_contents to get the XML content. That's fine with small files, but in case we have the need to read bigger files we should consider reading by chunks of data.

* In a real world scenario we probably would want to access different FTP's. We could use the strategy pattern to easily inject the FTP configuration needed (given some command argument).
