{include file="new_header.tpl"}
{* Editor Hint: ¿áéíóú *}

<style>
/* ── Project-create page styles ────────────────────────────────────────────── */
:root {
    --core-blue:  #1064C4;
    --twb-accent: #f59e0b;
    --twb-green:  #16a34a;
}
.custom-card {
    border-radius: 0.75rem !important;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -2px rgba(0,0,0,.05) !important;
}
.card-border-top-accent { border-top: 4px solid var(--twb-accent); }
.card-border-top-blue   { border-top: 4px solid var(--core-blue);  }
.space-y-4 > * + * { margin-top: 1.5rem !important; }
.space-y-8 > * + * { margin-top: 2rem   !important; }

/* ── bg-light-mariam / text-dark-mariam — light/dark adaptive ──────────────── */
[data-bs-theme=light] .bg-light-mariam {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-light-rgb), var(--bs-bg-opacity)) !important;
}
[data-bs-theme=dark] .bg-light-mariam {
    --bs-bg-opacity: 1;
    background-color: rgba(var(--bs-dark-rgb), var(--bs-bg-opacity)) !important;
}
[data-bs-theme=light] .text-dark-mariam {
    color: rgba(var(--bs-dark-rgb), 1) !important;
}
[data-bs-theme=dark] .text-dark-mariam {
    color: rgba(var(--bs-light-rgb), 1) !important;
}
.twb-core-blue { color: var(--core-blue) !important; }

/* ── Upload zone ────────────────────────────────────────────────────────────── */
.upload-zone {
    border: 2px dashed #ced4da;
    border-radius: 0.5rem;
    padding: 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s;
    display: block;
}
.upload-zone:hover { border-color: var(--twb-accent); }
[data-bs-theme=dark] .upload-zone { border-color: #495057; }
[data-bs-theme=dark] .upload-zone:hover { border-color: var(--twb-accent); }

/* ── JS-injected target language rows (.target-row added by ProjectCreate14.js) */
.target-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    padding: 0.75rem 1rem;
    background-color: rgba(var(--bs-light-rgb), 0.6);
    border: 1px solid rgba(0,0,0,.1);
    border-radius: 0.5rem;
    margin-top: 0.75rem;
}
[data-bs-theme=dark] .target-row {
    background-color: rgba(var(--bs-dark-rgb), 0.4);
    border-color: rgba(255,255,255,.15);
}
.target-row select {
    flex: 1;
    min-width: 200px;
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    background-color: var(--bs-body-bg);
    color: var(--bs-body-color);
}
[data-bs-theme=dark] .target-row select {
    background-color: #2b3035;
    border-color: #495057;
    color: #dee2e6;
}

/* ── proj-task-type-checkbox: sourcing selects injected by JS ──────────────── */
.proj-task-type-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}
.proj-task-type-checkbox select {
    min-width: 0;
    flex: unset;
    font-size: 0.8rem;
    padding: 0.2rem 0.4rem;
}
/* Legacy pull-left/width-50 — used by JS for the task-type checkbox sub-divs */
#projFormBottom .pull-left { float: left; }
.bottom-line-border { /* spacing handled by .target-row + .target-row sibling rule */ }

/* ── Admin badge ────────────────────────────────────────────────────────────── */
.badge-admin {
    font-size: 0.65rem;
    background-color: #133978;
    color: #fff;
    border-radius: 4px;
    padding: 2px 6px;
    vertical-align: middle;
}

/* ── Home tooltip (matches home_styles3.css) ────────────────────────────────── */
.home_tooltip { position: relative; display: inline-block; cursor: pointer; }
.home_tooltiptext {
    visibility: hidden; width: 220px; background-color: #000; color: #fff;
    text-align: center; border-radius: 6px; padding: 5px 0;
    position: absolute; z-index: 1; bottom: 150%; left: 50%; margin-left: -110px;
    font-size: 0.8rem; font-weight: 400;
}
.home_tooltip:hover .home_tooltiptext { visibility: visible; }

