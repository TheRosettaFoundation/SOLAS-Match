# Integrating with katong

For the TWB platform team: how to send work to the review service, put reviewers into the
editor, and get results and payable time back. The complete reference is the OpenAPI spec
(`/openapi.json`, interactive at `/docs`); this guide is the order to read it in.
`examples/twb_client.py` does everything below in about 150 lines of Python.

## The sandbox

| | |
| --- | --- |
| Base URL | `https://katong.bredenkamp.ch` |
| API docs | `https://katong.bredenkamp.ch/docs` |
| Your keys | sent separately; keep them out of code and tickets. `twb-sandbox` for the platform (jobs, resources); `twb-reports` for staff who read reports (reports only) |
| Your partner id | `twb-sandbox` (use it as `partner_id` on every job) |

Ground rules for the sandbox:

- **Test content only.** No partner documents, no personal data. It runs on a single server
  with no backups, and data may be wiped without notice.
- **The API may still change** until the contract is agreed (Phase 0 gate, 12 December 2026).
  Changes will be listed at the end of this file.
- MT is Lara (Translated) for the Phase 0 languages (English, French, Latin American Spanish,
  Modern Standard Arabic, Swahili, Bangla, in all directions except Bangla↔Swahili) and European
  Portuguese. Please keep volumes modest: each MT call costs real money.
- The editor's interface translations (French, Spanish, Arabic, Swahili, Portuguese) are machine
  drafts not yet checked by language leads.

## Basics

- **Auth:** `Authorization: Bearer <key>` on every `/v1` call. Your key has the `jobs` scope
  (jobs, tasks, files, webhooks, direct MT) and `resources` (memories, termbases). A key sees
  only the jobs it created.
