<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $t) {
            $t->integer('discount_points')->default(0)->after('total_price');
            $t->integer('discount_amount')->default(0)->after('discount_points');
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $t) {
            $t->dropColumn(['discount_points','discount_amount']);
        });
    }
};
