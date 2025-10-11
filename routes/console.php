<?php

use App\Models\Expense;
use Illuminate\Support\Facades\Artisan;

Artisan::command('init', function () {
    Expense::truncate();

    $path = storage_path('app/private/data.csv');

    $file = fopen($path, 'r');

    if (!$file) {
        die('Could not open the CSV file.');
    }

    $csvData = [];
    while (($row = fgetcsv($file)) !== FALSE) {
        if ($row[0] === 'Timestamp') {
            $headers = collect($row)->map(fn ($s) => str($s)->snake())->toArray();
            continue;
        }

        $csvData[] = array_combine($headers, $row);
    }

    fclose($file);

    dd($csvData);
});
