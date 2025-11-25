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

                        {if !empty($claimed_tasks)}
                        <div class="card bg-light custom-card p-4 card-border-top-accent">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark mb-0">My Tasks</h2>
                                <a href="{urlFor name="claimed-tasks" options="user_id.{$user_id}"}" class="text-decoration-none fw-semibold d-flex align-items-center" style="color: var(--twb-accent);">
                                    Go to My Tasks <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="space-y-4">
                                {foreach from=$claimed_tasks item=task}
                                    {assign var="task_id" value=$task->getId()}
                                    {assign var="type_id" value=$task->getTaskType()}
                                    {assign var="status_id" value=$task->getTaskStatus()}
                                    {if mb_strlen($task->getTitle()) > 50}
                                        {assign var="task_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($task->getTitle(), 0, 50))}
                                        {assign var="task_title" value="`$task_title`..."}
                                    {else}
                                        {assign var="task_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
                                    {/if}
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 shadow-sm border bg-white hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="me-1">
                                            {if !empty($org_images[$task_id])}
                                            <a href="{$siteLocation}org/{$orgs[$task_id]}/profile">
                                            <img src="data:image/jpeg;base64,{$org_images[$task_id]}" alt="Organisation logo" class="rounded-circle ms-3" width="40" height="40" />
                                            </a>
                                            {else}
                                            <img src="https://placehold.co/40x40/ED1C24/ffffff?text=TWB" alt="Organisation logo" class="rounded-circle ms-3" width="40" height="40" />
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

                        <div class="card bg-light custom-card p-4 card-border-top-blue">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark mb-0">Available Tasks</h2>
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
                                    {if mb_strlen($task->getTitle()) > 50}
                                        {assign var="task_title" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($task->getTitle(), 0, 50))}
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
                                                    <h5 class="fw-bold text-dark mb-2"><a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/view" class="custom-link text-wrap">{$task_title}</a></h5>
                                                </div>
                                                {if !empty($org_images[$task_id])}
                                                <a href="{$siteLocation}org/{$orgs[$task_id]}/profile">
                                                <img src="data:image/jpeg;base64,{$org_images[$task_id]}" alt="Organisation logo" class="rounded-circle ms-3" width="40" height="40" />
                                                </a>
                                                {else}
                                                <img src="https://placehold.co/40x40/ED1C24/ffffff?text=TWB" alt="Organisation logo" class="rounded-circle ms-3" width="40" height="40" />
                                                {/if}
                                            </div>
                                            {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                                            <p class="small text-secondary mb-2">
                                                {if $type_id != 29}<span class="fw-medium">Languages:</span> {TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())} → {TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}{/if}
                                            </p>
                                            {else}
                                            <p class="small text-secondary mb-2">
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
                                <a class="btn btn-secondary fs-5 px-3" href="{urlFor name="home"}">View More Available Tasks</a>
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
                        <div class="card bg-light custom-card p-4">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <span class="me-2 fs-5" style="color: var(--twb-accent);">📰</span>
                                <a href="archive.html" class="fs-3 fw-bold text-dark text-decoration-none hover-text-secondary">
                                    News & Updates
                                </a>
                            </div>
                            <ul class="list-unstyled space-y-4">
                                {foreach from=$news item=item}
                                <li class="border-bottom pb-3">
                                    <a href="{if empty($item['external_link'])}{$siteLocation}content_display/{$item['id']}/{else}{$item['external_link']}{/if}" class="d-block text-decoration-none text-dark hover-bg-light p-1 rounded transition-colors">
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
                                                <p class="fs-4 fw-medium text-dark mb-0">{$item['title']}</p>
                                                <p class="small text-secondary mb-0">• {substr($item['update_date'], 0, 10)}</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                {/foreach}
                            </ul>
                            <a href="#news-archive" class="mt-3 w-100 btn btn-outline-primary fw-semibold" style="color: var(--core-blue);">
                                View All News <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        {/if}

                        {if !empty($resources)}
                        <div class="card bg-light custom-card p-4">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <span class="me-2 fs-5" style="color: var(--core-blue);">📖</span>
                                <a href="archive.html" class="fs-3 fw-bold text-dark text-decoration-none hover-text-secondary">
                                    Resources & Tools
                                </a>
                            </div>
                            <ul class="list-unstyled space-y-4">
                                {foreach from=$resources item=item}
                                <li>
                                    <a href="{if empty($item['external_link'])}{$siteLocation}content_display/{$item['id']}/{else}{$item['external_link']}{/if}" class="d-flex align-items-center small text-dark fw-medium text-decoration-none hover-text-primary">
                                        <span class="me-2" style="color: var(--core-blue); opacity: 0.6;">&rarr;</span> <p class="fs-4 fw-medium text-dark mb-0">{$item['title']}</p>
                                    </a>
                                </li>
                                {/foreach}
                            </ul>
                            <a href="#news-archive" class="mt-3 w-100 btn btn-outline-primary fw-semibold" style="color: var(--core-blue);">
                                View All Resources <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        {/if}

                        <div class="card bg-light custom-card p-4 card-border-top-accent">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <div class="p-2 rounded-circle me-3" style="background-color: var(--core-blue); opacity: 0.1;">
                                    💬
                               </div>
                               <h3 class="fs-3 fw-bold text-dark mb-0">Community Survey</h3>
                            </div>
                            <p class="small text-secondary mb-3">We want to hear from you! Take our annual Community Survey and help shape future opportunities and support for volunteers. It’s anonymous and only takes 7–10 minutes.</p>
                            <a href="https://forms.translatorswb.org/view.php?id=56405" class="btn rounded-pill text-white fw-medium transition-colors" style="background-color: var(--twb-accent);">
                                💡 Submit Response
                            </a>
                        </div>

                        <div class="card bg-light custom-card p-4 card-border-top-blue">
                            <h3 class="fs-3 fw-bold text-dark mb-2">Feedback & Suggestions</h3>
                            <p class="text-secondary mb-3 small">Please share your feedback with our team to help fix any issues and deliver a better experience to our community.</p>
                            <a href="https://form.asana.com/?k=dlsF11XkOwpfFllbq325dg&d=170818793545926" class="btn rounded-pill text-white fw-medium transition-colors" style="background-color: var(--twb-accent);">
                                💡 Submit Feedback
                            </a>
                        </div>
                    </div>
                </div>
            </div>

{include file="footer2.tpl"}
