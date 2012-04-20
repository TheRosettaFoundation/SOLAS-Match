{include file="header.tpl"}

<div class="page-header">
	<h1>Dashboard <small>Overview of your tasks for translation</small></h1>
</div>

<a class="btn btn-primary" href="{urlFor name="task-upload"}"><i class="icon-upload icon-white"></i> Add new task</a>

{if isset($my_tasks)}
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Task title</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$my_tasks item=task}
				<tr>
					{assign var="task_id" value=$task->getTaskId()}
					{if $task_dao->getLatestFileVersion($task) > 0}
						<td>
							<a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
						</td>
						<td>
							<a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}" class="btn btn-small">Download&nbsp;updated&nbsp;file</a>
						</td>
						<td>
							<a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-small">Archive</a>
						</td>
					{else}
						<td>
							<a href="{urlFor name="task" options="task_id.$task_id"}">{$task->getTitle()}</a>
						</td>
						<td>
						</td>
						<td>
							<a href="{urlFor name="archive-task" options="task_id.$task_id"}" class="btn btn-small">Archive</a>
						</td>
					{/if}
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<h2>What now?</h2>
	<p>You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new task for that content.</p>
{/if}

{include file="footer.tpl"}
