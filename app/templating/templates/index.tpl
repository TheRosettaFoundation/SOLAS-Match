{include file="header.tpl"}

<div class="row">
	{if !isset($user)}
		<div class="hero-unit">
			<h1>Help NGOs translate</h1>
			<p>Non&ndash;governmental agencies need <em>your</em> translation skills.</p>
			<p>
				<a class="btn btn-primary btn-large" href="{$url_register}">
					Register to Volunteer
				</a>
				<a class="btn btn-large" href="{$url_login}">
					Login
				</a>
			</p>
		</div>
	{/if}

	<div class="span8">
		{if isset($tasks)}
			<div class="page-header">
				<h1>Translation tasks <small>Pick one that you could help translate</small></h1>
			</div>
			{if isset($tasks)}
				{foreach from=$tasks item=task name=tasks_loop}
					{include file="task.summary-link.tpl" task=$task}
				{/foreach}
			{/if}
		{else}
			{include file="task.empty-stream.tpl"}
		{/if}
	</div>
	
	<div class="span4">
		{include file="tags.top-list.inc.tpl"}
	</div>
</div>

{include file="footer.tpl"}
