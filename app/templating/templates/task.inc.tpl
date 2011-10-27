{* Must have an object $task assigned by parent *}
<div class="task">
	{assign var="tag_ids" value=$task->tagIDs()}
	{assign var="target" value=$task->target()}
	{if $tag_ids || $target}
		<ul class="tags">
			<li>{$task->source()} to {$task->target()}</li>
			{foreach from=$tag_ids item=tag_id}
				<li>{$s->tags->tagHTML($tag_id)}</li>
			{/foreach}
		</ul>
	{/if}
	<h3><a href="{$task->url()}">{$task->title()}</a></h3>
	<p class="task_details">
		<span class="time_since">{$s->io->timeSince($task->createdTime())} ago</span> {$task->organisation()}
	</p>
	<p class="task_summary">
		{assign var="wordcount" value=$task->wordcount()}
		{if $wordcount}
			{$wordcount|number_format} words
		{/if}
	
		{assign var="task_files" value=$task->files()}
		{if $task_files}
			{foreach from=$task_files item=task_file}
				&middot; {$task_file->filename()}
			{/foreach}
		{/if}
	</p>
</div>
