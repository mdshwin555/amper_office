<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('weekly_bills', function (Blueprint $table) {
        $table->decimal('discount_per_kwh', 10, 2)->default(0);
        $table->decimal('discount_total', 10, 2)->default(0);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_bills', function (Blueprint $table) {
            //
        });
    }
};
