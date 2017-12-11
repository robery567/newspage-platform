Deployment instructions
=======================

When deploying the new site structure, do the following steps:

- MySQL Workbench:
    - [ ] SELECT users FROM old_users_table => export result set AS JSON to `PROJECT_ROOT/app/Import/Data/users.json`
    - [ ] SELECT id, legacy_code FROM old_article_table => export result set AS JSON to `PROJECT_ROOT/app/Import/Data/legacy_article.json`
    - [ ] EXECUTE EACH SQL RESULT SET FROM `PROJECT_ROOT/app/Export/*.sql` TO `PROJECT_ROOT/app/Import/SQL/*.sql` 