/* ── Date-conversion classes: hidden until JS processes ─────────────────────── */
.process_deadline_utc_new_home_if_possible,
.convert_utc_to_local_deadline_day_mon_year { visibility: hidden; }
</style>

{* ── Hidden parameters consumed by ProjectCreate14.js ───────────────────────── *}
<span class="d-none">
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="maxfilesize">{$maxFileSize}</div>
    <div id="imageMaxFileSize">{$imageMaxFileSize}</div>
    <div id="supportedImageFormats">{$supportedImageFormats}</div>
    <div id="org_id">{$org_id}</div>
    <div id="user_id">{$user_id}</div>
    <div id="deadline_timestamp">{$deadline_timestamp}</div>
    <div id="userIsAdmin">{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}1{else}0{/if}</div>
    <div id="create_memsource">{$create_memsource}</div>
    <div id="split">{if true || in_array($org_id, [860])}1{else}0{/if}</div>
    <div id="template_language_options">
        <option value="0"></option>
        {foreach from=$languages key=codes item=language}
            <option value="{TemplateHelper::uiCleanseHTML($codes)}">{TemplateHelper::uiCleanseHTML($language)}</option>
        {/foreach}
    </div>
    <div id="template1">{$template1}</div>
    <div id="template2">{$template2}</div>

    {* Deadline selects — read/written by ProjectCreate14.js validateForm(); not submitted *}
    <select id="selectedDay"   name="selectedDay"></select>
    <select id="selectedMonth" name="selectedMonth" onchange="selectedMonthChanged()">
        {html_options options=$month_list selected=$selected_month}
    </select>
    <select id="selectedYear"  name="selectedYear"  onchange="selectedYearChanged()">
        {html_options options=$year_list selected=$selected_year}
    </select>
    <select id="selectedHour"  name="selectedHour">
        {html_options options=$hour_list selected=$selected_hour}
    </select>
    <select id="selectedMinute" name="selectedMinute">
        {html_options options=$minute_list selected=$selected_minute}
    </select>

    {* earthquake tag — feature currently disabled; hidden input keeps JS references intact *}
    <input type="hidden" id="earthquake" />
</span>

{* ── Flash messages ─────────────────────────────────────────────────────────── *}
<div class="container">
    {if isset($flash['error'])}
    <div class="alert alert-danger alert-dismissible fade show mt-4">
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    {/if}
    {if isset($flash['info'])}
    <div class="alert alert-info alert-dismissible fade show mt-4">
        <p><strong>{Localisation::getTranslation('common_note')} </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    {/if}
    {if isset($flash['success'])}
    <div class="alert alert-success alert-dismissible fade show mt-4">
        <img src="{urlFor name='home'}ui/img/success.svg" alt="" class="mx-1" />
        <strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    {/if}
    {if isset($flash['warning'])}
    <div class="alert alert-warning alert-dismissible fade show mt-4">
        <p><strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['warning'])}</strong></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    {/if}
</div>

