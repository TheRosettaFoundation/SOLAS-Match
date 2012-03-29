{include file="header.tpl"}

<div class="page-header">
	<h1>Dashboard <small>Overview of your tasks for translation</small></h1>
</div>

<a class="btn btn-primary" href="{urlFor name="task-upload"}">Add new task</a>

{if isset($my_tasks)}
	<table class="table table-striped">
		<thead>
		<tr>
			<th>Task title</th>
		</tr>
		</thead>
	<tbody>
		{foreach from=$my_tasks item=task}
			<tr>
				<td>{$task->getTitle()}</td>
			</tr>
		{/foreach}
	</tbody>
	</table>
{else}
	<h2>What now?</h2>
	<p>You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new task for that content.</p>
{/if}

{include file="footer.tpl"}
