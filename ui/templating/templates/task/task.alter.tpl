{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}
{assign var="task_status_id" value=$task->getTaskStatus()}
    <h1 class="page-header">
        {Localisation::getTranslation(Strings::COMMON_TASK)} {$task->getTitle()}
        <small>{Localisation::getTranslation(Strings::TASK_ALTER_ALTER_TASK_DETAILS_HERE)}</small>
        <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
            <i class="icon-list icon-white"></i> {Localisation::getTranslation(Strings::TASK_ALTER_VIEW_TASK_DETAILS)}
        </a>
    </h1>

    {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}                 
        <div class="alert alert-info">
            <h3>
                <p>{Localisation::getTranslation(Strings::COMMON_NOTE)}:</p>
            </h3>            
            {if $task_status_id == TaskStatusEnum::IN_PROGRESS}
                <p>{Localisation::getTranslation(Strings::TASK_ALTER_0)} {Localisation::getTranslation(Strings::TASK_ALTER_1)}</p>
            {else if $task_status_id == TaskStatusEnum::COMPLETE}
                <p>{Localisation::getTranslation(Strings::TASK_ALTER_2)} {Localisation::getTranslation(Strings::TASK_ALTER_YOU_CAN_ONLY_EDIT)} <strong>{Localisation::getTranslation(Strings::COMMON_TASK_COMMENT)}</strong> {Localisation::getTranslation(Strings::TASK_ALTER_AND)} <strong>{Localisation::getTranslation(Strings::COMMON_DEADLINE)}</strong>.</p>
            {/if}
        </div>
    {/if}
            
    <form method="post" action="{urlFor name="task-alter" options="task_id.$task_id"}" class="well" accept-charset="utf-8">
        <table width="100%">
            <tr align="center">
                <td width="50%">
                    <div style="margin-bottom:20px;">
                        <label for="title" style="font-size: large"><strong>{Localisation::getTranslation(Strings::COMMON_TITLE)}:</strong></label>
                        <textarea wrap="soft" cols="1" rows="4" name="title" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">{$task->getTitle()}</textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label for="impact" style="font-size: large"><strong>{Localisation::getTranslation(Strings::COMMON_TASK_COMMENT)}:</strong></label>
                        <textarea wrap="soft" cols="1" rows="6" name="impact" style="width: 400px">{$task->getComment()}</textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label for="deadline" style="font-size: large"><strong>{Localisation::getTranslation(Strings::COMMON_DEADLINE)}:</strong></label>
                        {if $deadline_error != ''}
                            <div class="alert alert-error">
                                {$deadline_error}
                            </div>
                        {/if}
                        <p>
                            {assign var="deadlineDateTime" value=$task->getDeadline()}
                            <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($deadlineDateTime)}{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}{/if}" style="width: 400px" />
                        </p>
                    </div>
                </td>
                <td>
                    <div style="margin-bottom:60px;">
                        <label for="publishtask" style="font-size: large"><strong>{Localisation::getTranslation(Strings::COMMON_PUBLISH_TASK)}:</strong></label>
                        <p class="desc">{Localisation::getTranslation(Strings::COMMON_IF_CHECKED_TASKS_WILL_APPEAR_IN_THE_TASK_STREAM)}.</p>
                        <input type="checkbox" name="publishTask" value="1" checked="true" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if}/>
                    </div>
                    <p>
                        <label for="target" style="font-size: large"><strong>{Localisation::getTranslation(Strings::COMMON_TARGET_LANGUAGE)}:</strong></label>
                        <select name="target" id="target" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">
                            {foreach $languages as $language}
                                {if $task->getTargetLocale()->getLanguageCode() == $language->getCode()}
                                        <option value="{$language->getCode()}" selected="selected" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} >{$language->getName()}</option>
                                {else}
                                    <option value="{$language->getCode()}" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} >{$language->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </p>
                    <p>
                    {if isset($countries)}
                        <select name="targetCountry" id="targetCountry" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">
                            {foreach $countries as $country}
                                {if $task->getTargetLocale()->getCountryCode() == $country->getCode()}
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

                    <label for="word_count" style="font-size: large"><strong>{Localisation::getTranslation(Strings::COMMON_WORD_COUNT)}:</strong></label>
                    <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px" />
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
                    <h2>{Localisation::getTranslation(Strings::COMMON_TASK_PREREQUISITES)}:</h2>
                    <p class="desc">{Localisation::getTranslation(Strings::COMMON_ASSIGN_PREREQUISITES_FOR_THIS_TASK_IF_ANY)}.</p>
                    <p>
                        {Localisation::getTranslation(Strings::COMMON_THESE_ARE_TASKS_THAT_MUST_BE_COMPLETED_BEFORE_THE_CURRENT_TASK_BECOMES_AVAILABLE)}.
                    </p>
                    <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all;" width="100%" >
                        <thead>
                            <th>{Localisation::getTranslation(Strings::COMMON_ASSIGN)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_TITLE)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_TARGET_LANGUAGE)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_TYPE)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_STATUS)}</th>
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
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_SEGMENTATION)}</span>
                                    {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION)}</span> 
                                    {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING)}</span> 
                                    {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION)}</span> 
                                    {/if}
                                </td>
                                <td>                                            
                                    {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                        {Localisation::getTranslation(Strings::COMMON_WAITING)}
                                    {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                        {Localisation::getTranslation(Strings::COMMON_UNCLAIMED)}
                                    {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                        <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">{Localisation::getTranslation(Strings::COMMON_IN_PROGRESS)}</a>
                                    {elseif $status_id == TaskStatusEnum::COMPLETE}
                                        <a href="{Settings::get("site.api")}v0/tasks/{$task_id}/file/?">{Localisation::getTranslation(Strings::COMMON_COMPLETE)}</a>
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
                        <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CANCEL)}
                    </a>
                    <p style="margin-bottom:20px;"/>  
                </td>
                <td>
                    <p style="margin-bottom:20px;"/>
                    <p>
                        <button type="submit" value="Submit" name="submit" class="btn btn-primary">
                            <i class="icon-refresh icon-white"></i> {Localisation::getTranslation(Strings::TASK_ALTER_UPDATE_TASK_DETAILS)}
                        </button>
                    </p>    
                    <p style="margin-bottom:20px;"/>
                </td>
            </tr>        
        </table>
    </form>

{include file="footer.tpl"}
