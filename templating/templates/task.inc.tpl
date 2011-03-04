{* Must have an object $task assigned by parent *}
<div class="task">
	<h3><a href="{$task->url()}">{$task->title()}</a></h3>
	<p class="details">
		<span class="time_since">{$s->io->timeSince($task->createdTime())} ago</span> {$task->organisation()}
	</p>
	{assign var="tag_ids" value=$task->tagIDs()}
	{if $tag_ids}
		<ul class="tags">
			{foreach from=$tag_ids item=tag_id}
				<li>{$s->tags->tagHTML($tag_id)}</a>
			{/foreach}
		</ul>
	{/if}
</div>
