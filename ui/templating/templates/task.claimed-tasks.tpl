{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
    <h2>
        <a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
    </h2>
    {if $type_id == TaskTypeEnum::CHUNKING}
        <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking</span> 
        </p>
    {elseif $type_id == TaskTypeEnum::TRANSLATION}
        </h2>
        <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation</span>
        </p>
    {elseif $type_id == TaskTypeEnum::PROOFREADING}
        <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading</span>
        </p>
    {elseif $type_id == TaskTypeEnum::POSTEDITING}
        <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting</span>
        </p>
    {/if}

    <p>
        From: <strong>{TemplateHelper::getTaskSourceLanguage($task)}</strong>
    </p>   
    <p>
        To: <strong>{TemplateHelper::getTaskTargetLanguage($task)}</strong>
    </p>

        {assign var="taskTags" value=$task['Project']->getTagList()}
        {if !empty($taskTags)}
            <p>
                Tags:
                {foreach from=$taskTags item=tag}
                    {assign var="label" value=$tag->getLabel()}
                    <a href="{urlFor name="tag-details" options="label.$label"}" class="label"><span class="label">{$label}</span></a>
                {/foreach}
            </p>
        {/if}
        <p>
            {if $task->getWordCount()}
                Word Count: <strong>{$task->getWordCount()|number_format}</strong>
            {/if}      
        </p> 
	<p class="task_details">
            Added: <strong>{TemplateHelper::timeSinceSqlTime($task->getCreatedTime())}</strong> ago
	</p>
        <p>
            Due by: <strong>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</strong>
        </p>          
        
        <p>            
            {assign var="project_id" value=$task->getProjectId()}
            {assign var="org_id" value=$task['Project']->getOrganisationId()}
            
            Part of: <a href="{urlFor name="project-view" options="project_id.$project_id"}">{$task['Project']->getTitle()}</a>
            for <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$task['Org']->getName()}</a>        
        </p>  

        <p style="margin-bottom:40px;"/>        
</div>
