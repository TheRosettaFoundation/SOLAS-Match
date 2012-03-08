{* Must have an object $task assigned by parent *}
<div class="task">
	<h2><a href="/task/id/{$task->getTaskId()}/">{$task->getTitle()}</a></h2>
	<p>
		{if $task->areSourceAndTargetSet()}
			{Languages::languageNameFromId($task->getSourceId())} 
			to 
			{Languages::languageNameFromId($task->getTargetId())}
		{/if}
			
		{foreach from=$task->getTags() item=tag}
			<span class="label">{$tag}</span>
		{/foreach}
	</p>
	
	<p class="task_details">
		Added {IO::timeSinceSqlTime($task->getCreatedTime())} ago
		&middot; By {Organisations::nameFromId($task->getOrganisationId())}
		{if $task->getWordcount()}
			&middot; {$task->getWordcount()|number_format} words
		{/if}
	</p>
</div>