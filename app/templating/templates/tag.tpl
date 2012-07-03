{include file="header.tpl"}
<div class="page-header">
	<h1>{$tag} <small>Find tasks tagged with this tag</small></h1>
</div>

<div class="row">
	<div class="span8">
		{if isset($tasks)}
			<div id="tasks">
				{foreach from=$tasks item=task}
					{include file="task.summary-link.tpl" task=$task}
				{/foreach}
			</div>
		{else}
			<div class="alert alert-warning">
				<strong>No open tasks</strong> Sorry, there are currently no open tasks for this label.
			</div>
		{/if}
	</div>

	<div class="span4">
		{include file="tags.top-list.inc.tpl"}
	</div>
</div>

{include file="footer.tpl"}
