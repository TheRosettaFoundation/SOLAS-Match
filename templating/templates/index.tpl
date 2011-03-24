{include file="header.inc.tpl"}
	<div class="grid_8">
		{if isset($tasks)}
			<h2 class="section_top">Translation Tasks</h2>
			{if isset($tasks)}
				{foreach from=$tasks item=task name=tasks_loop}
					{include file="task.inc.tpl" task=$task}
				{/foreach}
			{/if}
		{/if}
	</div>
	<div id="sidebar" class="grid_4">
		<p><a href="/mockup/pm.create-task.php">+ New task</a></p>
		{include file="tags.top-list.inc.tpl"}
	</div>
{include file="footer.inc.tpl"}
