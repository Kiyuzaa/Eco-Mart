<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'points')) {
                $t->unsignedInteger('points')->default(0);
            }
        });

        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders', 'points_awarded')) {
                $t->boolean('points_awarded')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (Schema::hasColumn('orders', 'points_awarded')) {
                $t->dropColumn('points_awarded');
            }
        });

        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users', 'points')) {
                $t->dropColumn('points');
            }
        });
    }
};