<div class="container-xxl px-4 px-sm-5 px-lg-5 pb-5 pt-4">

    {* ── Breadcrumb + page header ───────────────────────────────────────────── *}
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="{urlFor name="home"}" class="text-decoration-none twb-core-blue">{Localisation::getTranslation('header_home')}</a>
                    <i class="fa-solid fa-chevron-right mx-1 text-muted" style="font-size:.65rem;"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{urlFor name="ngo_projects" options="org_id.{$org_id}"}" class="text-decoration-none twb-core-blue">Projects</a>
                    <i class="fa-solid fa-chevron-right mx-1 text-muted" style="font-size:.65rem;"></i>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">{Localisation::getTranslation('project_create_create_a_project')}</li>
            </ol>
        </nav>
        <h1 class="fs-2 fw-bold text-dark-mariam mb-0">{Localisation::getTranslation('project_create_create_a_project')}</h1>
        <p class="text-muted small mt-1">
            {Localisation::getTranslation('common_denotes_a_required_field')}
        </p>
    </div>

    {* ── Entitlement guard ──────────────────────────────────────────────────── *}
    {if !$allowed}
    <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-circle-exclamation fs-3 text-danger"></i>
            <div>
                <h2 class="fs-3 fw-bold text-dark-mariam mb-1">{Localisation::getTranslation('common_warning')}</h2>
                <p class="mb-0 text-muted small">This organization does not have an active package or has used all available quota for new requests. We’d be happy to help you continue! Please get in touch at <a href="mailto:projects@clearglobal.org?subject=Packages" target="_blank">projects@clearglobal.org</a> to discuss your options.</p>
            </div>
        </div>
    </div>
    {else}

    {* ══════════════════════════════════════════════════════════════════════════
       FORM
       ══════════════════════════════════════════════════════════════════════════ *}
    <form method="post" action="{urlFor name="project-create" options="org_id.$org_id"}"
          enctype="multipart/form-data" accept-charset="utf-8" id="projectCreateForm"
          onsubmit="create_project_button.disabled = true;">

        <div id="placeholder_for_errors_1"></div>

        <div class="row g-4">

            {* ══════════════════════════════════════════════════════════════════
               LEFT COLUMN — main form fields
               ══════════════════════════════════════════════════════════════════ *}
            <div class="col-lg-8 order-1 space-y-8">

                {* ── Card 1: Project Details ─────────────────────────────────── *}
                <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h2 class="fs-3 fw-bold text-dark-mariam mb-0">
                            <i class="fa-solid fa-folder-open me-2" style="color: var(--twb-accent);"></i>
                            {Localisation::getTranslation('project_create_create_a_project')}
                        </h2>
                    </div>

                    {* Title *}
                    <div class="mb-4">
                        <label for="project_title" class="form-label fw-semibold text-dark-mariam">
                            {Localisation::getTranslation('common_title')} <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="project_title" name="project_title"
                               maxlength="128"
                               onblur="checkTitleNotUsed();">
                        <div class="form-text">{Localisation::getTranslation('project_create_1')}</div>
                    </div>

                    {* Summary / Description (impact) *}
                    <div class="mb-4">
                        <label for="project_impact" class="form-label fw-semibold text-dark-mariam">
                            Project Summary/Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="project_impact" name="project_impact"
                                  rows="3"
                                  ></textarea>
                        <div class="form-text">
                            {Localisation::getTranslation('project_create_3')} {Localisation::getTranslation('project_create_4')}
                        </div>
                    </div>

                    {* Project-specific Instructions *}
                    <div class="mb-4">
                        <label for="project_description" class="form-label fw-semibold text-dark-mariam">
                            Any specific instructions to the translators. <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="project_description" name="project_description"
                                  rows="5"
                                  ></textarea>
                        <div class="form-text">Any specific instructions to the translators.</div>
                    </div>

                    {* Reference *}
                    <div class="mb-4">
                        <label for="project_reference" class="form-label fw-semibold text-dark-mariam">
                            {Localisation::getTranslation('common_reference')}
                        </label>
                        <input type="text" class="form-control" id="project_reference"
                               name="project_reference" maxlength="128"
                               >
                        <div class="form-text">{Localisation::getTranslation('project_create_5')}</div>
                    </div>

                    {* Tags *}
                    <div class="mb-0">
                        <label for="tagList" class="form-label fw-semibold text-dark-mariam">
                            {Localisation::getTranslation('common_tags')}
                        </label>
                        <input type="text" class="form-control" id="tagList" name="tagList"
                               >
                        <div class="form-text">
                            {Localisation::getTranslation('project_create_8')}
                            {Localisation::getTranslation('project_create_separated_by')} {Localisation::getTranslation('project_create_seperator')}.
                            {Localisation::getTranslation('project_create_for_multiword_tags_joinwithhyphens')}
                        </div>
                    </div>
                </div>
                {* /Card 1 *}

                {* ── Card 2: Files ──────────────────────────────────────────── *}
                <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h2 class="fs-3 fw-bold text-dark-mariam mb-0">
                            <i class="fa-solid fa-file-arrow-up me-2" style="color: var(--core-blue);"></i>
                            {Localisation::getTranslation('project_create_files')}
                        </h2>
                    </div>

                    <div class="row g-4">
                        {* Source text upload *}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark-mariam d-block">
                                {Localisation::getTranslation('project_create_source_text')} <span class="text-danger">*</span>
                            </label>
                            <label for="projectFile" class="upload-zone w-100">
                                <i class="fa-solid fa-cloud-arrow-up fs-2 mb-2"
                                   style="color: var(--twb-accent);"></i>
                                <div class="fw-semibold text-dark-mariam small">
                                    Click to select or drag & drop
                                </div>
                                <div id="source_text_desc" class="text-muted" style="font-size:.75rem;">
                                    {Localisation::getTranslation('common_loading')}
                                </div>
                                <input type="file" id="projectFile" name="projectFile" class="d-none">
                            </label>
                            <div id="projectFile_name" class="small text-muted mt-1"></div>
                        </div>

                        {* Project image upload *}
