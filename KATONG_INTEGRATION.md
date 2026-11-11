# Katong TMS integration

Katong can be used instead of Phrase TMS, chosen per organisation. Projects created for a flagged
organisation go to Katong. All other organisations keep using Phrase, with no change.

## Setup

1. **Database.** On an existing database, run `db/katong_migration.sql`. Then re-create the
   procedures it lists and run the "Katong TMS integration" section near the end of `db/schema.sql`.
   That section adds:
   - the tables `katong_orgs`, `katong_projects`, `katong_webhook_deliveries` and `katong_task_metrics`;
   - their procedures;
   - task type 40, "SME Review".
2. **Default user.** Create a TWB user, for example "Katong MT", to be the (machine) translator.
3. **Config.** Fill in the `[katong]` section of `Common/conf/conf.ini` (see `conf.template.ini`):
   - `api_url`, `base_url`, `api_key` and `partner_id`;
   - `default_user_id`, the id of the user from step 2.
4. **Webhook.** As a site admin, open `/katong_register_webhook`. This registers
   `{site.location}katong_hook` with Katong. Copy the secret it shows into `webhook_secret`.
   The secret is shown only once.
5. **Organisation.** As a site admin, open the organisation page and switch on
   "Use Katong TMS instead of Phrase for new projects".

## How it works

- **Project creation.**
  - Each target language gets its own workflow, chosen on the create form:
    - Translation only;
    - Translation and Revision;
    - Translation, Revision and SME Review.
  - One Katong job is created per project. It covers all target languages, with `review_langs` and
    `sme_langs` set from those choices.
- **Data stored.** Data is stored in the same tables as for Phrase. `MemsourceProjects.tms = 1`
  marks a Katong project.
  - `MemsourceProjects`:
    - `workflow_level_1..3` are always `Translation`, `Revision`, `SME Review`;
    - `memsource_project_uid` is the Katong job id.
  - `MemsourceTasks`:
    - `workflowLevel` is 1 for the Katong `mt` task, 2 for `review` and 3 for `sme_review`;
    - `memsource_task_uid` is the Katong task id;
    - `task` is the Katong target language.
- **Translation tasks.** They are done by machine translation. They are claimed by
  `katong.default_user_id` and completed by the Katong `task.status_changed` webhook.
- **Claims and deadlines.**
  - Claiming a task assigns it in Katong.
  - Unclaiming assigns it to the default user, because Katong cannot unassign.
  - Deadline changes are sent to Katong when the task is (re)assigned.
- **Work URL.** `{katong.base_url}work/{katong_task_id}`. Linguists sign in with SAML.
- **Download.** The output keeps the original file name. If Katong changed the format, the
  extension changes too, for example a PDF comes back as `.docx`.
- **Results.** When a Revision or SME Review task completes:
  - Its payable time and word counts by match band are saved in `katong_task_metrics`.
  - The word counts are also saved in `post_analysis_s`, like a Phrase post analysis.
- **Reopen.** When a job is reopened (`job.reopened`), a new Revision task is created for round 2.
- **Cancel.** A cancelled Katong task cannot be un-cancelled.
- **Sync.** The "Sync" button on the project page re-reads the Katong job. It creates missing
  tasks and brings statuses up to date.
