<?php

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('category_id')->after('user_id')->nullable()->constrained();
        });

        DB::transaction(function () {
            Expense::each(function (Expense $expense) {
                $expense->update([
                    'category_id' => Category::where('name', $expense->category)->value('id'),
                ]);
            });
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