- **Errors** are [RFC 9457](https://www.rfc-editor.org/rfc/rfc9457) problem details
  (`application/problem+json`): `title` is stable and machine-readable, `detail` is for people.
- **Ids** are UUIDs. Times are ISO 8601 with a zone. Language codes are ISO 639-1 (`en`, `fr`,
  `sw`…); the service maps them to locales (Spanish → es-419, Portuguese → pt-PT, Arabic → MSA).
- **Idempotency:** creating a job is idempotent on `external_ref` (your task id). Sending the
  same `external_ref` again returns the existing job with `200` instead of `201`.

## The model

- A **job** is one source document plus one or more target languages.
- Each target language gets **tasks**, in order:
  - `mt` (the machine; runs by itself straight away),
  - `review` (unless you leave the language out of `review_langs`),
  - `sme_review`, an optional partner subject-expert step after review (`sme_langs`).
- Task statuses: `queued → in_progress → completed` (also `failed`, `cancelled`). A job is
  `completed` when all its tasks are done.
- A long review task can be **split** into parts (each a task with a segment range, assigned
  separately). Reviewers of different parts see the whole document and each other's work live.
- A delivered job can be **reopened** for another review round; round 1's history is kept.

## A job, step by step

### 1. Register a webhook (once)

```http
POST /v1/webhooks
{"url": "https://your-platform.example/katong/events",
 "events": ["task.status_changed", "job.completed", "job.reopened", "segment.flagged"]}
```

The response includes a `secret`, **shown only once**: store it to verify signatures (below).

### 2. Send the source

Either inline text in the job (step 3), or upload a file first:

```http
POST /v1/files            (multipart, field "file")
→ 201 {"id": "…", "filename": "leaflet.docx", "format": "docx", "size": 48213}
```

- **Formats:** DOCX, PPTX, XLSX, HTML, SRT, WebVTT, UTF-8 TXT, up to 20 MB.
- The translated file comes back in the same format. Inline formatting inside a paragraph is
  flattened (a bold word takes the paragraph's style); structure, tables, slides, timings and
  styles are kept.
- **Reference files** for reviewers (style guides, glossaries as PDF, images) go up with
  `POST /v1/files?purpose=reference`. They are shown in the editor, never translated.

### 3. Create the job

```http
POST /v1/jobs
{
  "external_ref": "twb-task-12345",
  "partner_id": "twb-sandbox",
  "source_lang": "en",
  "target_langs": ["fr", "sw"],
  "content_type": "health",
  "title": "Cholera prevention leaflet",
  "due_at": "2026-11-20T17:00:00Z",
  "source": {"file_id": "…"},
  "review_langs": ["fr", "sw"],
  "sme_langs": ["fr"],
  "sme_mode": "comment",
  "brief": "For community health workers; plain language, formal 'vous'.",
  "reference_file_ids": ["…"]
}
```

| Field | Notes |
| --- | --- |
| `source` | `{"file_id": "…"}` or `{"text": "…"}` |
| `content_type` | `general`, `health` or `legal`; routing can differ by type |
| `due_at` | when the partner needs the finished translation, with a time zone |
| `review_langs` | omit to review every target; `[]` delivers MT only |
| `sme_langs`, `sme_mode` | expert review after review; `comment` (comments only) or `edit` |
| `brief`, `reference_file_ids` | shown to reviewers in the editor |
| `tm_ids`, `termbase_ids` | optional. Every job already reads and writes the partner's own memory for each pair (created on first use) and reads the partner's other and TWB-wide memories; `tm_ids` adds more, read-only. Termbases: those named, else the partner's and TWB-wide ones |

The response is the job with its tasks. MT starts at once; exact memory matches are filled in
before MT is called.

### 4. Follow progress

Webhooks tell you when anything changes (`task.status_changed` for each task, `job.completed` at
the end). You can also poll `GET /v1/jobs/{id}` (tasks with `progress.approved/total`) or look a
job up by your reference with `GET /v1/jobs?external_ref=…`.

### 5. Assign and open the editor

The platform decides who works on what. katong records it and never assigns anyone itself.

```http
POST /v1/tasks/{task_id}/assign
{"assignee_id": "user-881", "display_name": "Amina Diallo", "role": "reviewer",
 "deadline": "2026-11-20T17:00:00Z"}
```

When the reviewer clicks "Open" in TWB, mint a link and redirect them to it:

```http
POST /v1/tasks/{task_id}/session
{"user_id": "user-881", "display_name": "Amina Diallo", "role": "reviewer",
 "languages": ["en", "fr"], "ui_lang": "fr"}
→ {"url": "https://katong.bredenkamp.ch/work/{task_id}?token=…", "expires_at": "…"}
```

- The link works **once** and expires after 15 minutes; mint a fresh one each time. Opening it
  starts a 12-hour editor session in that browser.
- **Deadlines:** set each step's own `deadline` on assign (with a time zone), leaving review
  time for an expert review after it. The editor shows the reviewer that deadline, or the
  job's `due_at` if the task has none, in their own time zone. It is highlighted when less
  than a day is left, and once it has passed. Reminders and reassignment stay with the
  platform.
- Only the assignee gets a link (or `role: "lead"`, for a language lead looking in).
- `role` is `reviewer`, `translator`, `sme` or `lead`.
- `ui_lang` picks the interface language (en, fr, es, ar, sw, pt). The reviewer can switch it.
- **SME tasks** can't be opened until review of their language is complete: you get
  `409 Waiting for review`.
- To split a large review first: `POST /v1/tasks/{task_id}/split {"parts": 3}` on an
  unstarted task. It returns the parts, which you then assign separately.

The editor works on phones and on slow or dropping connections: work done offline is kept in the
browser and sent when the connection is back.

### 6. Results

When the reviewer completes, you get `task.status_changed` (`completed`), and `job.completed`
once every task is done.

- **Deliverable:** `GET /v1/jobs/{id}/deliverables/{lang}` returns the file in its original
  format. Add `?format=xliff` for XLIFF 2.1, or `?format=tmx` for TMX 1.4. It is available as
  soon as MT is done; until review completes it carries the MT. The `X-Untranslated-Segments`
  header counts segments with no translation.
- **Payable time:** `GET /v1/tasks/{task_id}/metrics` gives active time in milliseconds
  (`active_ms`, idle time excluded, the payment basis), wall time, accepted, edited and flagged
  segments, mean edit distance, and the same per reviewer (`by_reviewer`).
- **Words:** the same response has `words`: source words in the task, `total` and `by_band`.
  The bands are `tm_101` and `tm_100` (exact memory matches), `fuzzy_95_99`, `fuzzy_85_94`
  and `fuzzy_75_84` (best memory match found), `mt` (machine translation, no useful match) and
  `new`. This is for per-word payment or comparing it with time. katong applies no rates: those
  are the platform's to set.
- **What review changed:** `GET /v1/jobs/{id}/changes?lang=fr` gives each segment's MT text
  against its final text, plus a summary.
- **Comments** left by reviewers and experts: `GET /v1/jobs/{id}/comments`.

### 7. Another round

```http
POST /v1/jobs/{id}/reopen
{"target_langs": ["fr"], "reason": "Partner asked for changes"}
```

This creates a new `review` task (`round: 2`) per language, to assign as before. Segments keep
round 1's text, and round 1's versions, flags, comments and time are all kept. It emits
`job.reopened`, and needs that language's review to be finished.

## Webhooks

Every delivery is a `POST` with a JSON body:

```json
{"id": "6f1c…", "event": "task.status_changed", "created_at": "2026-11-03T09:12:44Z",
 "data": {"task_id": "…", "job_id": "…", "external_ref": "twb-task-12345", "kind": "review",
          "target_lang": "fr", "status": "completed", "previous_status": "in_progress",
          "detail": null}}
```

| Event | `data` |
| --- | --- |
| `task.status_changed` | task and job ids, `external_ref`, `kind`, `target_lang`, `status`, `previous_status`, `detail` (why, in words, e.g. an MT error) |
| `job.completed` | job id, `external_ref`, `target_langs`, `due_at`, `on_time` (completed by `due_at`, or no due date), `deliverables` (one URL per language) |
| `job.reopened` | job id, `external_ref`, `target_langs`, `rounds` (`{"fr": 2}`), `reason` |
| `segment.flagged` | task and job ids, `target_lang`, segment `index`, `category`, `severity`, `note` |

Headers: `X-Katong-Event`, `X-Katong-Delivery` (same as `id`), and `X-Katong-Signature`.

**Verify every delivery.** The signature is `t=<unix seconds>,v1=<hex>`, where `v1` is
HMAC-SHA256 with your secret over `"<t>." + raw body`. Reject timestamps more than five minutes
old:

```python
import hashlib, hmac, time

def verify(secret: str, header: str, body: bytes, tolerance: int = 300) -> bool:
    parts = dict(p.split("=", 1) for p in header.split(","))
    if abs(time.time() - int(parts["t"])) > tolerance:
        return False
    expected = hmac.new(secret.encode(), f"{parts['t']}.".encode() + body, hashlib.sha256)
    return hmac.compare_digest(expected.hexdigest(), parts["v1"])
```

- Answer with any `2xx` quickly and do the work afterwards.
- Failed deliveries are retried with backoff for about ten hours, so the same `id` can arrive
  more than once: ignore repeats.
- `GET /v1/webhooks/{id}/deliveries` shows recent attempts and responses, for debugging.

## Reports

For staff: effort and acceptance across jobs, for planning and payment checks. This needs the
`twb-reports` key, which has the `reports` scope only. Its reports cover the jobs created with
`twb-sandbox`, and it can't read or change anything else.

```http
GET /v1/reports/effort?group_by=lang_pair,reviewer&from=2026-11-01T00:00:00Z&to=2026-12-01T00:00:00Z
GET /v1/reports/effort?group_by=partner,content_type&format=csv
```

- `group_by` takes one or more of `lang_pair`, `engine`, `partner`, `reviewer`,
  `content_type` and `match_band` (the word bands above; `reviewer,match_band` gives each
  reviewer's words per band).
- `from` and `to` limit the period, by when segments were decided.
- `format=csv` returns a spreadsheet-ready file.

Each row has its grouping values plus:

| Column | Meaning |
| --- | --- |
| `segments`, `words` | decided in the period; each segment counted once, by its latest decision |
| `accepted`, `edited`, `flagged` | how reviewers decided them |
| `acceptance_rate` | accepted ÷ (accepted + edited): how often MT or memory was right as it stood |
| `mean_edit_distance` | 0 (unchanged) to 1 (rewritten), from the starting point to the final text |
| `active_minutes` | active review time, idle time excluded: the payment basis |
| `seconds_per_100_words` | active time per 100 source words |

Per-reviewer figures are for staff only (FR6.5): reviewers see their own time in the editor,
never other people's. For one task's payable time, use `GET /v1/tasks/{id}/metrics`
(platform key).

## Memories and termbases

- **Translation memories:** create one with `POST /v1/tms` (owner `partner`, `domain` or
  `global`; one language pair each), load it with `POST /v1/tms/{id}/import` (TMX 1.4), and
  export with `GET /v1/tms/{id}/export`. Approved review segments are added automatically when
  a review completes.
- **Termbases:** create one with `POST /v1/termbases`, then import TBX or CSV through
  `/v1/termbases/{id}/import`.
- **Term proposals:** reviewers can propose terms from the editor. List the pending ones with
  `GET /v1/termbases/{id}/concepts?pending=true` and approve them with
  `POST /v1/termbases/{id}/approve`. Until approved they aren't used.

## Changes

- 30 Oct 2026: first sandbox release, with a reports key for staff, and word counts by match
  band in task metrics and reports, and job due dates (`due_at`) with deadlines in the editor.
