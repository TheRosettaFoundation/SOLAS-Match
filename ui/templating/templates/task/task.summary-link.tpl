{* Must have an object $task assigned by parent *}
<div class="task" style="word-break: break-all; overflow-wrap: break-word;">
    {assign var='task_id' value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}    
                         
        <h2>
            <a href="{urlFor name="task-view" options="task_id.$task_id"}">{TemplateHelper::uiCleanseHTML($task->getTitle())}</a>
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

    <p>
        {Localisation::getTranslation('common_to')} <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>
    </p>

    {assign var="currentTaskTags" value=$taskTags[$task->getId()]}
    {if !empty($taskTags)}
        <p>
            {Localisation::getTranslation('common_tags')}
            {foreach from=$currentTaskTags item=tag}
                {assign var="tagId" value=$tag->getId()}
                <a href="{urlFor name="tag-details" options="id.$tagId"}" class="label"><span class="label">{TemplateHelper::uiCleanseHTML($tag->getLabel())}</span></a>
            {/foreach}
        </p>
    {/if}
    
    {if $task->getWordCount()}
        <p>{Localisation::getTranslation('common_word_count')} <strong>{$task->getWordCount()|number_format}</strong></p>
    {/if}      
	<p class="task_details">
        {sprintf(Localisation::getTranslation('common_added'), {TemplateHelper::timeSinceSqlTime($task->getCreatedTime())})}
	</p>
    <p>
        {sprintf(Localisation::getTranslation('common_due_by'), {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))})}
    </p>           
        
    <p>            
        {assign var="project_id" value=$task->getProjectId()}
        {assign var="org_id" value=$taskOrgs[$task->getId()]->getId()}
        {sprintf(Localisation::getTranslation('common_part_of_for'), {urlFor name="project-view" options="project_id.$project_id"}, {TemplateHelper::uiCleanseHTML($taskProjTitles[$task->getId()])}, {urlFor name="org-public-profile" options="org_id.$org_id"}, {$taskOrgs[$task->getId()]->getName()})}
    </p>  

    <p style="margin-bottom:40px;"/>        
</div>
