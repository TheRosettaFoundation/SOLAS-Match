<table class="table table-striped">
    <thead>
        <th style="text-align: left"><b>Project</b></th>

        <th>Source Language</th>
        <th>Target Language</th>
        <th>Created</th>
        <th>Task Deadline</th>
        <th>Word Count</th>
        {if isset($isOrgMember)}<th>Status</th>{/if}
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
                {TemplateHelper::getTaskSourceLanguage($task)}
            </td>
            <td>
                {TemplateHelper::getTaskTargetLanguage($task)}
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
                        Waiting
                    {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                        Unclaimed
                    {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                        <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">In Progress</a>
                    {elseif $status_id == TaskStatusEnum::COMPLETE}
                        <a href="{Settings::get("site.api")}v0/tasks/{$task_id}/file/?">Complete</a>
                    {/if}
                </td>
            {/if}
        </tr>
    </tbody>
</table>

<div class="well">
    <table width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
        <thead>
            <th width="48%" align="left">Task Comment:<hr/></th>
            <th></th>
            <th width="48%" align="left">Project Description:<hr/></th>
        </thead>
        <tbody>
            <tr style="overflow-wrap: break-word;" valign="top">
                <td>
                    <i>
                        {if $task->getComment() != ''}
                            {$task->getComment()}
                        {else}
                            No comment has been added.
                        {/if}
                    </i>
                </td>
                <td></td>
                <td>
                    <i>
                        {if $project->getDescription() != ''}
                            {$project->getDescription()}
                        {else}
                            No description has been added.
                        {/if}
                    </i>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 40px"></td>
            </tr>
            <tr>
                <td>
                    <b>Project Impact:</b><hr/>
                </td>
                <td></td>
                <td>
                    <b>Task Tags:</b><hr/>
                </td>
            </tr>
            <tr valign="top">                
                <td>
                    <i>
                    {if $project->getImpact() != ''}
                        {$project->getImpact()}
                    {else}
                        No impact has been added.
                    {/if}  
                    </i> 
                </td>    
                <td></td>
                <td>
                </td>                    
            </tr>
        </tbody>
    </table>
</div>

{if isset($isOrgMember)}
    <table width="100%" class="table table-striped">
        <thead>
            <th>Task Published</th>
            <th>Task Tracked</th>
        </thead>
        <tr align="center">
            <td>
                {assign var="task_id" value=$task->getId()}
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $task->getPublished() == 1}
                        <input type="hidden" name="published" value="0" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-check icon-black"></i> Published
                        </a>
                    {else}
                        <input type="hidden" name="published" value="1" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-remove-circle icon-white"></i> Unpublished
                        </a>
                    {/if}
                </form>
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $taskMetaData[$task_id]['tracking']}
                        <input type="hidden" name="track" value="Ignore" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-inbox icon-black"></i> Tracked
                        </a>
                    {else}
                        <input type="hidden" name="track" value="Track" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-envelope icon-white"></i> Untracked
                        </a>
                    {/if}
                </form>
            </td>
        </tr>
    </table>
{/if}

