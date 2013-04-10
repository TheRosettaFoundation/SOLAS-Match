{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}
{assign var="task_status_id" value=$task->getTaskStatus()}
    <h1 class="page-header">
        Task {$task->getTitle()}
        <small>Alter task details here.</small>
        <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
            <i class="icon-list icon-white"></i> View Task Details
        </a>
    </h1>

    {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}                 
        <div class="alert alert-info">
            <h3>
                <p>Note:</p>
            </h3>            
            {if $task_status_id == TaskStatusEnum::IN_PROGRESS}
                <p>This task has been completed. You can only edit certain task details.</p>
            {else if $task_status_id == TaskStatusEnum::COMPLETE}
                <p>This task has been claimed and is in progress. You can only edit <strong>Task Comment</strong> and <strong>Deadline</strong>.</p>
            {/if}
        </div>
    {/if}
            
    <form method="post" action="{urlFor name="task-alter" options="task_id.$task_id"}" class="well">
        <table width="100%">
            <tr align="center">
                <td width="50%">
                    <div style="margin-bottom:20px;">
                        <label for="title" style="font-size: large"><strong>Title:</strong></label>
                        <textarea wrap="soft" cols="1" rows="4" name="title" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} >{$task->getTitle()}</textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label for="impact" style="font-size: large"><strong>Task Comment:</strong></label>
                        <textarea wrap="soft" cols="1" rows="6" name="impact">{$task->getComment()}</textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label for="deadline" style="font-size: large"><strong>Deadline:</strong></label>
                        {if $deadline_error != ''}
                            <div class="alert alert-error">
                                {$deadline_error}
                            </div>
                        {/if}
                        <p>
                            {assign var="deadlineDateTime" value=$task->getDeadline()}
                            <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($deadlineDateTime)}{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}{/if}" />
                        </p>
                    </div>
                </td>
                <td>
                    <div style="margin-bottom:60px;">
                        <label for="publishtask" style="font-size: large"><strong>Publish Task:</strong></label>
                        <p class="desc">If checked, this task will appear in the task stream.</p>
                        <input type="checkbox" name="publishTask" value="1" checked="true" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if}/>
                    </div>
                    <p>
                        <label for="target" style="font-size: large"><strong>Target Language:</strong></label>
                        <select name="target" id="target" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if}>
                            {foreach $languages as $language}
                                {if $task->getTargetLanguageCode() == $language->getCode()}
                                        <option value="{$language->getCode()}" selected="selected" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} >{$language->getName()}</option>
                                {else}
                                    <option value="{$language->getCode()}" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} >{$language->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </p>
                    <p>
                    {if isset($countries)}
                        <select name="targetCountry" id="targetCountry" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if}>
                            {foreach $countries as $country}
                                {if $task->getTargetCountryCode() == $country->getCode()}
                                    <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                                {else}
                                    <option value="{$country->getCode()}">{$country->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                    {/if}
                    </p>
                    <p style="margin-bottom:60px;"/>

                    {if !is_null($word_count_err)}
                        <div class="alert alert-error">
                            {$word_count_err}
                        </div>
                    {/if} 
                    <p style="margin-bottom:40px;"/>

                    <label for="word_count" style="font-size: large"><strong>Word Count:</strong></label>
                    <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} />
                </td>             
            </tr>
            <tr>
                <td colspan="2">
                    <hr/>
                </td> 
            </tr>
            {if $deadlockError != ''}
            <tr>
                <td colspan="2">
                    <div class="alert alert-error">
                        {$deadlockError}
                    </div>
                </td>
            </tr>
            {/if}
            <tr>
                <td colspan="2">
                    <h2>Task Prerequisite(s):</h2>
                    <p class="desc">Assign prerequisites for this task - if any.</p>
                    <p>
                        These are tasks that must be completed before the current task becomes available.
                    </p>
                    <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all;" width="100%" >
                        <thead>
                            <th>Assign</th>
                            <th>Title</th>
                            <th>Source Language</th>
                            <th>Target Language</th>
                            <th>Type</th>
                            <th>Status</th>
                        </thead>
                        {assign var="i" value=0}
                        {foreach $projectTasks as $projectTask}                                    
                            {assign var="type_id" value=$projectTask->getTaskType()}
                            {assign var="status_id" value=$projectTask->getTaskStatus()}
                            {assign var="task_id" value=$projectTask->getId()}
                            <tr style="overflow-wrap: break-word;">
                                <td>
                                    {if $task_status_id > TaskStatusEnum::PENDING_CLAIM} 
                                        <input type="checkbox" name="preReq_{$i}" value="{$task_id}" disabled                                               
                                        {if in_array($task_id, $thisTaskPreReqIds)}
                                            checked="true" />
                                        {else}
                                            />
                                        {/if} 
                                    {else}
                                        {*
                                        {if $tasksEnabled[$task_id]}
                                            <input type="checkbox" name="preReq_{$i}" value="{$task_id}"
                                        {else}
                                            <input type="checkbox" name="preReq_{$i}" value="{$task_id}" disabled
                                        {/if}
                                        *}
                                        <input type="checkbox" name="preReq_{$i}" value="{$task_id}"
                                        {if in_array($task_id, $thisTaskPreReqIds)}
                                            checked="true" />
                                        {else}
                                            />
                                        {/if} 
                                    {/if}
                                    {assign var="i" value=$i+1}
                                </td>
                                <td>
                                    <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$projectTask->getTitle()}</a>
                                </td>
                                <td>{TemplateHelper::getTaskSourceLanguage($projectTask)}</td>  
                                <td>{TemplateHelper::getTaskTargetLanguage($projectTask)}</td>
                                <td>                                            
                                    {if $type_id == TaskTypeEnum::SEGMENTATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">Segmentation</span>                                    
                                    {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation</span> 
                                    {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading</span> 
                                    {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">Desegmentation</span> 
                                    {/if}
                                </td>
                                <td>                                            
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
                            </tr>
                        {/foreach}
                        <input type="hidden" name="totalTaskPreReqs" value="{$i}" />
                    </table>                            
                </td>
            </tr>
            <tr align="center">
                <td>
                    <p style="margin-bottom:20px;"/>  
                    <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='btn btn-danger'>
                        <i class="icon-ban-circle icon-white"></i> Cancel
                    </a>
                    <p style="margin-bottom:20px;"/>  
                </td>
                <td>
                    <p style="margin-bottom:20px;"/>
                    <p>
                        <button type="submit" value="Submit" name="submit" class="btn btn-primary">
                            <i class="icon-refresh icon-white"></i> Update Task Details
                        </button>
                    </p>    
                    <p style="margin-bottom:20px;"/>
                </td>
            </tr>        
        </table>
    </form>

{include file="footer.tpl"}
