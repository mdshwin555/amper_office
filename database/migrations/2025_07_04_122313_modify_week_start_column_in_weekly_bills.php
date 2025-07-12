<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyWeekStartColumnInWeeklyBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_bills', function (Blueprint $table) {
            // تعديل الحقل week_start ليقبل القيم الفارغة
            $table->date('week_start')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weekly_bills', function (Blueprint $table) {
            // إعادة الحقل إلى حالته السابقة ليصبح non-nullable
            $table->date('week_start')->nullable(false)->change();
        });
    }
}