[[[these are not in res file
                                        {Localisation::getTranslation('project_create_image_reuse_note')}
 
 {Localisation::getTranslation('project_create_languages')}

                                <span class="text-muted">{Localisation::getTranslation('project_create_suggested_default')}:</span>

                                    {Localisation::getTranslation('project_create_deadline_tips_heading')}
                                    <li>{Localisation::getTranslation('project_create_deadline_tip_1')}</li>
                                    <li>{Localisation::getTranslation('project_create_deadline_tip_2')}</li>
                                    <li>{Localisation::getTranslation('project_create_deadline_tip_3')}</li>


                            {Localisation::getTranslation('project_create_how_to_guide_heading')}
                        {Localisation::getTranslation('project_create_how_to_guide_desc')}
                        {Localisation::getTranslation('project_create_launch_guide')}
                            {Localisation::getTranslation('project_create_project_settings')}



                            {Localisation::getTranslation('project_create_incremental_matching')}
                            {Localisation::getTranslation('project_create_incremental_matching_desc')}


                                {Localisation::getTranslation('common_learn_more')}

                            {Localisation::getTranslation('project_create_admin_settings')}
                            <span class="badge-admin ms-2">{Localisation::getTranslation('common_admin')}</span>

                            {Localisation::getTranslation('project_create_additional_private_tm_keys')}
                               placeholder="{Localisation::getTranslation('project_create_tm_keys_placeholder')}">
                            {Localisation::getTranslation('project_create_tm_keys_desc')}
                            {Localisation::getTranslation('project_create_use_mt_engine')}
                            {Localisation::getTranslation('project_create_pretranslate_100')}
                            {Localisation::getTranslation('project_create_verification_system_project')}
                            {Localisation::getTranslation('project_create_verification_system_project_desc')}
                            {Localisation::getTranslation('project_create_selected_deadline')}

                        {Localisation::getTranslation('project_create_deadline_local_time')}:
                        {Localisation::getTranslation('project_create_deadline_updates_note')}
]]]
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark-mariam d-block">
                                {Localisation::getTranslation('common_project_image')}
                                <span class="home_tooltip ms-1">
                                    <i class="fa-solid fa-circle-info text-muted"></i>
                                    <span class="home_tooltiptext">
                                        {Localisation::getTranslation('project_create_image_reuse_note')}
                                    </span>
                                </span>
                            </label>
                            <label for="projectImageFile" class="upload-zone w-100">
                                <i class="fa-solid fa-image fs-2 mb-2"
                                   style="color: var(--core-blue);"></i>
                                <div class="fw-semibold text-dark-mariam small">
                                    {Localisation::getTranslation('project_create_click_to_upload')}
                                </div>
                                <div id="image_file_desc" class="text-muted" style="font-size:.75rem;">
                                    {Localisation::getTranslation('common_loading')}
                                </div>
                                <input type="file" id="projectImageFile" name="projectImageFile"
                                       accept="image/*" class="d-none">
                            </label>
                            <div id="projectImageFile_name" class="small text-muted mt-1"></div>
                        </div>
                    </div>
                </div>
                {* /Card 2 *}

                {* ── Card 3: Languages ──────────────────────────────────────── *}
                <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h2 class="fs-3 fw-bold text-dark-mariam mb-0">
                            <i class="fa-solid fa-language me-2" style="color: var(--twb-accent);"></i>
                            {Localisation::getTranslation('project_create_languages')}
                        </h2>
                    </div>

                    {* Source language *}
                    <div id="sourceLanguageDiv" class="mb-4">
                        <label for="sourceLanguageSelect" class="form-label fw-semibold text-dark-mariam">
                            {Localisation::getTranslation('common_source_language')} <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="sourceLanguageSelect" name="sourceLanguageSelect">
                            <option value="0"></option>
                            {foreach from=$languages key=codes item=language}
                                <option value="{TemplateHelper::uiCleanseHTML($codes)}">{TemplateHelper::uiCleanseHTML($language)}</option>
                            {/foreach}
                        </select>
                    </div>

                    {* Target languages — rows injected here by ProjectCreate14.js *}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold text-dark-mariam mb-0">
                            {Localisation::getTranslation('project_create_target_languages')} <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-2" id="targetLangContainer">
                            <button type="button" class="btn btn-sm text-white fw-semibold px-3"
                                    style="background-color: var(--twb-accent); border: none;"
                                    id="addTargetLanguageBtn"
                                    onclick="addMoreTargetLanguages(); return false;">
                                <i class="fa-solid fa-plus me-1"></i>{Localisation::getTranslation('project_create_add_more_target_languages')}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger px-3"
                                    id="removeBottomTargetBtn"
                                    onclick="removeTargetLanguage(); return false;"
                                    disabled="disabled">
                                <i class="fa-solid fa-minus me-1"></i>{Localisation::getTranslation('common_remove')}
                            </button>
                        </div>
                    </div>

                    <div id="loading_warning" class="text-muted small py-2">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        {Localisation::getTranslation('common_loading')}
                    </div>

                    {* ProjectCreate14.js appends .target-row divs here *}
                    <div id="targetLangSelectDiv"></div>

                    <div id="placeholder_for_maxTargetsReached" class="mt-2"></div>
                </div>
                {* /Card 3 *}

                {* ── Card 4: Deadline ───────────────────────────────────────── *}
                <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h2 class="fs-3 fw-bold text-dark-mariam mb-0">
                            <i class="fa-regular fa-calendar me-2" style="color: var(--core-blue);"></i>
                            {Localisation::getTranslation('common_deadline')}
                        </h2>
                    </div>

                    <div class="row g-4 align-items-start">
                        <div class="col-md-7">
                            <label for="tdDeadlineInput" class="form-label fw-semibold text-dark-mariam">
                                {Localisation::getTranslation('common_deadline')} <span class="text-danger">*</span>
                            </label>
                            {* Tempus Dominus 6 picker — syncs with hidden selects via inline script below *}
                            <div class="input-group" id="tdDeadlineContainer">
                                <input type="text" class="form-control" id="tdDeadlineInput"
                                       placeholder="DD/MM/YYYY HH:MM" autocomplete="off">
                                <span class="input-group-text" id="tdDeadlineToggle" style="cursor:pointer;">
                                    <i class="fa-regular fa-calendar"></i>
                                </span>
                            </div>
                            <div class="form-text mt-1">
                                {Localisation::getTranslation('project_create_7')}
                            </div>

                            {*
                                Deadline preview — process_deadline_utc_new_home_if_possible converts
                                the raw UTC Unix timestamp to the user's local timezone at render time.
                                Matches the Home5.js conversion pattern used across the platform.
                            *}
                            <div class="mt-3 d-flex align-items-center gap-2 small">
                                <span class="text-muted">{Localisation::getTranslation('project_create_suggested_default')}:</span>
                                <span class="process_deadline_utc_new_home_if_possible fw-semibold text-dark-mariam">
                                    {$deadline_timestamp}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card border-0 p-3 rounded-3"
                                 style="background-color: rgba(var(--bs-info-rgb),.08);">
                                <p class="small text-muted mb-2 fw-semibold">
                                    <i class="fa-solid fa-circle-info me-1"
                                       style="color: var(--core-blue);"></i>
                                    {Localisation::getTranslation('project_create_deadline_tips_heading')}
                                </p>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li>{Localisation::getTranslation('project_create_deadline_tip_1')}</li>
                                    <li>{Localisation::getTranslation('project_create_deadline_tip_2')}</li>
                                    <li>{Localisation::getTranslation('project_create_deadline_tip_3')}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {* /Card 4 *}

                {* Second error placeholder (for language/task-type errors) *}
                <div id="placeholder_for_errors_2"></div>

                {* ── Submit / Cancel ────────────────────────────────────────── *}
                <div class="d-flex gap-3 pt-2">
                    <a href="{urlFor name="ngo_projects" options="org_id.{$org_id}"}"
                       class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-ban me-1"></i>
                        {Localisation::getTranslation('common_cancel')}
                    </a>
                    <button type="submit" class="btn text-white fw-bold px-4"
                            style="background-color: var(--twb-accent); border: none;"
                            name="create_project_button" id="create_project_button"
                            onclick="return validateForm();">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i>
                        {Localisation::getTranslation('common_create_project')}
                    </button>
                </div>

            </div>
            {* /LEFT COLUMN *}


            {* ══════════════════════════════════════════════════════════════════
               RIGHT COLUMN — sidebar
               ══════════════════════════════════════════════════════════════════ *}
            <div class="col-lg-4 order-2 space-y-8">

                {* ── Help card ──────────────────────────────────────────────── *}
                <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
                    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                        <i class="fa-solid fa-book-open me-2 fs-5" style="color: var(--core-blue);"></i>
                        <h3 class="fs-3 fw-bold text-dark-mariam mb-0">
                            {Localisation::getTranslation('project_create_how_to_guide_heading')}
                        </h3>
                    </div>
                    <p class="small text-muted mb-3">
                        {Localisation::getTranslation('project_create_how_to_guide_desc')}
                    </p>
                    <a href="https://communitylibrary.translatorswb.org/books/12-self-managed-partners/page/launching-your-translation-project-on-the-twb-platform"
                       target="_blank"
                       class="btn btn-outline-primary w-100 fw-semibold"
                       style="color: var(--core-blue);">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>
                        {Localisation::getTranslation('project_create_launch_guide')}
                    </a>
                </div>

                {* ── Project Settings card ──────────────────────────────────── *}
                {if !$create_memsource}
                <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
                    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                        <i class="fa-solid fa-sliders me-2 fs-5" style="color: var(--twb-accent);"></i>
                        <h3 class="fs-3 fw-bold text-dark-mariam mb-0">
                            {Localisation::getTranslation('project_create_project_settings')}
                        </h3>
                    </div>

                    {* Publish tasks *}
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="publish" id="publish" value="1" checked="checked">
                        <label class="form-check-label fw-semibold text-dark-mariam" for="publish">
                            {Localisation::getTranslation('project_create_publish_tasks')}
                        </label>
                        <div class="form-text">
                            {Localisation::getTranslation('common_if_checked_tasks_will_appear_in_the_tasks_stream')}
                        </div>
                    </div>

                    {* Track project *}
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="trackProject" id="trackProject" value="1" checked="checked">
                        <label class="form-check-label fw-semibold text-dark-mariam" for="trackProject">
                            {Localisation::getTranslation('common_track_project')}
                        </label>
                        <div class="form-text">{Localisation::getTranslation('project_create_12')}</div>
                    </div>
                {/if}

                    {* Incremental Matching — only when org has NGO linguists *}
                    {if !empty($ngo_linguists_by_language_pair)}
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="incremental_sourcing" id="incremental_sourcing"
                               value="1" checked="checked">
                        <label class="form-check-label fw-semibold text-dark-mariam"
                               for="incremental_sourcing">
                            {Localisation::getTranslation('project_create_incremental_matching')}
                        </label>
                        <div class="form-text">
                            {Localisation::getTranslation('project_create_incremental_matching_desc')}
                            <a href="https://communitylibrary.translatorswb.org/books/12-self-managed-partners/page/who-can-work-on-your-project"
                               target="_blank" class="text-decoration-none twb-core-blue">
                                {Localisation::getTranslation('common_learn_more')}
                                <i class="fa-solid fa-arrow-up-right-from-square"
                                   style="font-size:.7rem;"></i>
                            </a>
                        </div>
                    </div>
                    {/if}

                {if !$create_memsource}
                </div>
                {/if}
                {* /Project Settings card *}

                {* ── Admin-only Settings card ───────────────────────────────── *}
                {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER)) && !$create_memsource}
                <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
                    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                        <i class="fa-solid fa-shield-halved me-2 fs-5"
                           style="color: #133978;"></i>
                        <h3 class="fs-3 fw-bold text-dark-mariam mb-0">
                            {Localisation::getTranslation('project_create_admin_settings')}
                            <span class="badge-admin ms-2">{Localisation::getTranslation('common_admin')}</span>
                        </h3>
                    </div>

                    {* Private TM Keys *}
                    <div class="mb-3">
                        <label for="private_tm_key" class="form-label fw-semibold text-dark-mariam">
                            {Localisation::getTranslation('project_create_additional_private_tm_keys')}
                        </label>
                        <input type="text" class="form-control form-control-sm"
                               id="private_tm_key" name="private_tm_key"
                               placeholder="{Localisation::getTranslation('project_create_tm_keys_placeholder')}">
                        <div class="form-text">
                            {Localisation::getTranslation('project_create_tm_keys_desc')}
                        </div>
                    </div>

                    {* MT Engine *}
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="mt_engine" id="mt_engine" value="1" checked="checked">
                        <label class="form-check-label fw-semibold text-dark-mariam" for="mt_engine">
                            {Localisation::getTranslation('project_create_use_mt_engine')}
                        </label>
                    </div>

                    {* Pre-translate 100% *}
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="pretranslate_100" id="pretranslate_100"
                               value="1" checked="checked">
                        <label class="form-check-label fw-semibold text-dark-mariam"
                               for="pretranslate_100">
                            {Localisation::getTranslation('project_create_pretranslate_100')}
                        </label>
                    </div>

                    {* Verification System Project *}
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="testing_center" id="testing_center" value="1">
                        <label class="form-check-label fw-semibold text-dark-mariam"
                               for="testing_center">
                            {Localisation::getTranslation('project_create_verification_system_project')}
                        </label>
                        <div class="form-text">
                            {Localisation::getTranslation('project_create_verification_system_project_desc')}
                        </div>
                    </div>
                </div>
                {/if}
                {* /Admin Settings card *}

                {* ── Deadline preview card ──────────────────────────────────── *}
                <div class="card bg-light-mariam custom-card p-4">
                    <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                        <i class="fa-regular fa-clock me-2 fs-5"
                           style="color: var(--twb-green);"></i>
                        <h3 class="fs-3 fw-bold text-dark-mariam mb-0">
                            {Localisation::getTranslation('project_create_selected_deadline')}
                        </h3>
                    </div>
                    <p class="small text-muted mb-1">
                        {Localisation::getTranslation('project_create_deadline_local_time')}:
                    </p>
                    {*
                        process_deadline_utc_new_home_if_possible converts a raw UTC Unix timestamp
                        (integer seconds) in the element text to local YYYY-MM-DD HH:MM:SS TZ.
                        Matches the pattern used in home_mariam.tpl / Home5.js.
                        visibility:hidden until JS runs prevents raw timestamp flash.
                    *}
                    <p class="fw-semibold text-danger small mb-0">
                        <span class="process_deadline_utc_new_home_if_possible">
                            {$deadline_timestamp}
                        </span>
                    </p>
                    <p class="small text-muted mt-2 mb-0">
                        {Localisation::getTranslation('project_create_deadline_updates_note')}
                    </p>
                </div>

            </div>
            {* /RIGHT COLUMN *}

        </div>
        {* /row *}

        <input type="hidden" name="sesskey" value="{$sesskey}" />
        <input type="hidden" name="project_deadline" id="project_deadline" />

    </form>

    {/if}
    {* /allowed *}

