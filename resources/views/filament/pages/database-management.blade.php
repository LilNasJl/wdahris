<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">Database Backup</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Download a SQL backup of the current database before maintenance or deployment.
            </p>
        </div>

        <div class="rounded-lg border border-danger-200 bg-white p-5 shadow-sm dark:border-danger-900 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">Clear Database</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Clears operational records and keeps system access, permissions, sessions, and audit history available.
            </p>
        </div>
    </div>
</x-filament-panels::page>
