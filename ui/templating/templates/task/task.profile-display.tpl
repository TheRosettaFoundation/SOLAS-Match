{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
        <h2>
            {$task->getTitle()}
        </h2>
        {if $type_id == TaskTypeEnum::SEGMENTATION}
            <p>{Localisation::getTranslation(Strings::COMMON_TYPE)}: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_SEGMENTATION)}</span> 
        {elseif $type_id == TaskTypeEnum::TRANSLATION}
            <p>{Localisation::getTranslation(Strings::COMMON_TYPE)}: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION)}</span>
        {elseif $type_id == TaskTypeEnum::PROOFREADING}
            <p>{Localisation::getTranslation(Strings::COMMON_TYPE)}: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING)}</span>
        {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
            <p>{Localisation::getTranslation(Strings::COMMON_TYPE)}: 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION)}</span>
        {/if}                
    </p>

    <p>
        {Localisation::getTranslation(Strings::COMMON_FROM)}: <strong>{TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}</strong>
    </p>   

    <p>
        {Localisation::getTranslation(Strings::COMMON_TO)}: <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>
    </p>
    
    {if $task->getWordCount()}
        <p>{Localisation::getTranslation(Strings::COMMON_WORD_COUNT)}: <strong>{$task->getWordCount()|number_format}</strong></p>
    {/if}      
	<p class="task_details">
        {Localisation::getTranslation(Strings::COMMON_ADDED)}: <strong>{TemplateHelper::timeSinceSqlTime($task->getCreatedTime())}</strong> {Localisation::getTranslation(Strings::COMMON_AGO)}
	</p>
        
    <p>
        {Localisation::getTranslation(Strings::TASK_PROFILE_DISPLAY_ARCHIVED)}: <strong>{TemplateHelper::timeSinceSqlTime($task->getArchivedDate())}</strong> {Localisation::getTranslation(Strings::COMMON_AGO)}
    </p>
    <p>
        {Localisation::getTranslation(Strings::COMMON_DUE_BY)}: <strong>{date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))}</strong>
    </p>

    <p style="margin-bottom:40px;"/>        
</div>
