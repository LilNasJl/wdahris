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
        Schema::table('leaves', function (Blueprint $table): void {
            if (! Schema::hasColumn('leaves', 'status_updated_at')) {
                $table->timestamp('status_updated_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('leaves', 'deducted_points')) {
                $table->decimal('deducted_points', 5, 2)->default(0)->after('status_updated_at');
            }

            if (! Schema::hasColumn('leaves', 'is_lwop')) {
                $table->boolean('is_lwop')->default(false)->after('deducted_points');
            }

            if (! Schema::hasColumn('leaves', 'approval_comment')) {
                $table->text('approval_comment')->nullable()->after('is_lwop');
            }

            if (! Schema::hasColumn('leaves', 'approved_checked_by_hr')) {
                $table->string('approved_checked_by_hr')->nullable()->after('approval_comment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table): void {
            foreach ([
                'approved_checked_by_hr',
                'approval_comment',
                'is_lwop',
                'deducted_points',
                'status_updated_at',
            ] as $column) {
                if (Schema::hasColumn('leaves', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
