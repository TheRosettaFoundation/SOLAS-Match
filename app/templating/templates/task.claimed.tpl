{include file="header.tpl"}
{assign var=task_id value=$task->getTaskId()}

<div class="page-header">
	<h1>Task claimed <small>Please translate it!</small></h1>
</div>

<div class="alert alert-success">
	<strong>Success</strong> You have claimed the task &ldquo;<strong>{$task->getTitle()}</strong>&rdquo;.
</div>

<section>
	<h1>What now? <small>We need your translation</small></h1>

	<p>This this what you need to do (as soon as possible):</p>

	<ol>
		<li><strong>Open the file</strong> that you have already saved to your computer.</li>
		{if $task->getTargetId()}
			<li><strong>Translate the file</strong> to <strong>{Languages::languageNameFromId($task->getTargetId())}</strong> using your favourite translation software.</li>
		{/if}
		<li><strong>Upload your finished translated file</strong> to <a href="{urlFor name="task" options="task_id.$task_id"}">the task page</a>.</li>
	</ol>
</section>

<section>
	<h3>When you have finished translating the file you downloaded:</h3>

	<p><a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">Visit the task page to upload my translation</a> <a href="{urlFor name="home"}" class="btn">Go back home</a> </p>
</section>

{include file="footer.tpl"}
