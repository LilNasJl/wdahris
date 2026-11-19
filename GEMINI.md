# Project Guidelines & Rules

## Project Location
`\\wsl.localhost\Ubuntu\var\www\html\wdahris` (WSL: `/var/www/html/wdahris`)

---

# DATABASE RESTRICTION

### Database Data Protection Requirement

For all future **changes, updates, new features, database migrations, and system improvements**, existing database data must be preserved.

* **Do not use destructive migration commands** such as:
  ```bash
  php artisan migrate:fresh
  php artisan migrate:fresh --seed
  php artisan migrate:reset
  ```

* **Do not**:
  * Drop all tables
  * Truncate existing tables
  * Clear the entire database
  * Delete all existing records
  * Recreate the database from scratch
  * Remove production or existing development data just to support a new feature

* Before making any database-related change, first **review the existing schema, relationships, records, foreign keys, and dependencies**.

* New features must use **safe and non-destructive migrations** whenever possible.
  For example, prefer:
  ```php
  Schema::table('employees', function (Blueprint $table) {
      $table->string('new_field')->nullable();
  });
  ```
  instead of recreating the entire table or database.

* When modifying an existing column, relationship, or table, verify that the change will not:
  * Remove existing records
  * Overwrite stored values
  * Break foreign-key relationships
  * Reset IDs
  * Destroy historical records
  * Affect existing transactions or reports

* If existing data needs to be transformed for a new feature, use a controlled migration or data-update process that preserves the original records whenever possible.

---

### Mandatory Rule Before Destructive Operations

If a requested feature appears to require deleting, resetting, or recreating database data:

```text
STOP
   ↓
Review the existing database
   ↓
Find a non-destructive solution first
   ↓
If destruction is truly unavoidable
   ↓
Explain exactly what data would be affected
   ↓
Request explicit approval before proceeding
```

Do **not** automatically clear the database even if doing so would make implementation easier.

---

### Core Rule

```text
Existing Database Data = Must Be Preserved

New Feature
     ↓
Review Existing Database
     ↓
Create Safe Migration
     ↓
Preserve Existing Records
     ↓
Test Compatibility
     ↓
Apply Update
```

This restriction applies even during major feature additions or structural database changes. Existing data should always be treated as persistent and important unless explicitly authorized by the user.
