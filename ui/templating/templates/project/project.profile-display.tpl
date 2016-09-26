{* Must have an object $project assigned by parent *}
<div class="project">
    {assign var='project_id' value=$project->getId()}
    <h2>{$project->getTitle()}</h2>
    <p>
        {if $project->getSourceLanguageCode()}
            {Localisation::getTranslation('common_from')} <strong>{TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}</strong>
        {/if}
        {if $project->getTargetLanguageCode()}
            {Localisation::getTranslation('common_to')} <strong>{TemplateHelper::languageNameFromCode($project->getTargetLanguageCode())}</strong>
        {/if}                

        {foreach from=$project->getTags() item=tag}
            <span class="label">{TemplateHelper::uiCleanseHTML($tag)}</span>
        {/foreach}
    </p>

    <p class="task_details">
        {sprintf(Localisation::getTranslation('common_added'), {TemplateHelper::timeSinceSqlTime($project->getCreatedTime())})}
        &middot; {Localisation::getTranslation('project_profile_display_by_project')}
        {if $task->getWordCount()}
                &middot; {$task->getWordCount()|number_format} {Localisation::getTranslation('project_profile_display_words')}
        {/if}
        <p style="margin-bottom:30px;"/>
    </p>
</div>
