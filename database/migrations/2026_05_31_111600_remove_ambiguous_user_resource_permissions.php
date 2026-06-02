<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')
            ->whereIn('name', [
                'ViewAny:User',
                'View:User',
                'Create:User',
                'Update:User',
                'Delete:User',
                'DeleteAny:User',
                'Restore:User',
                'RestoreAny:User',
                'ForceDelete:User',
                'ForceDeleteAny:User',
                'Replicate:User',
                'Reorder:User',
            ])
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
