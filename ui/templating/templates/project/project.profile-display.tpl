{* Must have an object $project assigned by parent *}
<div class="project">
    {assign var='project_id' value=$project->getId()}
    <h2>{$project->getTitle()}</h2>
    <p>
        {if $project->getSourceLanguageCode()}
            {Localisation::getTranslation(Strings::COMMON_FROM)} <strong>{TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}</strong>
        {/if}
        {if $project->getTargetLanguageCode()}
            {Localisation::getTranslation(Strings::COMMON_TO)} <strong>{TemplateHelper::languageNameFromCode($project->getTargetLanguageCode())}</strong>
        {/if}                

        {foreach from=$project->getTags() item=tag}
            <span class="label">{$tag}</span>                        
        {/foreach}
    </p>

    <p class="task_details">
        {Localisation::getTranslation(Strings::COMMON_ADDED)} {TemplateHelper::timeSinceSqlTime($project->getCreatedTime())} {Localisation::getTranslation(Strings::COMMON_AGO)}
        &middot; {Localisation::getTranslation(Strings::PROJECT_PROFILE_DISPLAY_BY_PROJECT)}
        {if $task->getWordCount()}
                &middot; {$task->getWordCount()|number_format} {Localisation::getTranslation(Strings::PROJECT_PROFILE_DISPLAY_WORDS)}
        {/if}
        <p style="margin-bottom:30px;"/>
    </p>
</div>
