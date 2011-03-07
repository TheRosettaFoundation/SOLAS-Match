{include file="header.inc.tpl"}
<div class="grid_8 task_content">
	<h2><a href="{$task->url()}">{$task->title()}</a></h2>
	
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
		
	<form>
		<input type="submit" value="Download to translate"> (no functionality)
	</form>
	
	<form>
		<input type="submit" value="Upload translated"> (no functionality)
	</form>
	
</div>
{include file="footer.inc.tpl"}
