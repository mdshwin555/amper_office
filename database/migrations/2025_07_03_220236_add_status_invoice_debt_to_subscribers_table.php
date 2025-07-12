<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('status')->default('active'); // الحالة (مؤقتة أو متوقفة أو مشترك)
            $table->decimal('invoice_value', 10, 2)->nullable(); // قيمة الفاتورة
            $table->decimal('debt_value', 10, 2)->nullable(); // قيمة الديون
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn(['status', 'invoice_value', 'debt_value']);
        });
    }
};
