<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weekly_bills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');

            $table->date('week_start');
            $table->date('week_end');

            $table->decimal('old_reading', 8, 2);
            $table->decimal('new_reading', 8, 2);
            $table->decimal('consumption', 8, 2);
            $table->decimal('price_per_kwh', 8, 2);
            $table->decimal('amount_due', 10, 2);

            $table->boolean('paid')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_bills');
    }
};
