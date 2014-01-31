<table class="table table-striped">
    <thead>
        <th style="text-align: left"><strong>{Localisation::getTranslation(Strings::COMMON_PROJECT)}</strong></th>
        <th>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)}</th>
        <th>{Localisation::getTranslation(Strings::COMMON_TARGET_LANGUAGE)}</th>
        <th>{Localisation::getTranslation(Strings::COMMON_CREATED)}</th>
        <th>{Localisation::getTranslation(Strings::COMMON_TASK_DEADLINE)}</th>
        <th>{Localisation::getTranslation(Strings::COMMON_WORD_COUNT)}</th>
        {if isset($isOrgMember)}<th>{Localisation::getTranslation(Strings::COMMON_STATUS)}</th>{/if}
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left; word-break:break-all; width: 150px">
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view" options="project_id.$projectId"}">
                    {$project->getTitle()}
                    </a>
                {/if}
            </td>
            
            <td>
                {TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}
            </td>
            <td>
                {TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}
            </td>
            <td>
                {date(Settings::get("ui.date_format"), strtotime($task->getCreatedTime()))}
            </td>
            <td>
                {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}
            </td>
            <td>
                {if $task->getWordCount() != ''}
                    {$task->getWordCount()}
                {else}
                    -
                {/if}
            </td>
            {if isset($isOrgMember)}
                <td>
                    {assign var="status_id" value=$task->getTaskStatus()}
                    {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                        {Localisation::getTranslation(Strings::COMMON_WAITING)}
                    {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                        {Localisation::getTranslation(Strings::COMMON_UNCLAIMED)}
                    {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                        {Localisation::getTranslation(Strings::COMMON_IN_PROGRESS)}
                    {elseif $status_id == TaskStatusEnum::COMPLETE}
                        {Localisation::getTranslation(Strings::COMMON_COMPLETE)}
                    {/if}
                </td>
            {/if}
        </tr>
    </tbody>
</table>

<div class="well">
    <table width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
        <thead>
            <th width="48%" align="left">{Localisation::getTranslation(Strings::COMMON_TASK_COMMENT)}<hr/></th>
            <th></th>
            <th width="48%" align="left">{Localisation::getTranslation(Strings::COMMON_PROJECT_DESCRIPTION)}<hr/></th>
        </thead>
        <tbody>
            <tr style="overflow-wrap: break-word;" valign="top">
                <td>
                    <i>
                        {if $task->getComment() != ''}
                            {$task->getComment()}
                        {else}
                            {Localisation::getTranslation(Strings::COMMON_NO_COMMENT_HAS_BEEN_LISTED)}
                        {/if}
                    </i>
                </td>
                <td></td>
                <td>
                    <i>
                        {if $project->getDescription() != ''}
                            {$project->getDescription()}
                        {else}
                            {Localisation::getTranslation(Strings::COMMON_NO_DESCRIPTION_HAS_BEEN_LISTED)}
                        {/if}
                    </i>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 40px"/>
            </tr>
            <tr>
                <td>
                    <strong>{Localisation::getTranslation(Strings::TASK_DETAILS_PROJECT_IMPACT)}</strong><hr/>
                </td>
                <td></td>
                <td>
                    <strong>{Localisation::getTranslation(Strings::TASK_DETAILS_PROJECT_TAGS)}</strong><hr/>
                </td>
            </tr>
            <tr valign="top">                
                <td>
                    <i>
                    {if $project->getImpact() != ''}
                        {$project->getImpact()}
                    {else}
                        {Localisation::getTranslation(Strings::COMMON_NO_IMPACT_HAS_BEEN_LISTED)}
                    {/if}  
                    </i> 
                </td>    
                <td></td>
                <td>
                    {foreach from=$project->getTagList() item=tag}
                        <a class="tag label" href="{urlFor name="tag-details" options="id.{$tag->getId()}"}">{$tag->getLabel()}</a>
                    {/foreach}
                </td>                    
            </tr>
        </tbody>
    </table>
</div>

{if isset($isOrgMember)}
    <table width="100%" class="table table-striped">
        <thead>
            <th>{Localisation::getTranslation(Strings::COMMON_PUBLISH_TASK)}</th>
            <th>{Localisation::getTranslation(Strings::COMMON_TRACKING)}</th>
        </thead>
        <tr align="center">
            <td>
                {assign var="task_id" value=$task->getId()}
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $task->getPublished() == 1}
                        <input type="hidden" name="published" value="0" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-remove-circle icon-white"></i> {Localisation::getTranslation(Strings::COMMON_UNPUBLISH)}
                        </a>
                    {else}
                        <input type="hidden" name="published" value="1" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-check icon-black"></i> {Localisation::getTranslation(Strings::COMMON_PUBLISH)}
                        </a>
                    {/if}
                </form>
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $taskMetaData[$task_id]['tracking']}
                        <input type="hidden" name="track" value="Ignore" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-inbox icon-white"></i> {Localisation::getTranslation(Strings::COMMON_UNTRACK_TASK)}
                        </a>
                    {else}
                        <input type="hidden" name="track" value="Track" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-envelope icon-black"></i> {Localisation::getTranslation(Strings::COMMON_TRACK_TASK)}
                        </a>
                    {/if}
                </form>
            </td>
        </tr>
    </table>
{/if}
