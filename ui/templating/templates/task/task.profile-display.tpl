{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
        <h2>
            {$task->getTitle()}
        </h2>
        {if $type_id == TaskTypeEnum::SEGMENTATION}
            <p>{Localisation::getTranslation('common_type')} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation('common_segmentation')}</span> 
        {elseif $type_id == TaskTypeEnum::TRANSLATION}
            <p>{Localisation::getTranslation('common_type')} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation('common_translation')}</span>
        {elseif $type_id == TaskTypeEnum::PROOFREADING}
            <p>{Localisation::getTranslation('common_type')} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation('common_proofreading')}</span>
        {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
            <p>{Localisation::getTranslation('common_type')} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation('common_desegmentation')}</span>
        {/if}                
    </p>

    <p>
        {Localisation::getTranslation('common_from')} <strong>{TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}</strong>
    </p>   

    <p>
        {Localisation::getTranslation('common_to')} <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>
    </p>
    
    {if $task->getWordCount()}
        <p>{Localisation::getTranslation('common_word_count')} <strong>{$task->getWordCount()|number_format}</strong></p>
    {/if}      
	<p class="task_details">
        {sprintf(Localisation::getTranslation('common_added'), {TemplateHelper::timeSinceSqlTime($task->getCreatedTime().' UTC')})}
	</p>
        
    <p>
        {sprintf(Localisation::getTranslation('task_profile_display_archived'), {TemplateHelper::timeSinceSqlTime($task->getArchivedDate())})}
    </p>
    <p>
        {sprintf(Localisation::getTranslation('common_due_by'), {date(Settings::get("ui.date_format"), strtotime($task->getDeadline().' UTC'))})}
    </p>

    <p style="margin-bottom:40px;"/>        
</div>
