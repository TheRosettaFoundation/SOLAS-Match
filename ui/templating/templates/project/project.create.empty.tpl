{include file="new_header.tpl"}
{* Editor Hint: ¿áéíóú *}

{* ── Hidden parameters consumed by project_create_empty.js ───────────────────────── *}
<span class="d-none">
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="imageMaxFileSize">{$imageMaxFileSize}</div>
    <div id="supportedImageFormats">{$supportedImageFormats}</div>
    <div id="org_id">{$org_id}</div>
    <div id="user_id">{$user_id}</div>
    <div id="deadline_timestamp">{$deadline_timestamp}</div>
    <div id="userIsAdmin">{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}1{else}0{/if}</div>
    <div id="template_language_options">
        <option value="0"></option>
        {foreach from=$languages key=codes item=language}
            <option value="{TemplateHelper::uiCleanseHTML($codes)}">{TemplateHelper::uiCleanseHTML($language)}</option>
        {/foreach}
    </div>

    {* Deadline selects — read/written by project_create_empty.js validateForm(); not submitted *}
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
                <li class="breadcrumb-item active text-muted" aria-current="page">Create An Empty Project For Shell Tasks</li>
            </ol>
        </nav>
        <h1 class="fs-2 fw-bold text-dark-mariam mb-0">Create An Empty Project For Shell Tasks</h1>
        <p class="text-muted small mt-1">
            {Localisation::getTranslation('common_denotes_a_required_field')}
        </p>
    </div>

    {* ══════════════════════════════════════════════════════════════════════════
       FORM
       ══════════════════════════════════════════════════════════════════════════ *}
    <form method="post" action="{urlFor name="project-create-empty" options="org_id.$org_id"}"
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
                            Create An Empty Project For Shell Tasks
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
                            Project-specific Instructions <span class="text-danger">*</span>
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
                            Files
                        </h2>
                    </div>

                    <div class="row g-4">
                        {* Project image upload *}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark-mariam d-block">
                                {Localisation::getTranslation('common_project_image')}
                                <span class="home_tooltip ms-1">
                                    <i class="fa-solid fa-circle-info text-muted"></i>
                                    <span class="home_tooltiptext">
                                        Optional. If omitted, the most recent image will be reused.
                                    </span>
                                </span>
                            </label>
                            <label for="projectImageFile" class="upload-zone w-100">
                                <i class="fa-solid fa-image fs-2 mb-2"
                                   style="color: var(--core-blue);"></i>
                                <div class="fw-semibold text-dark-mariam small">
                                    Click to select
                                </div>
                                <div id="image_file_desc" class="text-muted" style="font-size:.75rem;">
                                    {Localisation::getTranslation('common_loading')}
                                </div>
                                <div class="text-muted" style="font-size:.75rem;">
                                    If you do not upload an image, the most recent will be reused.
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
                            Language
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

                    <div id="loading_warning" class="text-muted small py-2">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        {Localisation::getTranslation('common_loading')}
                    </div>
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
                            How to launch a translation project
                        </h3>
                    </div>
                    <a href="https://communitylibrary.translatorswb.org/books/12-self-managed-partners/page/launching-your-translation-project-on-the-twb-platform"
                       target="_blank"
                       class="btn btn-outline-primary w-100 fw-semibold"
                       style="color: var(--core-blue);">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>
                        Guidelines
                    </a>
                </div>

            </div>
            {* /RIGHT COLUMN *}

        </div>
        {* /row *}

        <input type="hidden" name="sesskey" value="{$sesskey}" />
        <input type="hidden" name="project_deadline" id="project_deadline" />

    </form>

</div>
{* /container-xxl *}


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
    ['projectImageFile'].forEach(function (id) {
        document.getElementById(id).addEventListener('change', function () {
            var nameEl = document.getElementById(id + '_name');
            if (nameEl) nameEl.textContent = this.files.length ? this.files[0].name : '';
        });
    });
});
</script>

{include file="footer2.tpl"}
