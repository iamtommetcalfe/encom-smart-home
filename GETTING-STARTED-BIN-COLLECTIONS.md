# Running the Bin Collections Import Command

This guide explains how to run the `ImportBinCollectionsCommand` to import bin collection dates from a PDF file into your Encom Smart Home Web App database.

## Command Overview

The `ImportBinCollectionsCommand` is a Laravel Artisan command that:

1. Reads bin collection dates from a PDF file
2. Creates necessary database structures if they don't exist
3. Imports the collection dates into the database
4. Makes the data available for the Bin Collection widget

## How to Run the Command

### Basic Usage

To run the command with the default settings (using the bin-collections.pdf file in the root directory):

```bash
php artisan bin-collections:import
```

### Specifying a Custom PDF Path

If your PDF file is located elsewhere or has a different name, you can specify the path:

```bash
php artisan bin-collections:import /path/to/your/collection-schedule.pdf
```

## What to Expect When Running the Command

When you run the command, it will:

1. Check if the PDF file exists
2. Create the necessary database migration and model if they don't exist
3. Extract data from the PDF (currently using sample data)
4. Import the data into the database
5. Display progress information in the console

Example output:
```
Importing bin collection dates from: /Users/thomasmetcalfe/Projects/encom/bin-collections.pdf
Extracting data from PDF...
[WARNING] PDF parsing not implemented. Using sample data instead.
[WARNING] To properly implement PDF parsing, install a PDF library like "spatie/pdf-to-text" or "smalot/pdfparser".
Importing data into database...
6 records imported.
Bin collection dates imported successfully.
```

## Notes on PDF Parsing

The current implementation uses sample data instead of actually parsing the PDF. To implement proper PDF parsing:

1. Install a PDF parsing library:
   ```bash
   composer require spatie/pdf-to-text
   # OR
   composer require smalot/pdfparser
   ```

2. Modify the `extractDataFromPdf` method in the `ImportBinCollectionsCommand` class to use the library.

## Scheduling the Command

You can schedule the command to run automatically on a regular basis. To do this:

1. Uncomment the scheduling line in `app/Console/Kernel.php`:
   ```php
   $schedule->command('bin-collections:import')->weekly();
   ```

2. Set up a cron job to run Laravel's scheduler:
   ```
   * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Troubleshooting

If you encounter issues:

1. Make sure the PDF file exists at the specified path
2. Check that your database connection is configured correctly
3. Ensure you have the necessary permissions to create and modify database tables
4. If you're using a custom PDF parser, make sure the library is installed correctly

For more information on Laravel Artisan commands, see the [Laravel documentation](https://laravel.com/docs/artisan).
