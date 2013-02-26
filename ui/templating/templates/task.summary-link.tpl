{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
            <h2>
                <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$task->getTitle()}</a>
            </h2>
            {if $type_id == TaskTypeEnum::CHUNKING}
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking</span>  

            {elseif $type_id == TaskTypeEnum::TRANSLATION}
                </h2>
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation</span>
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading</span>
            {elseif $type_id == TaskTypeEnum::POSTEDITING}
                <p>Type: 
                <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting</span>
            {/if}                
        </p>

        <p>
            From: <b>{TemplateHelper::getTaskSourceLanguage($task)}</b>
    	</p>   
        
        <p>
            To: <b>{TemplateHelper::getTaskTargetLanguage($task)}</b>
        </p>

        {assign var="taskTags" value=$task->getTagList()}
        {if !empty($taskTags)}
            <p>
                Tags:
                {foreach from=$task->getTagList() item=tag}
                    {assign var="label" value=$tag->getLabel()}
                    <a href="{urlFor name="tag-details" options="label.$label"}" class="label"><span class="label">{$label}</span></a>
                {/foreach}
            </p>
        {/if}
        <p>
            {if $task->getWordCount()}
                Word Count: <b>{$task->getWordCount()|number_format}</b>
            {/if}      
        </p> 
	<p class="task_details">
            Added: <b>{TemplateHelper::timeSinceSqlTime($task->getCreatedTime())}</b> ago
	</p>
        <p>
            Due by: <b>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</b>
        </p>           
        
        <p>            
            {assign var="project_id" value=$task->getProjectId()}
            {assign var="org_id" value=$task['Project']->getOrganisationId()}
            
            Part of: <a href="{urlFor name="project-view" options="project_id.$project_id"}">{$task['Project']->getTitle()}</a>
            for <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$task['Org']->getName()}</a>        
        </p>  

        <p style="margin-bottom:40px;"></p>        
</div>
