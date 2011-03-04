{include file="header.inc.tpl"}
<div class="grid_8">
	<h2>All open items tagged with <em>{$s->tags->label($tag_id)}</em></h2>

	{if $tasks}
		{foreach from=$tasks item=task}
			{include file="task.inc.tpl" task=$task}
		{/foreach}
	{/if}
</div>
{include file="footer.inc.tpl"}
