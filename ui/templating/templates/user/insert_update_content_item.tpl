<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <meta charset="utf-8" content="application/xhtml+xml" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>TWB Platform — Resource CMS</title>
  <style>
    :root {
      --brand-blue:#0b6fb2; /* TWB-like */
      --brand-dark:#073e5d;
      --muted:#6b7785;
      --card:#ffffff;
      --bg:#f3f6f9;
      --success:#1eae6f;
    }
    * { box-sizing:border-box }
    body { font-family:Inter,Segoe UI,Arial,sans-serif;margin:0;background:var(--bg);color:#182026 }
    header { background:linear-gradient(90deg,var(--brand-blue),#257bbf);color:white;padding:18px 24px;display:flex;align-items:center;gap:16px }
    .logo { width:38px;height:38px;border-radius:6px;background:white;display:flex;align-items:center;justify-content:center;color:var(--brand-blue);font-weight:700 }
    header h1 { font-size:18px;margin:0 }

    .container { max-width:1100px;margin:24px auto;padding:16px }
    .card { background:var(--card);border-radius:10px;padding:18px;box-shadow:0 6px 18px rgba(10,20,30,0.06); }

    /* Stepper */
    .steps { display:flex;gap:8px;align-items:center;margin-bottom:16px }
    .step { flex:1;padding:12px 14px;border-radius:8px;background:#eef3f7;color:var(--muted);text-align:center;font-weight:600 }
    .step.active { background:linear-gradient(180deg,var(--brand-blue),#2b8ed6);color:white }

    form .row { display:flex;gap:12px;margin-bottom:12px }
    .col { flex:1 }
    label { display:block;font-size:13px;color:var(--muted);margin-bottom:6px }
    input[type=text],input[type=date],select,textarea { width:100%;padding:10px;border:1px solid #e2e8ee;border-radius:8px;font-size:14px }
    textarea { min-height:140px }
    .muted { font-size:13px;color:var(--muted) }

    .actions { display:flex;gap:10px;justify-content:space-between;align-items:center;margin-top:12px }
    .btn { background:var(--brand-blue);color:white;padding:10px 14px;border-radius:8px;border:none;cursor:pointer }
    .btn.ghost { background:transparent;color:var(--brand-dark);border:1px solid #d6dbe0 }
    .btn.secondary { background:#f5f7f9;color:var(--brand-dark) }

    /* attachments table */
    .attachments { margin-top:8px;border-top:1px solid #eef3f7;padding-top:12px }
    .attach-list { width:100%;border-collapse:collapse }
    .attach-list th,.attach-list td { padding:8px;text-align:left;border-bottom:1px solid #f1f5f8 }
    .small { font-size:13px;color:var(--muted) }

    /* right summary */
    .layout { display:grid;grid-template-columns:2fr 1fr;gap:18px }
    .summary .meta { display:flex;flex-direction:column;gap:8px }
    .meta-item { display:flex;justify-content:space-between;padding:8px 10px;background:#fbfdff;border-radius:8px }

    /* responsive */
    @media(max-width:900px) { .layout { grid-template-columns:1fr } .steps { flex-direction:column } .step { width:100% }  }
  </style>
</head>
<body>
  <header>
    <div class="logo"><a href="https://twbplatform.org">TWB</a></div>
    <h1>CMS — Create/Edit Resource{if !empty($org)} for {$org->getName()|escape:'html':'UTF-8'}{/if}</h1>
  </header>

{if !empty($content[0]['id'])}{assign var="selected_content_id" value=$content[0]['id']}{else}{assign var="selected_content_id" value=0}{/if}
{if !empty($content[0]['type'])}{assign var="selected_type" value=$content[0]['type']}{else}{assign var="selected_type" value=0}{/if}
{if !empty($content[0]['scope'])}{assign var="selected_scope" value=$content[0]['scope']}{else}{assign var="selected_scope" value=0}{/if}
{if !empty($content[0]['highlight'])}{assign var="selected_highlight" value=$content[0]['highlight']}{else}{assign var="selected_highlight" value=0}{/if}
{if !empty($content[0]['published'])}{assign var="selected_published" value=$content[0]['published']}{else}{assign var="selected_published" value=0}{/if}
{if !empty($content[0]['sorting_order'])}{assign var="selected_sorting_order" value=$content[0]['sorting_order']}{else}{assign var="selected_sorting_order" value=0}{/if}
{if !empty($content[0]['title'])}{assign var="selected_title" value=$content[0]['title']}{else}{assign var="selected_title" value=''}{/if}
{if !empty($content[0]['snippet'])}{assign var="selected_snippet" value=$content[0]['snippet']}{else}{assign var="selected_snippet" value=''}{/if}
{if !empty($content[0]['body'])}{assign var="selected_body" value=$content[0]['body']}{else}{assign var="selected_body" value=''}{/if}
{if !empty($content[0]['number_images'])}{assign var="number_images" value=$content[0]['number_images']}{else}{assign var="number_images" value=0}{/if}
{if !empty($content[0]['number_attachments'])}{assign var="number_attachments" value=$content[0]['number_attachments']}{else}{assign var="number_attachments" value=0}{/if}

{if !empty($content[0]['language_pair_target_JSON'])}{assign var="selected_codes" value=json_decode($content[0]['language_pair_target_JSON'], true)}{else}{assign var="selected_codes" value=[]}{/if}

{if !empty($content[0]['selected_service_JSON'])}{assign var="selected_service_ids" value=json_decode($content[0]['selected_service_JSON'], true)}{else}{assign var="selected_service_ids" value=[]}{/if}

{if !empty($content[0]['external_link'])}{assign var="selected_external_link" value=$content[0]['external_link']}{else}{assign var="selected_external_link" value=''}{/if}
{if !empty($content[0]['number_of_views'])}{assign var="number_of_views" value=$content[0]['number_of_views']}{else}{assign var="number_of_views" value=0}{/if}

{assign var="selected_project_ids" value=[]}
{foreach from=$projects item=row}
    {if !empty($row['project_id'])}
        {append var="selected_project_ids" value=$row['project_id']}
    {/if}
{/foreach}
{assign var="number_of_projects" value=count($selected_project_ids)}

  <div class="container">
    <div class="layout">
      <div>
        <div class="card">
          <div class="steps" id="stepper">
            <div class="step active" data-step="1">1 — Basic</div>
            <div class="step" data-step="2">2 — Content</div>
            <div class="step" data-step="3">3 — Attachments</div>
            <div class="step" data-step="4">4 — Links & Publish</div>
          </div>

         {if !empty($org_id)}
         <form method="post" action="{urlFor name="content_item_org" options="content_id.$selected_content_id|org_id.$org_id"}" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="update_content_item.disabled = true;">
         {else}
         <form method="post" action="{urlFor name="content_item" options="content_id.$selected_content_id"}" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="update_content_item.disabled = true;">
         {/if}

            <input type="hidden" id="content_id" name="content_id" value="{$selected_content_id}" />

            <!-- Step 1 -->
            <div class="step-panel" data-panel="1">
              <h3>Step 1 — Basic Details</h3>
              <div class="row">
<!--
                <div class="col">
                  <label for="date">Date</label>
                  <input type="date" id="date" name="date" />
                </div>
-->
                <div class="col">
                  <label for="published">Status</label>
                  <select id="published" name="published">
                    <option value= "0" {if $selected_published ==  0}selected="selected"{/if}>Unpublished</option>
                    <option value= "1" {if $selected_published ==  1}selected="selected"{/if}>Published</option>
                    <option value="-1" {if $selected_published == -1}selected="selected"{/if}>Archived</option>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <label for="title">Title / Resource name</label>
                  {if empty($selected_title)}
                  <input type="text" id="title" name="title" placeholder="Enter title" />
                  {else}
                  <input type="text" id="title" name="title" value="{$selected_title|escape:'html':'UTF-8'}" />
                  {/if}
                </div>
                <div class="col">
                  <label for="type">Type / Category</label>
                  <select id="type" name="type">
                    {if $org_id == 0}
                    <option value="1" {if $selected_type == 11}selected="selected"{/if}>Article</option>
                    <option value="3" {if $selected_type == 13}selected="selected"{/if}>Event</option>
                    <option value="2" {if $selected_type == 12}selected="selected"{/if}>Newsletter</option>
                    <option value="2" {if $selected_type == 14}selected="selected"{/if}>Report</option>
                    <option value="4" {if $selected_type == 21}selected="selected"{/if}>Resource</option>
                    {else}
<!--                    <option value="0">External content</option> -->
                    <option value="5" {if $selected_type == 50}selected="selected"{/if}>Organization</option>
                    <option value="6" {if $selected_type == 60}selected="selected"{/if}>Project</option>
                    {/if}
                  </select>
                </div>
              </div>

              <div class="row">
                {if $org_id == 0}
                <div class="col">
                  <label for="scope">Scope</label>
                  <select id="scope" name="scope">
                    <option value="1" {if $selected_scope == 1}selected="selected"{/if}>TWB Resource</option>
                    <option value="2" {if $selected_scope == 2}selected="selected"{/if}>Public Resource</option>
<!--
                    <option value="5" {if $selected_scope == 3}selected="selected"{/if}>Organization</option>
                    <option value="6" {if $selected_scope == 4}selected="selected"{/if}>Project</option>
-->
                  </select>
                </div>
                {/if}
                <div class="col">
                  <label for="languages">Language + Variant (optional)</label>
                  <select id="languages" name="languages[]" multiple style="height:120px">
                    <option value="0"></option>
                    {foreach from=$language_selection key=codes item=language}
                    <option value="{$codes}" {if in_array($codes, $selected_codes)}selected="selected"{/if}>{$language}</option>
                    {/foreach}
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <label for="services">Relevant Services (optional)</label>
                  <select id="services" name="services[]" multiple style="height:120px">
                    <option value="0"></option>
                    {foreach from=$service_selection item=service}
                    <option value="{$service['id']}" {if in_array({$service['id']}, $selected_service_ids)}selected="selected"{/if}>{$service['desc']}</option>
                    {/foreach}
                  </select>
                </div>
              </div>

              <div class="actions">
                <div class="small">You can proceed to the next step.</div>
                <div>
<!--                  <button class="btn ghost" type="button" onclick="saveDraft()">Save draft</button> -->
                  <button class="btn" type="button" onclick="gotoStep(2)">Next: Content →</button>
                </div>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="step-panel" data-panel="2" style="display:none">
              <h3>Step 2 — Content</h3>
              <div>
                <label for="snippet">Snippet (HTML) — used in lists / homepage</label>
                {if empty($selected_snippet)}
                <textarea id="snippet" name="snippet" placeholder="Short HTML snippet or summary"></textarea>
                {else}
                <textarea id="snippet name="snippet"">{$selected_snippet|escape:'html':'UTF-8'}</textarea>
                {/if}
              </div>
              <div style="margin-top:10px">
                <label for="body">Body (HTML)</label>
                {if empty($selected_body)}
                <textarea id="body" name="body" placeholder="Full HTML body"></textarea>
                {else}
                <textarea id="body" name="body">{$selected_body|escape:'html':'UTF-8'}</textarea>
                {/if}
              </div>
              <div class="row" style="margin-top:10px">
                <div class="col">
                  <label for="image">Image</label>
                  <input type="file" id="image" name="image[]" accept="image/*" onchange="previewImage(event)" />
                  <div id="imgPreview" style="margin-top:8px"></div>
                </div>
              </div>
              {if !empty($previous_images)}
              <div class="row" style="align-items:center">
                <div class="col">
                  Check a checkbox to delete previous image
                </div>
                <div class="col">
                  Creation Date
                </div>
              </div>
              {foreach $previous_images as $row}
              <div class="row" style="align-items:center">
                <div class="col">
                  <label><input type="checkbox" name="previous_images[]" value="{$row['sorting_order']}" /> Previous Image {$row['sorting_order']}</label>
                </div>
                <div class="col">
                  {$row['creation_date']}
                </div>
              </div>
              {/foreach}
              {/if}

              <div class="row">
                <div class="col">
                  <div style="margin-top:10px">
                    <label for="external_link">External link</label>
                    {if empty($selected_external_link)}
                    <input type="text" id="external_link" name="external_link" placeholder="https://..." />
                    {else}
                    <input type="text" id="external_link" name="external_link" value="{$selected_external_link|escape:'html':'UTF-8'}" />
                    {/if}
                    <div class="small">Clicks on this link should be tracked.</div>
                  </div>
                </div>
              </div>

              <div class="actions">
                <div></div>
                <div>
                  <button class="btn ghost" type="button" onclick="gotoStep(1)">← Back</button>
                  <button class="btn" type="button" onclick="gotoStep(3)">Next: Attachments →</button>
                </div>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="step-panel" data-panel="3" style="display:none">
              <h3>Step 3 — Attachments</h3>
              <div>
                <label>Upload attachments (multiple files allowed)</label>
                <input type="file" id="attachments" name="attachments[]" multiple />
<!--
                <div class="attachments">
                  <table class="attach-list" id="attachTable">
                    <thead><tr><th>File name</th><th>Size</th><th>Downloads</th><th>Action</th></tr></thead>
                    <tbody></tbody>
                  </table>
                </div>
-->

                {if !empty($previous_attachments)}
                <div class="row" style="align-items:center">
                  <div class="col">
                    Check a checkbox to delete previous attachment
                  </div>
                  <div class="col">
                    Creation Date
                  </div>
                </div>
                {foreach $previous_attachments as $row}
                <div class="row" style="align-items:center">
                  <div class="col">
                    <label><input type="checkbox" name="previous_attachments[]" value="{$row['sorting_order']}" /> Previous Attachment {$row['sorting_order']}</label>
                  </div>
                  <div class="col">
                    {$row['creation_date']}
                  </div>
                </div>
                {/foreach}
                {/if}

              </div>

              <div class="actions">
<!--                <div class="small" id="attachCount">Total attachments: 0</div> -->
                <div>
                  <button class="btn ghost" type="button" onclick="gotoStep(2)">← Back</button>
                  <button class="btn" type="button" onclick="gotoStep(4)">Next: Publish →</button>
                </div>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="step-panel" data-panel="4" style="display:none">
              <h3>Step 4 — Projects & Publish</h3>

              {if $org_id != 0}
              <div class="row">
{*
                <div class="col">
                  <label for="organisation">Creator Organization</label>
                  <select id="organisation" name="organisation">
                    <option value="">— Select Organization —</option>
                    {foreach from=$organisations key=o_id item=o_name}
                    <option value="{$o_id}" {if $o_id == $selected_org_id}selected="selected"{/if}>{$o_name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                  </select>
                </div>
*}
                <div class="col">
                  <label for="projects">Link to Projects</label>
                  <select id="projects" name="projects[]" multiple style="height:120px">
                    <option value="">— Select Project —</option>
                    {foreach from=$project_selection key=project_id item=project_name}
                    <option value="{$project_id}" {if in_array($project_id, $selected_project_ids)}selected="selected"{/if}>{$project_name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              {/if}

              <div class="row" style="align-items:center">
                <div class="col">
                  <label><input type="checkbox" id="highlight" name="highlight" value="1" {if !empty($selected_highlight)}checked="checked"{/if} /> Show on homepage</label>
                </div>
                <div class="col">
                  <label for="sorting_order">Sort order (higher = higher priority)</label>
                  {if empty($selected_sorting_order)}
                  <input type="number" id="sorting_order" name="sorting_order" value="0" />
                  {else}
                  <input type="number" id="sorting_order" name="sorting_order" value="{$selected_sorting_order}" />
                  {/if}
                </div>
              </div>

              <div style="margin-top:10px" class="row">
                <div class="col small">Views: <span id="viewsCount">{if empty($number_of_views)}0{else}{$number_of_views}{/if}</span></div>
                <div class="col small">Attachments: <span id="summaryAttachments">{if empty($number_attachments)}0{else}{$number_attachments}{/if}</span> • Linked projects: <span id="summaryProjects">{if empty($number_of_projects)}0{else}{$number_of_projects}{/if}</span></div>
              </div>

              <div class="actions">
                <div></div>
                <div>
                  {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                  <button class="btn ghost" type="button" onclick="gotoStep(3)">← Back</button>
                  <button type="submit" id="update_content_item" name="update_content_item" class="btn">Save</button>
<!--                  <button class="btn secondary" onclick="saveDraft()" type="button">Save as Draft</button> -->
                </div>
              </div>
            </div>

          </form>
        </div>

        <div style="height:12px"></div>

<!--
        <div class="card small">
          <div class="small"><strong>Notes</strong></div>
          <div class="small" style="margin-top:8px">• This prototype is client-side only — attachments are simulated and not uploaded.
          • In a real system, attachments live in <code>resource_attachments</code> and link via <code>resource_id</code>. </div>
        </div>
-->

      </div>

      <aside class="summary">
        <div class="card">
          <h3>Resource summary</h3>
          <div class="meta">
            <div class="meta-item"><div>Title</div><div id="sumTitle" class="small">{if !empty($selected_title)}{$selected_title}{else}—{/if}</div></div>
            <div class="meta-item"><div>Type</div><div id="sumType" class="small">{if !empty($selected_type)}{$selected_type}{else}—{/if}</div></div>
            {if $org_id == 0}
            <div class="meta-item"><div>Scope</div><div id="sumScope" class="small">{if !empty($selected_scope)}{$selected_scope}{else}—{/if}</div></div>
            {/if}
            <div class="meta-item"><div>Languages</div><div id="sumLang" class="small">{foreach from=$language_selection key=codes item=language}{if in_array($codes, $selected_codes)}{$language},{/if}{/foreach}</div></div>
            <div class="meta-item"><div>Attachments</div><div id="sumAttach" class="small">{if empty($number_attachments)}0{else}{$number_attachments}{/if}</div></div>
            {if $org_id != 0}
            <div class="meta-item"><div>Linked projects</div><div id="sumProjects" class="small">{if empty($number_of_projects)}0{else}{$number_of_projects}{/if}</div></div>
            {/if}
            <div class="meta-item"><div>Show on homepage</div><div id="sumHomepage" class="small">{if !empty($selected_highlight)}Yes{else}No{/if}</div></div>
          </div>
        </div>

        <div style="height:18px"></div>

        <div class="card">
          <h3>Quick actions</h3>
          <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px">
            <button class="btn" onclick="gotoStep(1)">Edit Basic</button>
            <button class="btn ghost" onclick="gotoStep(2)">Edit Content</button>
            <button class="btn ghost" onclick="gotoStep(3)">Edit Attachments</button>
            <button class="btn ghost" onclick="gotoStep(4)">Edit Links & Publish</button>
          </div>
        </div>
      </aside>
    </div>
  </div>

  <script>
    // simple step logic + client-side preview + attachments simulation
    const panels = document.querySelectorAll('.step-panel');
    const steps = document.querySelectorAll('.step');

    function gotoStep(n) {
      panels.forEach(p=>p.style.display = p.dataset.panel==n ? 'block' : 'none');
      steps.forEach(s=> s.classList.toggle('active', s.dataset.step==n));
      updateSummary();
    }

    // initialize date to today
    //document.getElementById('date').valueAsDate = new Date();

    function previewImage(e) {
      const file = e.target.files[0];
      const p = document.getElementById('imgPreview');
      p.innerHTML = '';
      if(!file) return;
      const img = document.createElement('img');
      img.style.maxWidth='100%';img.style.borderRadius='6px';
      img.src = URL.createObjectURL(file);
      p.appendChild(img);
    }

/*
    // attachments simulation
    const attachTbody = document.querySelector('#attachTable tbody');
    let attachments = [];
    function handleFiles(e) {
      const files = Array.from(e.target.files);
      files.forEach(f => {
        const id = Date.now().toString(36) + Math.random().toString(36).slice(2,6);
        const row = { id, name: f.name, size: formatBytes(f.size), downloads:0 };
        attachments.push(row);
      });
      renderAttachments();
    }
    function renderAttachments() {
      attachTbody.innerHTML = '';
      attachments.forEach(a => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${ a.name }</td><td>${ a.size }</td><td>${ a.downloads }</td><td><button class='btn ghost' onclick="removeAttach('${ a.id }')">Delete</button></td>`;
        attachTbody.appendChild(tr);
      });
      document.getElementById('attachCount').innerText = 'Total attachments: ' + attachments.length;
      document.getElementById('summaryAttachments').innerText = attachments.length;
      document.getElementById('sumAttach').innerText = attachments.length;
    }
    function removeAttach(id) { attachments = attachments.filter(a=>a.id!==id); renderAttachments(); }
    function formatBytes(bytes) { if(bytes===0) return '0 B'; const k=1024; const sizes=['B','KB','MB','GB']; const i=Math.floor(Math.log(bytes)/Math.log(k)); return parseFloat((bytes/Math.pow(k,i)).toFixed(2)) + ' ' + sizes[i]; }

    function saveDraft() { alert('Draft saved (client-side). In production, POST to API endpoint.'); }
    function publishResource() {
      const data = {
        content_id: document.getElementById('content_id').value,
        type: document.getElementById('type').value,
        scope: document.getElementById('scope').value,
        highlight: document.getElementById('highlight').checked,
        published: document.getElementById('published').value,
        sorting_order: document.getElementById('sorting_order').value
        title: document.getElementById('title').value,
        snippet: document.getElementById('snippet').value,
        body: document.getElementById('body').value,
        language: document.getElementById('language').value,
        direct_link: document.getElementById('direct_link').value,
        external_link: document.getElementById('external_link').value,
//image
        attachments: attachments,
        projects: Array.from(document.getElementById('projects').selectedOptions).map(o=>o.value),
        organisation: document.getElementById('organisation').value,
      };
    }
*/

    function updateSummary() {
      document.getElementById('sumTitle').innerText = document.getElementById('title').value || '—';
      document.getElementById('sumType').innerText = document.getElementById('type').options[document.getElementById('type').selectedIndex].text || '—';
      {if $org_id == 0}
      document.getElementById('sumScope').innerText = document.getElementById('scope').options[document.getElementById('scope').selectedIndex].text || '—';
      {/if}
      document.getElementById('sumLang').innerText = Array.from(document.getElementById('languages').selectedOptions).length;

      {if $org_id != 0}
      document.getElementById('sumProjects').innerText = Array.from(document.getElementById('projects').selectedOptions).length;
      document.getElementById('summaryProjects').innerText = document.getElementById('sumProjects').innerText;
      {/if}

      document.getElementById('sumHomepage').innerText = document.getElementById('highlight').checked ? 'Yes' : 'No';
    }

    // wire up inputs to summary updates
    ['title', 'type', 'scope', 'languages', 'highlight', 'projects'].forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      el.addEventListener('change', updateSummary);
      el.addEventListener('input', updateSummary);
    });

    // initial
    gotoStep(1);
  </script>
</body>
</html>
