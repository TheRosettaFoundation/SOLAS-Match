{* Must have an object $task assigned by parent *}
<div class="task">
	<h3><a href="/task/id/{$task->getTaskId()}/">{$task->getTitle()}</a></h3>
	<ul class="tags">
		{if $task->areSourceAndTargetSet()}
			<li>
				{Languages::languageNameFromId($task->getSourceId())} 
				to 
				{Languages::languageNameFromId($task->getTargetId())}
			</li>
		{/if}
		
		{foreach from=$task->getTags() item=tag}
			<li>{include file="inc.tag.tpl" tag_name=$tag}</li>
		{/foreach}
	</ul>
	<p class="task_details">
		<span class="time_since">{IO::timeSinceSqlTime($task->getCreatedTime())} ago</span> {Organisations::nameFromId($task->getOrganisationId())}
	</p>
	<p class="task_summary">
		{if $task->getWordcount()}
			{$task->getWordcount()|number_format} words
		{/if}
	
		{if $files = TaskFiles::getTaskFiles($task)}
			{foreach from=$files item=task_file}
				&middot; {$task_file->filename()}
			{/foreach}
		{/if}
	</p>
</div>