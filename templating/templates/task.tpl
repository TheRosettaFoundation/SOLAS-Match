{include file="header.inc.tpl"}
<div class="grid_8 task_content">
	<h2>{$task->title()}</h2>
	
	<p class="details">
		<span class="time_since">{$s->io->timeSince($task->createdTime())} ago</span> {$task->organisation()}
	</p>
	{assign var="tag_ids" value=$task->tagIDs()}
	{if $tag_ids}
		<ul class="tags">
			{foreach from=$tag_ids item=tag_id}
				<li>{$s->tags->tagHTML($tag_id)}</li>
			{/foreach}
		</ul>
	{/if}
	
	{if isset($task_files)}
		{foreach from=$task_files item=task_file}
			<h3>{$task_file->filename()}</h3>
			<p><a href="{$task_file->url()}">Download the file to tranlate it.</a></p>
		{/foreach}
	{/if}	
</div>
{include file="footer.inc.tpl"}
