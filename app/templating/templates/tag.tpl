{include file="header.inc.tpl"}
<div class="grid_8">
	<h2>All open items tagged with <em>{$tag->getLabel()}</em></h2>

	{if $tasks}
		{foreach from=$tasks item=task}
			{include file="task.inc.tpl" task=$task}
		{/foreach}
	{/if}
</div>
<div id="sidebar" class="grid_4">
	<p><a href="/task/create/">+ New task</a></p>
	<a href="/">All tasks</a>
	{include file="tags.top-list.inc.tpl"}
</div>
{include file="footer.inc.tpl"}
