{include file="header.inc.tpl"}

<div class="row">
	{if !isset($user)}
		<div class="hero-unit">
			<h1>Help NGOs translate</h1>
			<p>Non&ndash;governmental agencies need <em>your</em> translation skills.</p>
			<p>
				<a class="btn btn-primary btn-large" href="{$url_register}">
					Register to Volunteer
				</a>
			</p>
		</div>
	{/if}
	<div class="span8">
		{if isset($tasks)}
			<h2 class="section_top">Translation Tasks</h2>
			{if isset($tasks)}
				{foreach from=$tasks item=task name=tasks_loop}
					{include file="task.inc.tpl" task=$task}
				{/foreach}
			{/if}
		{/if}
	</div>
	<div class="span4">
		<p><a href="{urlFor name="task-upload"}">+ Create new task</a></p>
		{include file="tags.top-list.inc.tpl"}
	</div>
</div>

{include file="footer.inc.tpl"}
