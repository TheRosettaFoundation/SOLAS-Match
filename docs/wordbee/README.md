# Wordbee integration

Wordbee Translator is the third option on the **Translation system** switch on `project/{org_id}/create/`, next to Phrase
(the default) and Katong. Only site admins see the switch. The Phrase code path is unchanged.

API docs: [API2](https://help.wordbee.com/api2) (used by default), [API1](https://help.wordbee.com/api1) (only where API2 has no
equivalent), [webhooks](https://help.wordbee.com/wbt/webhooks-api).

## Setup

1. Run `db/schema.sql`. It adds the `Wordbee*` tables and their procedures.
2. In Wordbee:
   - Enable API2 (Settings > Developer API) and tick "Allow API access" for an **admin** user. Admin rights are needed to create persons for TWB linguists. That user's API key is in My Account.
   - Enable API1, note its password, and allow-list the TWB server's IP.
   - Create (or choose):
     - a client company for TWB projects
     - a manager person
     - a **supplier company** for TWB linguists
     - a **Translation + Revision workflow** template and workflow profile
   - Set up **SAML** with TWB's simplesaml as the identity provider, and note Wordbee's SP entity id. Add Wordbee as a service provider in TWB's simplesaml.
3. Add a `[wordbee]` section to `Common/conf/conf.ini` (see `conf.template.ini`). Keep keys and passwords out of the repository.
4. Add webhooks in Wordbee (Settings > Developer API Webhooks):
   - **URL:** `https://twbplatform.org/wordbee_hook`
   - **Method:** POST, content type `application/json`
   - **Header:** `X-TWB-Webhook-Secret: <webhook_secret>`
   - **Body:**
     ```json
     {"projectId": {{projectId}}, "documentIds": {{documentIds}}, "locale": "{{branchLocale}}", "job": "{{JobReference}}"}
     ```
   - **Events:** the job status changes (Completed, In progress, Cancelled, Not started), "Document workflow - Finished" and
     "Document workflow - Cancelled".
   - **Condition:** project reference starts with `twb-project-`.

   Wordbee's async operations call back to `/wordbee_hook?type=trm` with the same header automatically.

The existing every-minute call to `project_cron_1_minute` also finishes async operations whose callback was missed. It also polls open
Wordbee projects every 10 minutes, with one call per open job, which keeps well under Wordbee's 1000 calls per hour.

## How a Wordbee project maps onto TWB data

A Wordbee project writes the same rows as a Phrase self-service project:

- `Projects`, `ProjectFiles`, `project_complete_dates`
- `MemsourceProjectLanguages`, `MemsourceProjects`, `MemsourceSelfServiceProjects`, `MemsourceTasks`
- `Tasks`, `tasks_status`, `taskclaims_required_to_make_claimable`
- entitlements, tracking, Asana and Discourse

As for Shell and Katong projects, the Memsource uid columns hold the TWB project and task ids. The Wordbee ids are in `WordbeeProjects` and `WordbeeTasks`. Code that is shared between Katong and Wordbee lives in `TmsDao`.

| Wordbee | TWB |
| --- | --- |
| Standard project (API1 `POST projects`, reference `twb-project-{id}`) | project |
| Source document (`media/upload` + `workflows/new`, async) | project file |
| `workflows/jobs/new` per target language, after the document import. Over 2000 words it uses `ProjectAndSegments` with `maxSegments = ceil(segments / ceil(words/2000))` | Same split rule as Phrase |
| TR job (one per chunk) | Translation task: workflowLevel 1, internalId `n` or `n.part`, segment range in `beginIndex`/`endIndex` |
| RV job (one per chunk) | Revision task: workflowLevel 2 |
| Word count per language (`workflows/{did}/wordcounts/{trg}`) | Task word count (shared between chunks by segments), project word count, entitlement |
| `POST jobs/{id}/supplier` (person created on first claim with `persons/io/update`) | claim; an empty body on unclaim |
| `POST jobs/{id}/properties` | task deadlines (TWB's self-service deadlines are pushed on creation) and cancel/uncancel |
| API1 `PUT projects/{id}?deadline=` | project deadline |
| `https://{editor_domain}/ux/editor/go?s=jbs{job}` through TWB's SAML SSOService | the task's "open" link |
| `workflows/{did}/files/{trg}/file` | target file download |
| Job status 6/7/8 · 5 · 10 · supplier removed | COMPLETE (plus match bands in `WordbeeTaskMetrics`) · back to IN_PROGRESS · cancelled · unclaimed |

## Step 0: check against your Wordbee platform before going live

These were built from the docs and tested only with mocks. Confirm each on a test platform. Where possible, each is isolated in one
constant or setting in `WordbeeDao`:

- **Auth:** API1 `connect` and API2 `auth/token`, and the API1 token and deadline formats (ISO 8601 UTC is sent).
- **Workflow template:** `PUT projects/{pid}/settings/workflow?id=` works for a Standard project.
- **Document import:** `workflows/new` with `workflow: None` imports the document into a Standard project without creating jobs, and returns `custom.files[].did`.
- **Job creation:** the `workflows/jobs/new` modes (`JOBS_MODE_WHOLE = 'Project'`, `JOBS_MODE_SPLIT = 'ProjectAndSegments'`):
  - whether one call with `workflowProfileId` and `startWorkflow` creates both the TR and the RV job for each chunk
  - the shape of `jobsByLanguage`
  - whether jobs come back in chunk order
- **Job ids:** `jobs/{id}/summary` field names (`task`, `status`, `assignment.uid`), and whether job ids are plain numbers.
- **Persons:** `persons/io/update` with `ChangeOrCreate` returns the person id. Also check:
  - which SAML attribute Wordbee matches persons on (TWB sends email and login `twb_{user_id}`)
  - whether each person uses a licence seat
- **Unassign:** sending `{}` to `jobs/{id}/supplier` (the docs also mention nulls).
- **File types:** the upload file types from `settings/documentformats`.

## Known gaps

- **Word counts per chunk are estimates.** Wordbee gives counts per language, so TWB shares them between chunks by segment count.
- **No active (payable) time is available from Wordbee.** Match bands are stored instead.
- **Webhooks are set up by hand in the Wordbee UI and are not signed.** TWB relies on the shared secret header, and treats every event as a prompt to re-read the project's jobs.
- **The headless `t_project_create` endpoint still uses Phrase only.**
