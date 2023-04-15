{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
        <h2>
            {TemplateHelper::uiCleanseHTML($task->getTitle())}
        </h2>
    <p>{Localisation::getTranslation('common_type')}
        {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
            {if $type_id == $task_type}
                <span class="label label-info" style="background-color: {$ui['colour']}">{$ui['type_text']}</span>
            {/if}
        {/foreach}
    </p>

    <p>
        {Localisation::getTranslation('common_from')} <strong>{TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}</strong>
    </p>   

    {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
    <p>
        {Localisation::getTranslation('common_to')} <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>
    </p>
    {/if}
    
    {if $task->getWordCount()}
        <p>{TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text']} <strong>{$task->getWordCount()|number_format}</strong></p>
    {/if}      
	<p class="task_details">
        {sprintf(Localisation::getTranslation('common_added'), {TemplateHelper::timeSinceSqlTime($task->getCreatedTime())})}
	</p>
        
    <p>
        {sprintf(Localisation::getTranslation('task_profile_display_archived'), {TemplateHelper::timeSinceSqlTime($task->getArchivedDate())})}
    </p>
    <p>
        {sprintf(Localisation::getTranslation('common_due_by'), {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))})}
    </p>

    <p style="margin-bottom:40px;"/>        
</div>
