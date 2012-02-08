{* Must have an object $task assigned by parent *}
<div class="task">
	Task ID: {$task->getTaskId()}

	<ul class="tags">
		<li>
			{Languages::languageNameFromId($task->getSourceId())} 
			to 
			{Languages::languageNameFromId($task->getTargetId())}
		</li>
		
		{foreach from=$task->getTags() item=tag}
			// Include a tag template here instead of getting it from a class?

			<li>{$tags->tagHTML($tag->getTagId())}</li>
		{/foreach}
	</ul>
	<h3><a href="{$task->url()}">{$task->title()}</a></h3>
	<p class="task_details">
		<span class="time_since">{$io->timeSince($task->createdTime())} ago</span> {$task->organisation()}
	</p>
	<p class="task_summary">
		{if $task->getWordcount()}
			{$task->getWordcount()|number_format} words
		{/if}
	
		{if $task->files()}
			{foreach from=$task->files() item=task_file}
				&middot; {$task_file->filename()}
			{/foreach}
		{/if}
	</p>
</div>
