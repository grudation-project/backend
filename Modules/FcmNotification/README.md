# 🐛 Lucid ORM Migration Sorting Issue with Multiple Directories

## Summary

When using multiple migration directories with Lucid ORM and enabling the `naturalSort` option, migration files are **not sorted globally** across all directories. Instead, they are sorted **individually per directory**, and the files are then concatenated in the order the directories are listed in the configuration.

This causes issues in modular applications where migrations from different modules are timestamped to run in a specific order but end up executing in an incorrect sequence.

---

## Reproduction

Given the following migration config in `.adonisrc.ts` or `config/database.ts`:

```ts
migrations: {
  naturalSort: true,
  paths: [
    'database/migrations',
    'modules/auth/database/migrations',
    'modules/city/database/migrations',
    'modules/client/database/migrations',
    'modules/gem/database/migrations',
    'modules/interest/database/migrations',
    'modules/markable/database/migrations',
    'modules/media/database/migrations',
    'modules/new/database/migrations',
    'modules/personality/database/migrations',
    'modules/trait/database/migrations',
    'modules/vendor/database/migrations',
  ],
}
```

And example migration files across modules:

```
modules/auth/database/migrations/2024_01_01_create_users.ts
modules/city/database/migrations/2024_01_02_create_cities.ts
modules/client/database/migrations/2024_01_03_create_clients.ts
modules/vendor/database/migrations/2024_01_01_create_vendors.ts
```

Expected behavior:  
All files should be sorted globally by filename (timestamp), regardless of which directory they come from.

Actual behavior:  
Each folder is sorted individually, and then the results are combined in the order they appear in the `paths` array.

---

## Why this is a problem

In modular architectures, migration files are timestamped intentionally to control the migration order. Failing to globally sort them may cause:

- Dependency violations (e.g., foreign key constraints not satisfied)
- Inconsistent schema generation
- Application errors during deployment or CI/CD

---

## Suggested Fix

To fix this issue:

1. Read and gather **all files from all defined directories** into a single array
2. Apply sorting (natural or normal) **after merging**, not per directory
3. Return the fully sorted list for execution

---

## Final Notes

This change would allow developers to organize their migrations modularly **without losing control over execution order**, which is essential in large, structured applications.

Happy to help contribute a fix if needed!
