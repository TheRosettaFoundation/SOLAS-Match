{* Must have an object $task assigned by parent *}
<div class="task">
    {assign var='task_id' value=$task->getId()}
	<h2>{$task->getTitle()}</h2>
	<p>
		{if $task->getSourceLangId()}
			From <b>{TemplateHelper::languageNameFromId($task->getSourceLangId())}</b>
		{/if}
		{if $task->getTargetLangId()}
			To <b>{TemplateHelper::languageNameFromId($task->getTargetLangId())}</b>
		{/if}                

		{foreach from=$task->getTags() item=tag}
			<span class="label">{$tag}</span>                        
		{/foreach}
	</p>
	
	<p class="task_details">
		Added {TemplateHelper::timeSinceSqlTime($task->getCreatedTime())} ago
		&middot; By {TemplateHelper::orgNameFromId($task->getOrgId())}
		{if $task->getWordcount()}
			&middot; {$task->getWordcount()|number_format} words
		{/if}
                <p style="margin-bottom:30px;"></p>
	</p>
</div>
