{include file="header.inc.tpl"}
	<div class="grid_8">
		{if isset($tasks)}
			<h2 class="section_top">Translation Tasks</h2>
			{* Language drop-down lists.
				{assign var=source_langs value=$s->tasks->allSourceLangauges()}
				{assign var=target_langs value=$s->tasks->allTargetLangauges()}
				{if $source_langs && $target_langs}
					<form class="language_filter" method="post" action="/">
						From
						<select>
							<option value="all">All languages</option> 
							{foreach from=$source_langs item=source}
								<option value="{$source}" {if $source=='English'}selected="selected"{/if}>{$source}</option>
							{/foreach}
						</select>
						to
						<select>
							<option value="all">all languages</option> 
							{foreach from=$target_langs item=target}
								<option value="{$source}">{$target}</option>
							{/foreach}
						</select>
						<input type="submit" value="Filter" />
					</form>
					<br />
				{/if}
			*}
			{if isset($tasks)}
				{foreach from=$tasks item=task name=tasks_loop}
					{include file="task.inc.tpl" task=$task}
				{/foreach}
			{/if}
		{/if}
	</div>
	<div id="sidebar" class="grid_4">
		<br><br>
		<p><a href="/task/create/">+ New task</a></p>
		{include file="tags.top-list.inc.tpl"}
	</div>
{include file="footer.inc.tpl"}
