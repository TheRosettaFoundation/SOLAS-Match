<table class="table table-striped">
    <thead>
        <th style="text-align: left"><strong>{Localisation::getTranslation('common_project')}</strong></th>
        <th>{Localisation::getTranslation('common_source_language')}</th>
        <th>{Localisation::getTranslation('common_target_language')}</th>
        <th>{Localisation::getTranslation('common_created')}</th>
        <th>{Localisation::getTranslation('common_task_deadline')}</th>
        <th>{Localisation::getTranslation('common_word_count')}</th>
        {if isset($isMember)}<th>{Localisation::getTranslation('common_status')}</th>{/if}
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left; word-break:break-all; width: 150px">
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view" options="project_id.$projectId"}">
                    {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}
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
                <div class="convert_utc_to_local" style="visibility: hidden">{$task->getCreatedTime()}</div>
            </td>
            <td>
                <div class="convert_utc_to_local" style="visibility: hidden">{$task->getDeadline()}</div>
            </td>
            <td>
                <span class="hidden">
                    <div id="siteLocationURL">{Settings::get("site.location")}</div>
                    <div id="project_id_for_updated_wordcount">{$task->getProjectId()}</div>
                </span>
                <div id="put_updated_wordcount_here">{if $task->getWordCount() != '' && $task->getWordCount() > 1}{$task->getWordCount()}{else}-{/if}</div>
            </td>
            {if isset($isMember)}
                <td>
                    {assign var="status_id" value=$task->getTaskStatus()}
                    {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                        {Localisation::getTranslation('common_waiting')}
                    {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                        {Localisation::getTranslation('common_unclaimed')}
                    {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                        {Localisation::getTranslation('common_in_progress')}
                    {elseif $status_id == TaskStatusEnum::COMPLETE}
                        {Localisation::getTranslation('common_complete')}
                    {/if}
                </td>
            {/if}
        </tr>
    </tbody>
</table>

<div class="well">
    <table width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
        <thead>
            <th width="48%" align="left">{Localisation::getTranslation('common_task_comment')}<hr/></th>
            <th></th>
            <th width="48%" align="left">{Localisation::getTranslation('common_project_description')}<hr/></th>
        </thead>
        <tbody>
            <tr style="overflow-wrap: break-word;" valign="top">
                <td>
                    <i>
                        {if $task->getComment() != ''}
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getComment())}
                        {else}
                            {Localisation::getTranslation('common_no_comment_has_been_listed')}
                        {/if}
                    </i>
                </td>
                <td></td>
                <td>
                    <i>
                        {if $project->getDescription() != ''}
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getDescription())}
                        {else}
                            {Localisation::getTranslation('common_no_description_has_been_listed')}
                        {/if}
                    </i>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 40px"/>
            </tr>
            <tr>
                <td>
                    <strong>{Localisation::getTranslation('task_details_project_impact')}</strong><hr/>
                </td>
                <td></td>
                <td>
                    <strong>{Localisation::getTranslation('task_details_project_tags')}</strong><hr/>
                </td>
            </tr>
            <tr valign="top">                
                <td>
                    <i>
                    {if $project->getImpact() != ''}
                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getImpact())}
                    {else}
                        {Localisation::getTranslation('No impact has been listed')}
                    {/if}  
                    </i> 
                </td>    
                <td></td>
                <td>
                    {foreach from=$project->getTag() item=tag}
                        <a class="tag label" href="{urlFor name="tag-details" options="id.{$tag->getId()}"}">{TemplateHelper::uiCleanseHTML($tag->getLabel())}</a>
                    {/foreach}
                </td>                    
            </tr>
            {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && isset($discourse_slug)}
            <tr>
                <td colspan="3" style="padding-bottom: 40px"/>
            </tr>
            <tr>
                <td>
                    <strong>{Localisation::getTranslation('common_discuss_on_community')}:</strong><hr/>
                </td>
                <td></td>
                <td>
                    {if $isSiteAdmin && $matecat_url != ''}<strong>Kató TM URL for Task:</strong><hr/>{/if}
                </td>
            </tr>
            <tr valign="top">
                <td>
                    <a href="https://community.translatorswb.org/t/{$discourse_slug}" target="_blank">https://community.translatorswb.org/t/{$discourse_slug}</a>
                </td>
                <td></td>
                <td>
                    {if $isSiteAdmin && $matecat_url != ''}<a href="{$matecat_url}" target="_blank">{$matecat_url}</a>{/if}
                </td>
            </tr>
            {/if}
        </tbody>
    </table>
</div>

{if isset($isOrgMember)}
    <table width="100%" class="table table-striped">
        <thead>
            <th>{Localisation::getTranslation('common_publish_task')}</th>
            <th>{Localisation::getTranslation('common_tracking')}</th>
        </thead>
        <tr align="center">
            <td>
                {assign var="task_id" value=$task->getId()}
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $task->getPublished() == 1}
                        <input type="hidden" name="published" value="0" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-remove-circle icon-white"></i> {Localisation::getTranslation('common_unpublish')}
                        </a>
                    {else}
                        <input type="hidden" name="published" value="1" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-check icon-black"></i> {Localisation::getTranslation('common_publish')}
                        </a>
                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $taskMetaData[$task_id]['tracking']}
                        <input type="hidden" name="track" value="Ignore" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-inbox icon-white"></i> {Localisation::getTranslation('common_untrack_task')}
                        </a>
                    {else}
                        <input type="hidden" name="track" value="Track" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-envelope icon-black"></i> {Localisation::getTranslation('common_track_task')}
                        </a>
                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
        </tr>
    </table>
{/if}
