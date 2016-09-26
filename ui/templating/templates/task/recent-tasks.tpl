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
    <h3>
        {if isset($thisUser)}
            {if $thisUser->getDisplayName() != ''}
                {sprintf(Localisation::getTranslation('recent_tasks_users_recent_tasks'), {$thisUser->getDisplayName()})}
            {else}
                {Localisation::getTranslation('recent_tasks_recently_viewed_tasks')}
            {/if}
        {else}
            {Localisation::getTranslation('recent_tasks_recently_viewed_tasks')}
        {/if}
        <a href="{urlFor name="home"}" class="btn btn-primary pull-right" role="button">
            <i class="icon-arrow-left icon-white"></i> {Localisation::getTranslation('common_task_stream')}
        </a>
    </h3>
        
</div>

<div id="loading_warning">
    <p>{Localisation::getTranslation('common_loading')}</p>
</div>

<div style="max-width: 70%; overflow-wrap: break-word; word-break:break-all;">
   {if isset($recentTasks) && count($recentTasks) > 0}
        <div id="recent-tasks">
            <div class="ts">
                {for $count=0 to $itemsPerScrollPage-1}
                    {assign var="task" value=$recentTasks[$count]}
                    <div class="ts-task">
                        {assign var="task_id" value=$task->getId()}
                        {assign var="type_id" value=$task->getTaskType()}
                        {assign var="status_id" value=$task->getTaskStatus()}
                        {assign var="task_title" value=$task->getTitle()}
                        <div class="task" style="word-break: break-all; overflow-wrap: break-word;">
                            <h2>
                                <a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/id">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task_title)}</a>
                                <span class="label label-info" style="background-color: {$taskTypeColours[$type_id]}">{$taskTypeTexts[$type_id]}</span>
                                {if $task->getWordCount()}
                                    <span class="label label-info" style="background-color:rgb(57, 165, 231);">{$task->getWordCount()} {Localisation::getTranslation('project_profile_display_words')}</span>
                                {/if}
                            </h2>
                            <p>
                                {Localisation::getTranslation('common_from')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())}</strong>
                                &nbsp;|&nbsp;
                            	{Localisation::getTranslation('common_to')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                            	&nbsp;|&nbsp;
                            	<span class="label label-info" style="background-color:rgb(218, 96, 52);">{$taskStatusTexts[$status_id]}</span>
								&nbsp;|&nbsp;
								<span class="process_deadline_utc" style="display: inline-block">{$deadline_timestamps[$task_id]}</span>
                            </p>
                            
                            
                            <p>

                                {if count($taskTags[$task_id]) gt 0}
                                    {foreach $taskTags[$task_id] as $tag}
                                        <a href="{$siteLocation}tag/{$tag->getId()}" class="label"><span class="label">{trim(trim(TemplateHelper::uiCleanseHTML($tag->getLabel())),",")}</span></a>
                                    {/foreach}
                                {/if}
                            </p>
                            <p id="parents_{$task_id}">{TemplateHelper::uiCleanseNewlineAndTabs($projectAndOrgs[$task_id])}</p>
                        </div>
                    </div>
                {/for}
            </div>

            {* pagination begins here *}
            {assign var="url_name" value="recent-tasks-paged"}
            <ul class="pager pull-left">
                <div class="pagination-centered" id="ias-pagination">
                    {if $currentScrollPage > 1}
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.1"}" title="First">&lt;&lt;</a>
                        </li>
                        <li class="ts-previous">
                            {assign var="previous" value=($currentScrollPage - 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$previous"}" title="Previous">&lt;</a>
                        </li>
                    {/if}
                    <li>
                        <a href="">{sprintf(Localisation::getTranslation('pagination_page_of'), {$currentScrollPage}, {$lastScrollPage})}</a>
                    </li>
                    {if $currentScrollPage < $lastScrollPage}
                        <li class="ts-next">
                            {assign var="next" value=($currentScrollPage + 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$next"}" title="Next" >&gt;</a>
                        </li>
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$lastScrollPage"}" title="Last">&gt;&gt;</a>
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
