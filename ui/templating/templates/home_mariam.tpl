{include file="new_header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

            <div class="container">

<span class="d-none">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
</span>

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
    <div class="alert alert-success alert-dismissible fade show mt-4 ">
        <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
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
                <div class="row g-4">
                    <div class="col-lg-8 order-1 order-lg-1 space-y-8">

                        {if $org_id}
                        <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark-mariam mb-0">Current projects</h2>
                                <div class="d-flex gap-2">
                                    <a href="{urlFor name="ngo_projects"   options="org_id.{$org_id}"}" class="btn text-white fw-bold px-3 py-1" style="background-color: #f7941d; border: none;">All projects</a>

                                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                                    <a href="{urlFor name="project-create" options="org_id.{$org_id}"}" class="btn text-white fw-bold px-3 py-1" style="background-color: #f7941d; border: none;">+ New project</a>
                                    {/if}

                                    {if $roles&($SITE_ADMIN + $PROJECT_OFFICER) || in_array($org_id, $ORG_EXCEPTIONS) && $roles&($NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                                    <a href="{urlFor name="project-create-empty" options="org_id.{$org_id}"}" class="btn text-white fw-bold px-3 py-1" style="background-color: #f7941d; border: none;">+ New non-Phrase project</a>
                                    {/if}
                                </div>
                            </div>

                            {if !empty($current_projects)}
                            <div class="row g-0 py-2 border-bottom d-md-flex text-muted small">
                                <div class="col-1">Status</div>
                                <div class="col-4">Title</div>
                                <div class="col-2">Progress</div>
                                <div class="col-2">Due date</div>
                                <div class="col-3">Target languages</div>
                            </div>

                            {foreach from=$current_projects item=project}
                            {assign var="project_id" value={$project['id']}}
                            <div class="row g-0 py-3 border-bottom align-items-center">
                                <div class="col-1">
                                    {if $project['number_overdue']}
                                        <i class="bi bi-exclamation-circle-fill home_tooltip"><span class="home_tooltiptext">Overdue tasks</span></i>
                                    {elseif $project['status'] == 2} <!-- All at least in progress -->
                                        <i class="bi bi-circle-half home_tooltip"><span class="home_tooltiptext">In progress</span></i>
                                    {else}
                                        <i class="bi bi-circle-half home_tooltip"><span class="home_tooltiptext">In progress</span></i>
                                    {/if}
                                </div>
                                <div class="col-11 col-md-4">
                                    <a href="{urlFor name="project-view" options="project_id.{$project_id}"}" class="text-decoration-none fw-bold text-dark-mariam d-block">
                                        {if mb_strlen($project['title']) > 31}
                                            {assign var="project_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($project['title'], 0, 31))}
                                            {assign var="project_title" value="`$project_title`..."}
                                        {else}
                                            {assign var="project_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs($project['title'])}
                                        {/if}
                                        {$project_title}
                                    </a>
                                </div>
                                <div class="col-6 col-md-2 mt-2 mt-md-0">
                                    <div class="progress" style="height: 12px; border-radius: 10px; background-color: #f0f0f0; width: 80%;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {$project['fraction']*100}%; border-radius: 10px;"></div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2 mt-2 mt-md-0 small">
                                    <span class="convert_utc_to_local_deadline_day_mon_year" style="visibility: hidden">{$project['deadline']}</span>
                                </div>
                                <div class="col-12 col-md-3 mt-2 mt-md-0">
                                    <div class="d-flex gap-1 flex-wrap">
                                        {if !empty($project['codes'])}
                                        {assign var="codes" value=explode(',', $project['codes'])}
                                            {foreach from=$codes item=code}
                                                <span class="badge border text-dark-mariam fw-normal bg-light-mariam px-2 py-1">{$code}</span>
                                            {/foreach}
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            {/foreach}
                            {else}
                            <div class="text-center mt-4">
                                There are currently no projects.
                            </div>
                            {/if}
                        </div>

                        <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark-mariam mb-0">Recently completed files</h2>
                                <div class="d-flex gap-2">
                                    <a href="{urlFor name="ngo_projects" options="org_id.{$org_id}"}" class="btn text-white fw-bold px-3 py-1" style="background-color: #f7941d; border: none;">All projects</a>
                                </div>
                            </div>

                            {if !empty($completed_files)}
                            <div class="row g-0 py-2 border-bottom d-md-flex text-muted small">
                                <div class="col-6">File title</div>
                                <div class="col-3">Word count</div>
                                <div class="col-3">Target languages</div>
                            </div>

                            {foreach from=$completed_files item=file}
                            <div class="row g-0 py-3 border-bottom align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="mb-1">
                                        {if mb_strlen($file['p_title']) > 31}
                                            {assign var="project_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($file['p_title'], 0, 31))}
                                            {assign var="project_title" value="`$project_title`..."}
                                        {else}
                                            {assign var="project_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs($file['p_title'])}
                                        {/if}
                                        <span class="badge bg-light-mariam text-muted border fw-normal">{$project_title}</span>
                                    </div>
                                    <div class="text-decoration-none fw-bold text-dark-mariam d-block">
                                        {if mb_strlen($file['t_filename']) > 31}
                                            {assign var="file_name" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($file['t_filename'], 0, 31))}
                                            {assign var="file_name" value="`$file_name`..."}
                                        {else}
                                            {assign var="file_name" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs($file['t_filename'])}
                                        {/if}
                                        {$file_name}
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mt-2 mt-md-0 small">
                                    {$file['t_wordcount']} {TaskTypeEnum::$enum_to_UI[$file['t_type']]['unit_count_text_short']}
                                </div>
                                <div class="col-6 col-md-3 mt-2 mt-md-0">
                                    <div class="d-flex gap-1 flex-wrap">
                                        {if !empty($file['codes'])}
                                        {assign var="codes" value=explode(',', $file['codes'])}
                                            {foreach from=$codes item=item}
                                                {assign var="code_id_status" value=explode(';', $item)}
                                                {if !TaskTypeEnum::$enum_to_UI[$code_id_status[3]]['shell_task'] && $code_id_status[2] == 4}
                                                    <a href="{urlFor name="download-task-latest-version" options="task_id.{$code_id_status[1]}"}"><span class="badge border text-dark-mariam fw-normal bg-light-mariam px-2 py-1 home_tooltip">{$code_id_status[0]}<i class="fa-solid fa-arrow-down small ms-1"></i><span class="home_tooltiptext">Download</span></span></a>
                                                {elseif TaskTypeEnum::$enum_to_UI[$code_id_status[3]]['shell_task'] && $code_id_status[2] == 4}
                                                    <span class="badge border text-dark-mariam fw-normal bg-light-mariam px-2 py-1 home_tooltip">{$code_id_status[0]}<span class="home_tooltiptext">Complete</span></span>
                                                {else}
                                                    <span class="badge border text-dark-mariam fw-normal bg-light-mariam px-2 py-1 home_tooltip">{$code_id_status[0]}<span class="home_tooltiptext">In progress</span></span>
                                                {/if}
                                            {/foreach}
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            {/foreach}
                            {else}
                            <div class="text-center mt-4">
                                There are currently no files.
                            </div>
                            {/if}
                        </div>
                        {/if}

                        {if !empty($claimed_tasks)}
                        <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark-mariam mb-0">My Tasks</h2>
                                <a href="{urlFor name="claimed-tasks" options="user_id.{$user_id}"}" class="text-decoration-none fw-semibold d-flex align-items-center" style="color: var(--twb-accent);">
                                    Go to My Tasks <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="space-y-4">
                                {foreach from=$claimed_tasks item=task}
                                    {assign var="task_id" value=$task->getId()}
                                    {assign var="type_id" value=$task->getTaskType()}
                                    {assign var="status_id" value=$task->getTaskStatus()}
                                    {if mb_strlen($task->getTitle()) > 31}
                                        {assign var="task_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($task->getTitle(), 0, 31))}
                                        {assign var="task_title" value="`$task_title`..."}
                                    {else}
                                        {assign var="task_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
                                    {/if}
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 shadow-sm border bg-light-mariam hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            {if !empty($org_images[$task_id])}
                                            <a href="{$siteLocation}org/{$orgs[$task_id]}/profile">
                                            <img src="data:image/jpeg;base64,{$org_images[$task_id]}" alt="{$org_names[$task_id]} logo" title="{$org_names[$task_id]}" class="rounded-circle" width="60" height="60" />
                                            </a>
                                            {else}
                                            <img src="https://placehold.co/60x60/ED1C24/ffffff?text=TWB" alt="{$org_names[$task_id]} logo" title="{$org_names[$task_id]}" class="rounded-circle" width="60" height="60" />
                                            {/if}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-md">
                                                <a id="taskc-{$task_id}" href="{$siteLocation}task/{$task_id}/view" class="custom-link text-wrap">{$task_title}</a>
                                                <span class="badge rounded-pill text-uppercase fs-7 fw-bold" style="background-color:{TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']}</span>
                                            </div>
                                            {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                                                <p class="text-muted small mb-0">{if $type_id != 29}{TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())} → {TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())} |{/if}
                                                    ({if $status_id == 3 && empty($matecat_urls[$task_id])}Claimed{elseif $status_id == 3}In Progress{else}Complete{/if}{if $task->get_cancelled()} (Cancelled){/if})
                                                </p>
                                            {else}
                                                <p class="text-muted small mb-0">{if $type_id != 29}{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())} |{/if}
                                                    ({if $status_id == 3 && empty($matecat_urls[$task_id])}Claimed{elseif $status_id == 3}In Progress{else}Complete{/if}{if $task->get_cancelled()} (Cancelled){/if})
                                                </p>
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <p class="small fw-medium text-danger d-flex align-items-center mb-0">
                                            <span class="process_deadline_utc_new_home_if_possible" style="visibility: hidden"> {$deadline_timestamps[$task_id]}</span>
                                        </p>
                                    </div>
                                </div>
                                {/foreach}
                            </div>
                        </div>
                        {/if}

                        <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark-mariam mb-0">Available Tasks</h2>
                                <a href="{urlFor name="task_stream"}" class="text-decoration-none fw-semibold d-flex align-items-center" style="color: var(--core-blue);">
                                    Browse All Tasks <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>

                            {if !empty($tasks)}
                            <div class="space-y-4">
                                {assign var="count" value=0}
                                {foreach from=$tasks item=task}
                                    {assign var="task_id" value=$task->getId()}
                                    {assign var="type_id" value=$task->getTaskType()}
                                    {if mb_strlen($task->getTitle()) > 31}
                                        {assign var="task_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($task->getTitle(), 0, 31))}
                                        {assign var="task_title" value="`$task_title`..."}
                                    {else}
                                        {assign var="task_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
                                    {/if}
                              {if $count%2 == 0}
                                <div class="row g-4">
                              {/if}
                                    <div class="col-md-6">
                                        <div class="card custom-card p-4 h-100 card-border-start-accent">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="mb-2">
                                                        <span class="badge rounded-pill text-uppercase fs-7 fw-bold" style="background-color:{TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']}</span>
                                                        {if $task->getWordCount()}
                                                        <span class="badge rounded-pill ms-1 task-unit-badge fs-7 fw-bold">{$task->getWordCount()} {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text_short']}</span>
                                                        {/if}
                                                        {if isset($chunks[$task_id])}
                                                        <span class="badge rounded-pill ms-1 fs-7 fw-bold" style="background-color:#7B61FF">Part {$chunks[$task_id]['low_level']}/{$chunks[$task_id]['number_of_chunks']}</span>
                                                        {/if}
                                                    </div>
                                                    <h5 class="fw-bold text-dark-mariam mb-2"><a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/view" class="custom-link text-wrap">{$task_title}</a></h5>
                                                </div>
                                                {if !empty($org_images[$task_id])}
                                                <a href="{$siteLocation}org/{$orgs[$task_id]}/profile">
                                                <img src="data:image/jpeg;base64,{$org_images[$task_id]}" alt="{$org_names[$task_id]} logo" title="{$org_names[$task_id]}" class="rounded-circle ms-3" width="60" height="60" />
                                                </a>
                                                {else}
                                                <img src="https://placehold.co/60x60/ED1C24/ffffff?text=TWB" alt="{$org_names[$task_id]} logo" title="{$org_names[$task_id]}" class="rounded-circle ms-3" width="60" height="60" />
                                                {/if}
                                            </div>
                                            {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                                            <p class="small text-muted mb-2">
                                                {if $type_id != 29}<span class="fw-medium">Languages:</span> {TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())} → {TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}{/if}
                                            </p>
                                            {else}
                                            <p class="small text-muted mb-2">
                                                {if $type_id != 29}<span class="fw-medium">Language:</span> {TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}{/if}
                                            </p>
                                            {/if}
                                            </p>
                                            <p class="small fw-medium text-danger d-flex align-items-center mb-3">
                                            <span class="process_deadline_utc_new_home_if_possible" style="visibility: hidden"> {$deadline_timestamps[$task_id]}</span>
                                            </p>
                                            <a class="btn btn-secondary fs-5 px-3" href="{$siteLocation}task/{$task_id}/view">View Task</a>
                                        </div>
                                    </div>
                              {if $count%2 == 0 && $count == count($tasks) - 1}
                                    <div class="col-md-6">
                                    </div>
                                </div>
                              {/if}
                              {if $count%2 == 1}
                                </div>
                              {/if}
                                {assign var="count" value=($count + 1)}
                                {/foreach}
                            </div>
                            <div class="text-center mt-4">
                                <a class="btn btn-secondary fs-5 px-3" href="{urlFor name="task_stream"}">View More Available Tasks</a>
                            </div>
                            {else}
                            <div class="text-center mt-4">
                                It looks like we don't have any tasks available for your language pair at the moment. In the meantime, you can take a course from our Learning Center:<br />
                                <a class="btn btn-secondary fs-5 px-3" href="https://elearn.translatorswb.org/">Learning Center</a>
                            </div>
                            {/if}
                        </div>

                    </div>

                    <div class="col-lg-4 order-2 order-lg-2 space-y-8">

                        {if !empty($news)}
                        <div class="card bg-light-mariam custom-card p-4">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <span class="me-2 fs-5" style="color: var(--twb-accent);">📰</span>
                                <a href="{$siteLocation}content_list/1/" class="fs-3 fw-bold text-dark-mariam text-decoration-none hover-text-secondary">
                                    News & Updates
                                </a>
                            </div>
                            <ul class="list-unstyled space-y-4">
                                {foreach from=$news item=item}
                                <li class="border-bottom pb-3">
                                    <a href="{if empty($item['external_link'])}{$siteLocation}content_display/{$item['id']}/{else}{$item['external_link']}{/if}" {if !empty($item['external_link'])}target="_blank" click_id="{$item['id']}" sesskey="{$sesskey}"{/if} class="d-block text-decoration-none text-dark-mariam hover-bg-light p-1 rounded transition-colors {if !empty($item['external_link'])}count_external_clicks{/if}">
                                        <div class="d-flex align-items-start">
                                            {if $item['type'] == 11}
                                            <span class="me-2 mt-1 flex-shrink-0" style="color: var(--twb-accent);">🗞</span>
                                            {elseif $item['type'] == 13}
                                            <span class="me-2 mt-1 flex-shrink-0" style="color: var(--twb-accent);">💡</span>
                                            {elseif $item['type'] == 12}
                                            <span class="me-2 mt-1 flex-shrink-0" style="color: var(--twb-accent);">📰</span>
                                            {elseif $item['type'] == 14}
                                            <span class="me-2 mt-1 flex-shrink-0" style="color: var(--twb-accent);">📊</span>
                                            {/if}
                                            <div>
                                                <p class="fs-4 fw-medium text-dark-mariam mb-0">{$item['title']}</p>
                                                <p class="small text-muted mb-0">• {substr($item['update_date'], 0, 10)}</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                {/foreach}
                            </ul>
                            <a href="{$siteLocation}content_list/1/" class="mt-3 w-100 btn btn-outline-primary fw-semibold" style="color: var(--core-blue);">
                                View All News <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        {/if}

                        {if !empty($resources)}
                        <div class="card bg-light-mariam custom-card p-4">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <span class="me-2 fs-5" style="color: var(--core-blue);">📖</span>
                                <a href="{$siteLocation}content_list/{$resources[0]['type']/10}/" class="fs-3 fw-bold text-dark-mariam text-decoration-none hover-text-secondary">
                                    Resources & Tools
                                </a>
                            </div>
                            <ul class="list-unstyled space-y-4">
                                {foreach from=$resources item=item}
                                <li>
                                    <a href="{if empty($item['external_link'])}{$siteLocation}content_display/{$item['id']}/{else}{$item['external_link']}{/if}" {if !empty($item['external_link'])}target="_blank" click_id="{$item['id']}" sesskey="{$sesskey}"{/if} class="d-flex align-items-center small text-dark-mariam fw-medium text-decoration-none hover-text-primary {if !empty($item['external_link'])}count_external_clicks{/if}">
                                        <span class="me-2" style="color: var(--core-blue); opacity: 0.6;">&rarr;</span> <p class="fs-4 fw-medium text-dark-mariam mb-0">{$item['title']}</p>
                                    </a>
                                </li>
                                {/foreach}
                            </ul>
                            <a href="{$siteLocation}content_list/2/" class="mt-3 w-100 btn btn-outline-primary fw-semibold" style="color: var(--core-blue);">
                                View All Resources <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        {/if}

<!--
                        <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
<iframe onload="javascript:parent.scrollTo(0,0);" height="449" allowTransparency="true" scrolling="no" frameborder="0" sandbox="allow-forms allow-modals allow-orientation-lock allow-pointer-lock allow-popups allow-popups-to-escape-sandbox allow-presentation allow-same-origin allow-scripts allow-top-navigation allow-top-navigation-by-user-activation" style="width:100%;border:none" src="https://forms.translatorswb.org/embed.php?id=58294" title="Quick Survey"><a href="https://forms.translatorswb.org/view.php?id=58294" title="Quick Survey">Quick Survey</a></iframe>
                        </div>
-->

                        <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <div class="p-2 rounded-circle me-3" style="background-color: var(--core-blue); opacity: 0.1;">
                                    💬
                               </div>
                               <h3 class="fs-3 fw-bold text-dark-mariam mb-0">TWB 2025 Community Survey Results!</h3>
                            </div>
                            <!-- <p class="small text-muted mb-3">We want to hear from you! Take our annual Community Survey and help shape future opportunities and support for volunteers. It’s anonymous and only takes 7–10 minutes.</p> -->
                            <a href="https://twbplatform.org/user_TWB_Dashboard_Final.html" click_id="8" sesskey="{$sesskey}" class="btn rounded-pill text-white fw-medium transition-colors count_external_clicks" style="background-color: var(--twb-accent);">
                                💡 See the Results
                            </a>
                        </div>

                        <div class="card bg-light-mariam custom-card p-4 card-border-top-blue">
                            <h3 class="fs-3 fw-bold text-dark-mariam mb-2">Feedback & Suggestions</h3>
                            <p class="text-muted mb-3 small">Please share your feedback with our team to help fix any issues and deliver a better experience to our community.</p>
                            <a href="https://form.asana.com/?k=dlsF11XkOwpfFllbq325dg&d=170818793545926" class="btn rounded-pill text-white fw-medium transition-colors" style="background-color: var(--twb-accent);">
                                💡 Submit Feedback
                            </a>
                        </div>
                    </div>
                </div>
            </div>

{include file="footer2.tpl"}
