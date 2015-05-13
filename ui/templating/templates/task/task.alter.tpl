{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}
{assign var="task_status_id" value=$task->getTaskStatus()}
    <h1 class="page-header">
        {Localisation::getTranslation('common_task')} {$task->getTitle()|escape:'html':'UTF-8'}
        <small>{Localisation::getTranslation('task_alter_alter_task_details_here')}</small>
        <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
            <i class="icon-list icon-white"></i> {Localisation::getTranslation('task_alter_view_task_details')}
        </a>
    </h1>

    {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}                 
        <div class="alert alert-info">
            <h3>
                <p>{Localisation::getTranslation('common_note')}</p>
            </h3>            
            {if $task_status_id == TaskStatusEnum::IN_PROGRESS}
                <p>{Localisation::getTranslation('task_alter_0')} {Localisation::getTranslation('task_alter_1')}</p>
            {else if $task_status_id == TaskStatusEnum::COMPLETE}
                <p>{Localisation::getTranslation('task_alter_2')} {Localisation::getTranslation('task_alter_you_can_only_edit')}</p>
            {/if}
        </div>
    {/if}
            
    <form method="post" action="{urlFor name="task-alter" options="task_id.$task_id"}" class="well" accept-charset="utf-8">
        <table width="100%">
            <tr align="center">
                <td width="50%">
                    <div style="margin-bottom:20px;">
                        <label for="title" style="font-size: large"><strong>{Localisation::getTranslation('common_title')}</strong></label>
                        <textarea wrap="soft" cols="1" rows="4" name="title" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">{$task->getTitle()|escape:'html':'UTF-8'}</textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label for="impact" style="font-size: large"><strong>{Localisation::getTranslation('common_task_comment')}</strong></label>
                        <textarea wrap="soft" cols="1" rows="6" name="impact" style="width: 400px">{$task->getComment()|escape:'html':'UTF-8'}</textarea>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label for="deadline" style="font-size: large"><strong>{Localisation::getTranslation('common_deadline')}</strong></label>
                        {if $deadline_error != ''}
                            <div class="alert alert-error">
                                {$deadline_error}
                            </div>
                        {/if}
                        <p>
                            {assign var="deadlineDateTime" value=$task->getDeadline()}
                            <input class="hasDatePicker" type="text" id="deadline_field" name="deadline_field" value="{if isset($deadlineDateTime)}{$task->getDeadline()}{/if}" style="width: 400px" />
                            <input type="hidden" name="deadline" id="deadline" />
                        </p>
                    </div>
                </td>
                <td>
                    <div style="margin-bottom:60px;">
                        <label for="publishtask" style="font-size: large"><strong>{Localisation::getTranslation('common_publish_task')}</strong></label>
                        <p class="desc">{Localisation::getTranslation('common_if_checked_tasks_will_appear_in_the_tasks_stream')}</p>
                        <input type="checkbox" name="publishTask" value="{$task->getPublished()}" {$publishStatus} {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if}/>
                    </div>
                    <p>
                        <label for="target" style="font-size: large"><strong>{Localisation::getTranslation('common_target_language')}</strong></label>
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

                    <label for="word_count" style="font-size: large"><strong>{Localisation::getTranslation('common_word_count')}</strong></label>
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
                    <h2>{Localisation::getTranslation('common_task_prerequisites')}</h2>
                    <p class="desc">{Localisation::getTranslation('common_assign_prerequisites_for_this_task_if_any')}</p>
                    <p>
                        {Localisation::getTranslation('common_these_are_tasks_that_must_be_completed_before_the_current_task_becomes_available')}
                    </p>
                    <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all;" width="100%" >
                        <thead>
                            <th>{Localisation::getTranslation('common_assign')}</th>
                            <th>{Localisation::getTranslation('common_title')}</th>
                            <th>{Localisation::getTranslation('common_source_language')}</th>
                            <th>{Localisation::getTranslation('common_target_language')}</th>
                            <th>{Localisation::getTranslation('common_type')}</th>
                            <th>{Localisation::getTranslation('common_status')}</th>
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
                                    <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$projectTask->getTitle()|escape:'html':'UTF-8'}</a>
                                </td>
                                <td>{TemplateHelper::getTaskSourceLanguage($projectTask)}</td>  
                                <td>{TemplateHelper::getTaskTargetLanguage($projectTask)}</td>
                                <td>                                            
                                    {if $type_id == TaskTypeEnum::SEGMENTATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation('common_segmentation')}</span>
                                    {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation('common_translation')}</span> 
                                    {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation('common_proofreading')}</span> 
                                    {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                                        <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation('common_desegmentation')}</span> 
                                    {/if}
                                </td>
                                <td>                                            
                                    {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                        {Localisation::getTranslation('common_waiting')}
                                    {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                        {Localisation::getTranslation('common_unclaimed')}
                                    {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                        <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">{Localisation::getTranslation('common_in_progress')}</a>
                                    {elseif $status_id == TaskStatusEnum::COMPLETE}
                                        <a href="{urlFor name="home"}task/{$task_id}/download-task-latest-file/">{Localisation::getTranslation('common_complete')}</a>
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
                        <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation('common_cancel')}
                    </a>
                    <p style="margin-bottom:20px;"/>  
                </td>
                <td>
                    <p style="margin-bottom:20px;"/>
                    <p>
                        <button type="submit" onclick="return validateForm();" value="Submit" name="submit" class="btn btn-primary">
                            <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('task_alter_update_task_details')}
                        </button>
                    </p>    
                    <p style="margin-bottom:20px;"/>
                </td>
            </tr>        
        </table>
    </form>
                        
{include file="footer.tpl"}
