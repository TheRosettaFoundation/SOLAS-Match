{* Must have an object $task assigned by parent *}
<div class="task">
	<ul class="tags">
		<li>
			{if $task->areSourceAndTargetSet()}
				{Languages::languageNameFromId($task->getSourceId())} 
				to 
				{Languages::languageNameFromId($task->getTargetId())}
			{/if}
		</li>
		
		{foreach from=TaskTags::getTags($task) item=tag}
			<li>{include file="inc.tag.tpl" tag_name=$tag}</li>
		{/foreach}
	</ul>
	<h3><a href="/task/{$task->getTaskId()}/">{$task->getTitle()}</a></h3>
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