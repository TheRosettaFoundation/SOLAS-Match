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

                <div class="col-lg-12 space-y-8">

                    <div class="card bg-light-mariam custom-card p-4 shadow-sm" style="border-top: 4px solid var(--twb-accent);">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h2 class="fs-2 fw-bold text-dark-mariam mb-0">Projects</h2>
                        </div>

                        {if !empty($current_projects)}
                        <div class="row g-0 py-2 d-md-flex text-muted small px-3 border-bottom">
                            <div class="col-md-4 ps-4">Title</div>
                            <div class="col-md-1 text-center">Progress</div>
                            <div class="col-md-1 text-center">Created</div>
                            <div class="col-md-1 text-center">Due date</div>
                            <div class="col-md-2 text-center">Word count</div>
                            <div class="col-md-3">Target languages</div>
                        </div>

                        {foreach from=$current_projects item=project}
                            {assign var="project_id" value={$project['id']}}
            
                            <div class="row g-0 py-3 align-items-center px-3 border-bottom hover-bg-light" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#project-group-{$project_id}" 
                                 style="cursor: pointer;">
                
                                <div class="col-12 col-md-4 fw-bold text-dark-mariam d-flex align-items-center">
                                    <i class="fa-solid fa-chevron-right me-3 small text-muted transition-icon"></i>
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

                                <div class="col-6 col-md-1 mt-2 mt-md-0 d-flex justify-content-center">
                                    <div class="progress w-100" style="height: 14px; border-radius: 4px; background-color: #e9ecef; max-width: 80px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {$project['fraction']*100}%; border-radius: 4px;"></div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-1 mt-2 mt-md-0 text-center small">
                                    <span class="convert_utc_to_local_deadline_day_mon_year" style="visibility: hidden">{$project['created']}</span>
                                </div>

                                <div class="col-6 col-md-1 mt-2 mt-md-0 text-center small text-danger fw-medium">
                                    <span class="convert_utc_to_local_deadline_day_mon_year" style="visibility: hidden">{$project['deadline']}</span>
                                </div>

                                <div class="col-6 col-md-2 mt-2 mt-md-0 text-center small">
                                </div>

                                <div class="col-12 col-md-3 mt-2 mt-md-0 d-flex gap-1 flex-wrap">
                                    {if !empty($project['codes'])}
                                        {assign var="codes" value=explode(',', $project['codes'])}
                                        {foreach from=$codes item=code}
                                            <span class="badge border text-dark-mariam fw-normal bg-white px-2 py-1">{$code}</span>
                                        {/foreach}
                                    {/if}
                                </div>
                            </div>

                            <div class="collapse" id="project-group-{$project_id}">
                                <div>
                                    <div class="row g-0 py-2 ps-5 pe-3 border-bottom align-items-center">
                                        {foreach from=$completed_files item=file}
                                            {if $file['id'] == $project_id}
                                                <div class="col-md-4 small text-muted">
                                                    {if mb_strlen($file['t_filename']) > 31}
                                                        {assign var="file_name" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs(mb_substr($file['t_filename'], 0, 31))}
                                                        {assign var="file_name" value="`$file_name`..."}
                                                    {else}
                                                        {assign var="file_name" value=TemplateHelper::uiCleanseHTMLNewlineAndTabs($file['t_filename'])}
                                                    {/if}
                                                    {$file_name}
                                                </div>
                                                <div class="col-md-1 offset-md-4 text-center small">
                                                    {$file['t_wordcount']} {TaskTypeEnum::$enum_to_UI[$file['t_type']]['unit_count_text_short']}
                                                </div>
                                                <div class="col-md-3 d-flex gap-1 flex-wrap">
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
                                            {/if}
                                        {/foreach}
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
                </div>
            </div>

{include file="footer2.tpl"}
