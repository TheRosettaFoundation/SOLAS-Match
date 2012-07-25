{include file="header.tpl" body_id="home"}

{if !isset($user)}
	<div class="hero-unit">
		<h1>Help NGOs translate</h1>
		<p>Non&ndash;governmental agencies need <em>your</em> translation skills.</p>
		<p>
			<a class="btn btn-primary btn-large" href="{urlFor name="register"}">
				Register to Volunteer
			</a>
			<a class="btn btn-large" href="{urlFor name="login"}">
				Login
			</a>
		</p>
	</div>
{/if}

{if isset($flash['error'])}
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>Warning! </strong>{$flash['error']}</p>
    </div>
{/if}

<div class="page-header">
	<h1>Translation tasks <small>Claim a task, translate it, upload it</small></h1>
</div>

<div class="row">
	<div class="span8">
		{if isset($tasks)}
			<div id="tasks">
				{foreach from=$tasks item=task name=tasks_loop}
					{include file="task.summary-link.tpl" task=$task}
				{/foreach}
			</div>
		{else}
			<div class="alert alert-warning">
				{if isset($user_is_organisation_member)}
					<strong>No open tasks</strong> You can upload a new task from your Dashboard in the navigation menu above.
				{else}
					<strong>No tasks available</strong> Please wait for organisations to upload more translation tasks.
				{/if}
			</div>
		{/if}
	</div>

    <div class="span4">
        {include file="tags.user-tags.inc.tpl"}
	
		{include file="tags.top-list.inc.tpl"}
	</div>
</div>

{if !isset($user)}
    <div class="alert">
        <p>Help us match you with the most suitable translation tasks.</p>
        <p><a href={urlFor name="register"}>Register now</a> to find the jobs best suited to you.</p>
    </div>
{/if}

{include file="footer.tpl"}
