<?php

use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Carbon;
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
            $row[0] = 'Created At';
            $headers = collect($row)->map(fn ($s) => str($s)->snake())->toArray();
            continue;
        }

        $csvData[] = array_combine($headers, $row);
    }

    fclose($file);

    foreach ($csvData as $attributes) {
        Expense::create([
            'user_id' => User::value('id'),
            "created_at" => Carbon::parse($attributes['created_at']),
            "date" => Carbon::parse($attributes['date']),
            "description" => $attributes['description'],
            "amount" => str($attributes['amount'])->remove('$')->remove(',')->append('00')->toInteger(),
            "category" => $attributes['category'],
            "type" => $attributes['type'],
            "payment_method" => $attributes['payment_method'],
            "notes" => $attributes['notes'] ?: null,
        ]);
    }


    $this->info('Done!');
});
