# Katong integration

Katong is an alternative to Phrase TMS. On `project/{org_id}/create/`, Site Admins see a
**Translation system: Phrase / Katong** switch. Phrase stays the default, and the Phrase code path is
unchanged. Org admins and other users don't see the switch, and the server ignores `tms=katong` from them.

- `INTEGRATION.md`: Katong's integration guide (sandbox).
- `openapi.json`: Katong API spec, version 0.1.0-draft.

## Setup

1. Run `db/schema.sql`. It adds the tables `KatongProjects`, `KatongTasks`, `KatongTaskMetrics` and
   `ProcessedKatongDeliveries`, and their procedures.
2. Add a `[katong]` section to `Common/conf/conf.ini`. See `conf.template.ini`. Keep the key out of the repository.
   ```
   [katong]
   enabled = 'y'
   api_url = 'https://katong.bredenkamp.ch/v1/'
   api_key = '<twb-sandbox key>'
   partner_id = 'twb-sandbox'
   webhook_secret = ''
   ```
3. Register the webhook once, then put the returned `secret` into `webhook_secret`. The secret is shown only once.
   ```bash
   curl -sS -X POST https://katong.bredenkamp.ch/v1/webhooks \
     -H "Authorization: Bearer $KATONG_KEY" -H 'Content-Type: application/json' \
     -d '{"url": "https://twbplatform.org/katong_hook", "events": ["task.status_changed", "job.completed", "job.reopened", "segment.flagged"]}'
   ```
   The site's existing every-minute call to `project_cron_1_minute` also polls Katong jobs that aren't
   complete. This is a fallback for webhook deliveries that never arrive.

## How a Katong project maps onto TWB data

A Katong project writes the same rows as a Phrase self service project:

- `Projects`, `ProjectFiles`, `project_complete_dates`
- `MemsourceProjectLanguages`, `MemsourceProjects`, `MemsourceSelfServiceProjects`, `MemsourceTasks`
- `Tasks`, `tasks_status`, `taskclaims_required_to_make_claimable`
- entitlements, tracking, Asana and Discourse

Katong ids are UUIDs and don't fit the Memsource columns. So, as with Shell Tasks, the Memsource uid
columns hold the TWB project and task id, and the Katong ids live in `KatongProjects` and `KatongTasks`.

| Katong | TWB |
| --- | --- |
| job (`external_ref` = `twb-project-{id}`) | project |
| `mt` task | no TWB task. When it completes, it triggers creation of the language's TWB tasks, like Phrase `JOB_CREATED` |
| `review` task (split into `ceil(words/2000)` parts when over 2000 words) | Translation task(s), workflowLevel 1, internalId `n` or `n.part`, segment range in `beginIndex`/`endIndex` |
| `sme_review` task (optional, "Revision step" switch) | Revision task, workflowLevel 2 |
| `GET /v1/tasks/{id}/metrics` `words.total` | task word count, project word count, entitlement |
| `POST /v1/tasks/{id}/assign` | claim; also used to push a changed task deadline |
| `POST /v1/tasks/{id}/session` | the task's "open" link goes to `task/{id}/katong_editor/`, which mints a one-time link and redirects |
| `GET /v1/jobs/{id}/deliverables/{lang}` | target file download |
| `task.status_changed` `completed` | task COMPLETE, notifications, complete date, payable time stored in `KatongTaskMetrics` |

## Known gaps (Katong API, to raise with the Katong team)

- **No unassign endpoint.** Unclaiming or declining in TWB only updates TWB.
- **No cancel endpoint** for jobs or tasks. Cancelling in TWB only updates TWB.
- **No way to change a job's `due_at`.** Project deadline edits only update TWB. Task deadlines are
  re-sent through assign.
- **File types don't agree.** The `/v1/files` description in the spec lists DOCX, TXT, SRT and WebVTT,
  while the guide also lists PPTX, XLSX and HTML. TWB allows the guide's list. If Katong rejects a file,
  project creation fails with a message, and the project is deleted.
- **Languages are limited** to the pairs `GET /v1/engines` reports. Katong maps `es` to es-419 and `pt` to
  pt-PT, so other Spanish and Portuguese variants can't be chosen.
- **The headless `t_project_create` endpoint still uses Phrase only.**
