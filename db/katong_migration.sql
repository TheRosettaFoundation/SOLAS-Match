# Katong TMS integration: changes to an existing TWB Platform database.
# Run once, BEFORE re-running the Katong section of db/schema.sql (tables, procedures and task type 40).
# Katong ids are 36 character UUIDs, so the Phrase uid columns are widened from 30 to 40 characters.

ALTER TABLE MemsourceProjects MODIFY memsource_project_uid VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE MemsourceProjects MODIFY created_by_uid        VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE MemsourceProjects MODIFY owner_uid             VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE MemsourceProjects ADD    tms                   INT NOT NULL DEFAULT 0 AFTER workflow_level_12; # 0 => Phrase, 1 => Katong

ALTER TABLE MemsourceTasks MODIFY memsource_task_uid VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE MemsourceTasks MODIFY task               VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE MemsourceTasks MODIFY internalId         VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE ProcessedMemsourceTaskUIDs     MODIFY memsource_task_uid VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE memsource_statuses             MODIFY memsource_task_uid VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE queue_copy_task_original_files MODIFY memsource_task_uid VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE post_analysis_s    MODIFY memsource_project_uid VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE compare_analysis_s MODIFY memsource_project_uid VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL;

# Then re-create these procedures from db/schema.sql (their uid parameters are now VARCHAR(40)):
# set_memsource_project, update_memsource_project_owner, get_memsource_project_by_memsource_uid,
# set_memsource_task, update_memsource_task, get_memsource_task_by_memsource_uid, is_job_uid_already_processed,
# get_memsource_tasks_for_project_language_type, get_memsource_tasks_for_project_internal_id_type,
# set_memsource_status, queue_copy_task_original_file, insert_post_analysis, insert_compare_analysis
# and run the "Katong TMS integration" section near the end of db/schema.sql.
