{* Must have an object $project assigned by parent *}
<div class="project">
    {assign var='project_id' value=$project->getId()}
	<h2>{$project->getTitle()}</h2>
	<p>
		{if $project->getSourceLanguageCode()}
			From <b>{TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}</b>
		{/if}
		{if $project->getTargetLanguageCode()}
			To <b>{TemplateHelper::languageNameFromCode($project->getTargetLanguageCode())}</b>
		{/if}                

		{foreach from=$project->getTags() item=tag}
			<span class="label">{$tag}</span>                        
		{/foreach}
	</p>
	
	<p class="task_details">
		Added {TemplateHelper::timeSinceSqlTime($project->getCreatedTime())} ago
		&middot; By Project
		{if $task->getWordCount()}
			&middot; {$task->getWordCount()|number_format} words
		{/if}
    <p style="margin-bottom:30px;"></p>
	</p>
</div>
