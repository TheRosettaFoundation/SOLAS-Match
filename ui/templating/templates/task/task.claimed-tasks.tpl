{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
    <h2>
        <a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
    </h2>
    {if $type_id == TaskTypeEnum::SEGMENTATION}
        <p>{Localisation::getTranslation(Strings::COMMON_TYPE)} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_SEGMENTATION)}</span> 
        </p>
    {elseif $type_id == TaskTypeEnum::TRANSLATION}
        <p>{Localisation::getTranslation(Strings::COMMON_TYPE)} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION)}</span>
        </p>
    {elseif $type_id == TaskTypeEnum::PROOFREADING}
        <p>{Localisation::getTranslation(Strings::COMMON_TYPE)} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING)}</span>
        </p>
    {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
        <p>{Localisation::getTranslation(Strings::COMMON_TYPE)} 
            <span class="label label-info" style="background-color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION)}</span>
        </p>
    {/if}

    <p>
        {Localisation::getTranslation(Strings::COMMON_FROM)} <strong>{TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}</strong>
    </p>   
    <p>
        {Localisation::getTranslation(Strings::COMMON_TO)} <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>
    </p>

        {assign var="taskTags" value=$task['Project']->getTagList()}
        {if !empty($taskTags)}
            <p>
                {Localisation::getTranslation(Strings::COMMON_TAGS)}
                {foreach from=$taskTags item=tag}
                    {assign var="label" value=$tag->getLabel()}
                    <a href="{urlFor name="tag-details" options="label.$label"}" class="label"><span class="label">{$label}</span></a>
                {/foreach}
            </p>
        {/if}
        <p>
            {if $task->getWordCount()}
                {Localisation::getTranslation(Strings::COMMON_WORD_COUNT)} <strong>{$task->getWordCount()|number_format}</strong>
            {/if}      
        </p> 
	<p class="task_details">
            {sprintf(Localisation::getTranslation(Strings::COMMON_ADDED), {TemplateHelper::timeSinceSqlTime($task->getCreatedTime())})}
        <p>
            {sprintf(Localisation::getTranslation(Strings::COMMON_DUE_BY), {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))})}
        </p>          
        
        <p>            
            {assign var="project_id" value=$task->getProjectId()}
            {assign var="org_id" value=$task['Project']->getOrganisationId()}
            
            {sprintf(Localisation::getTranslation(Strings::COMMON_PART_OF_FOR), {urlFor name="project-view" options="project_id.$project_id"}, {$task['Project']->getTitle()}, {urlFor name="org-public-profile" options="org_id.$org_id"}, {$task['Org']->getName()})}  
        </p>  

        <p style="margin-bottom:40px;"/>        
</div>
