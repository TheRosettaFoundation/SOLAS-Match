{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
        <h2>
            {$task->getTitle()}
        </h2>
        {if $type_id == TaskTypeEnum::SEGMENTATION}
            <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">Segmentation</span> 
        {elseif $type_id == TaskTypeEnum::TRANSLATION}
            <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation</span>
        {elseif $type_id == TaskTypeEnum::PROOFREADING}
            <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading</span>
        {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
            <p>Type: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">Desegmentation</span>
        {/if}                
    </p>

    <p>
        From: <strong>{TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}</strong>
    </p>   

    <p>
        To: <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>
    </p>
    
    {if $task->getWordCount()}
        <p>Word Count: <strong>{$task->getWordCount()|number_format}</strong></p>
    {/if}      
	<p class="task_details">
        Added: <strong>{TemplateHelper::timeSinceSqlTime($task->getCreatedTime())}</strong> ago
	</p>
        
    <p>
        Archived: <strong>{TemplateHelper::timeSinceSqlTime($task->getArchivedDate())}</strong> ago
    </p>
    <p>
        Due by: <strong>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</strong>
    </p>

    <p style="margin-bottom:40px;"/>        
</div>
