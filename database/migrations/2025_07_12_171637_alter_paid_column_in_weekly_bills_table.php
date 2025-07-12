<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('weekly_bills', function (Blueprint $table) {
            $table->decimal('paid', 12, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('weekly_bills', function (Blueprint $table) {
            $table->integer('paid')->change(); // رجّعها للنوع القديم إذا حبيت ترجّع المايغريشن
        });
    }
};
