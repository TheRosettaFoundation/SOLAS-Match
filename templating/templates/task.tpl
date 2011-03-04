{include file="header.inc.tpl"}
<div class="grid_8">
	<h2><a href="{$task->url()}">{$task->title()}</a></h2>
	
	<p>
		Posted {$s->io->timeSince($task->createdTime())} ago by {$task->organisation()}.
	</p>
	
	{assign var="tag_ids" value=$task->tagIDs()}
	{if $tag_ids}
		<ul class="tags">
			{foreach from=$tag_ids item=tag_id}
				<li>{$s->tags->tagHTML($tag_id)}</a>
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