</div>
{* /container-xxl *}


{* ── ngo_linguists_by_language_pair — consumed by ProjectCreate14.js ─────────── *}
<script>
    var ngo_linguists_by_language_pair = [];
    {foreach from=$ngo_linguists_by_language_pair key=language_pair item=ngo_linguists}
        ngo_linguists_by_language_pair["{TemplateHelper::uiCleanseHTML($language_pair)}"] = {$ngo_linguists};
    {/foreach}
</script>

{* ── Tempus Dominus 6 deadline picker ────────────────────────────────────────── *}
{*
    Bridge between Tempus Dominus and the hidden <select> elements that
    ProjectCreate14.js reads in validateForm() to compute project_deadline.

    On init   : read deadline_timestamp → pre-fill picker.
    On change : update hidden selects + call selectedMonthChanged()
                so validateForm() sees consistent values.
*}
<script>
$(document).ready(function () {
    if (typeof tempusDominus === 'undefined') return;

    var tsRaw = parseInt(document.getElementById('deadline_timestamp').innerHTML, 10);
    var localDate = new Date(tsRaw * 1000);

    var tdPicker = new tempusDominus.TempusDominus(
        document.getElementById('tdDeadlineContainer'),
        {
            display: {
                icons: {
                    time:     'fa-regular fa-clock',
                    date:     'fa-regular fa-calendar',
                    up:       'fa-solid fa-arrow-up',
                    down:     'fa-solid fa-arrow-down',
                    previous: 'fa-solid fa-chevron-left',
                    next:     'fa-solid fa-chevron-right',
                    today:    'fa-solid fa-calendar-check',
                    clear:    'fa-solid fa-trash',
                    close:    'fa-solid fa-xmark',
                },
                components: { seconds: false },
            },
            localization: { format: 'dd/MM/yyyy HH:mm' },
        }
    );

    tdPicker.dates.setValue(tempusDominus.DateTime.convert(localDate));

    document.getElementById('tdDeadlineContainer').addEventListener(
        tempusDominus.Namespace.events.change,
        function (e) {
            if (!e.detail.date) return;
            var d = e.detail.date;
            document.getElementById('selectedYear').value   = d.year;
            document.getElementById('selectedMonth').value  = d.month + 1;
            selectedMonthChanged();                          // regenerate day options
            document.getElementById('selectedDay').value    = d.date;
            document.getElementById('selectedHour').value   = d.hours;
            document.getElementById('selectedMinute').value = d.minutes;
        }
    );
});
</script>

{* ── File input: show selected filename below upload zone ─────────────────────── *}
<script>
$(document).ready(function () {
    ['projectFile', 'projectImageFile'].forEach(function (id) {
        document.getElementById(id).addEventListener('change', function () {
            var nameEl = document.getElementById(id + '_name');
            if (nameEl) nameEl.textContent = this.files.length ? this.files[0].name : '';
        });
    });
});
</script>

{include file="footer2.tpl"}
