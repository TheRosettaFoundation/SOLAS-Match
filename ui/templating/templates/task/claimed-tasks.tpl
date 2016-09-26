{include file='header.tpl'}

<span class="hidden">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
</span>

{if isset($flash['error'])}
    <br>
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTML($flash['error'])}</p>
    </div>
{/if}

<div class="page-header">
    <h1>
        {if isset($thisUser)}
            {if $thisUser->getDisplayName() != ''}
                {sprintf(Localisation::getTranslation('claimed_tasks_users_claimed_tasks'), {$thisUser->getDisplayName()})}
            {else}
                {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
            {/if}
        {else}
            {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
        {/if}
        <small>{Localisation::getTranslation('claimed_tasks_a_list_of_tasks')}</small>
    </h1>
</div>

<div id="loading_warning">
    <p>{Localisation::getTranslation('common_loading')}</p>
</div>

<div style="max-width: 70%; overflow-wrap: break-word; word-break:break-all;">
    <h3>{Localisation::getTranslation('index_filter_available_tasks')}</h3>
    <div id="filter-container">
        <form method="post" action="{urlFor name="claimed-tasks" options="user_id.$user_id"}">
            <div class="filter-block">
                <div class="filter-title">{Localisation::getTranslation('common_task_type')}</div>
                <select name="taskTypes" id="taskTypes">
                    <option value="0" {if ($selectedTaskType === 0)}selected="selected"{/if}>{Localisation::getTranslation('index_any_task_type')}</option>
                    <option value="1" {if ($selectedTaskType === 1)}selected="selected"{/if}>{Localisation::getTranslation('common_segmentation')}</option>
                    <option value="2" {if ($selectedTaskType === 2)}selected="selected"{/if}>{Localisation::getTranslation('common_translation')}</option>
                    <option value="3" {if ($selectedTaskType === 3)}selected="selected"{/if}>{Localisation::getTranslation('common_proofreading')}</option>
                    <option value="4" {if ($selectedTaskType === 4)}selected="selected"{/if}>{Localisation::getTranslation('common_desegmentation')}</option>
                </select>
            </div>
            <div class="filter-block">
                <div class="filter-title">{Localisation::getTranslation('common_task_status')}</div>
                <select name="taskStatusFilter" id="taskStatusFilter">
                    <option value="3" {if ($selectedTaskStatus === 3)}selected="selected"{/if}>{Localisation::getTranslation('common_in_progress')}</option>
                    <option value="0" {if ($selectedTaskStatus === 0)}selected="selected"{/if}>{Localisation::getTranslation('common_any_task_status')}</option>
                    <option value="4" {if ($selectedTaskStatus === 4)}selected="selected"{/if}>{Localisation::getTranslation('common_complete')}</option>
                </select>
            </div>
            <div class="filter-block">
                <div class="filter-title">{Localisation::getTranslation('claimed_tasks_ordering')}</div>
                <select name="ordering" id="ordering">
                    <option value="0" {if ($selectedOrdering === 0)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_created_asc')}</option>
                    <option value="1" {if ($selectedOrdering === 1)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_created_desc')}</option>
                    <option value="2" {if ($selectedOrdering === 2)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_deadline_asc')}</option>
                    <option value="3" {if ($selectedOrdering === 3)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_deadline_desc')}</option>
                    <option value="4" {if ($selectedOrdering === 4)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_title_asc')}</option>
                    <option value="5" {if ($selectedOrdering === 5)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_title_desc')}</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">
               <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('index_filter_task_stream')}
            </button>
        </form>
    </div>

   {if isset($topTasks) && count($topTasks) > 0}
        <div id="claimed-tasks">
            <div class="ts">
                {for $count=0 to $itemsPerScrollPage-1}
                    {assign var="task" value=$topTasks[$count]}
                    <div class="ts-task">
                        {assign var="task_id" value=$task->getId()}
                        {assign var="type_id" value=$task->getTaskType()}
                        {assign var="status_id" value=$task->getTaskStatus()}
                        {assign var="task_title" value=$task->getTitle()}
                        <div class="task" style="word-break: break-all; overflow-wrap: break-word;">
                            <h2>
                                <a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/id">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task_title)}</a>
                            </h2>
                            <p>
                                {Localisation::getTranslation('common_type')}: <span class="label label-info" style="background-color: {$taskTypeColours[$type_id]}">{$taskTypeTexts[$type_id]}</span>
                            </p>
                            <p>
                                {Localisation::getTranslation('common_from')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())}</strong>
                            </p>
                            <p>
                                {Localisation::getTranslation('common_to')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                            </p>
                            <p>
                                {Localisation::getTranslation('common_status')}: <strong>{$taskStatusTexts[$status_id]}</strong>
                            </p>
                            <p>
                                {if count($taskTags[$task_id]) gt 0}
                                    {foreach $taskTags[$task_id] as $tag}
                                        <a href="{$siteLocation}tag/{$tag->getId()}" class="label"><span class="label">{trim(trim(TemplateHelper::uiCleanseHTML($tag->getLabel())),",")}</span></a>
                                    {/foreach}
                                {/if}
                            </p>
                            <p>
                                {if $task->getWordCount()}
                                    {Localisation::getTranslation('common_word_count')}: <strong>{$task->getWordCount()}</strong>
                                {/if}
                            </p>
                            <p class="task_details"><div class="process_created_time_utc" style="visibility: hidden">{$created_timestamps[$task_id]}</div></p>
                            <p><div class="process_deadline_utc" style="visibility: hidden">{$deadline_timestamps[$task_id]}</div></p>
                            <p id="parents_{$task_id}">{TemplateHelper::uiCleanseNewlineAndTabs($projectAndOrgs[$task_id])}</p>
                            <p>
                               {if $status_id == 3 && ($type_id == 3 || $type_id == 2)}
                                    <a href="{$siteLocation}task/{$task_id}/simple-upload" class="btn btn-small btn-success">
                                        {Localisation::getTranslation('claimed_tasks_submit_completed_task')}
                                    </a>
                                {/if}
                                {if $status_id == 3 && $type_id == 1}
                                    <a href="{$siteLocation}task/{$task_id}/segmentation" class="btn btn-small btn-primary">
                                        {Localisation::getTranslation('claimed_tasks_submit_completed_task')}
                                    </a>
                                {/if}
                                {if $status_id == 3 && $type_id == 4}
                                    <a href="{$siteLocation}task/{$task_id}/desegmentation" class="btn btn-small btn-primary">
                                        {Localisation::getTranslation('claimed_tasks_submit_completed_task')}
                                    </a>
                                {/if}
                                <a href="{$siteLocation}user/task/{$task_id}/reviews" class="btn btn-small btn-primary">
                                    {Localisation::getTranslation('claimed_tasks_task_reviews')}
                                </a>
                                {if $status_id == 3}
                                    <a href="{$siteLocation}task/{$task_id}/user-feedback" class="btn btn-small btn-danger">
                                        {Localisation::getTranslation('claimed_tasks_unclaim_task')}
                                    </a>
                                {/if}
                                {if $type_id == 2}
                                    {if $proofreadTaskIds[$task_id]}
                                        <a href="{$siteLocation}task/{$proofreadTaskIds[$task_id]}/download-task-latest-file/" class="btn btn-small btn-info">
                                            {Localisation::getTranslation('claimed_tasks_download_proofread_task')}
                                        </a>
                                    {/if}
                                {/if}
                            </p>
                            <br/>
                        </div>
                    </div>
                {/for}
            </div>

            {* pagination begins here *}
            {assign var="url_name" value="claimed-tasks-paged"}
            <ul class="pager pull-left">
                <div class="pagination-centered" id="ias-pagination">
                    {if $currentScrollPage > 1}
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.1|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="First">&lt;&lt;</a>
                        </li>
                        <li class="ts-previous">
                            {assign var="previous" value=($currentScrollPage - 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$previous|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Previous">&lt;</a>
                        </li>
                    {/if}
                    <li>
                        <a href="">{sprintf(Localisation::getTranslation('pagination_page_of'), {$currentScrollPage}, {$lastScrollPage})}</a>
                    </li>
                    {if $currentScrollPage < $lastScrollPage}
                        <li class="ts-next">
                            {assign var="next" value=($currentScrollPage + 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$next|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Next" >&gt;</a>
                        </li>
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$lastScrollPage|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Last">&gt;&gt;</a>
                        </li>
                    {/if}
                </div>
            </ul>
        </div>
    {else}
        <p>{Localisation::getTranslation('index_no_tasks_available')}</p>
    {/if}
</div>
<br/>
<div style="float:left">
    {include file='footer.tpl'}
</div>
