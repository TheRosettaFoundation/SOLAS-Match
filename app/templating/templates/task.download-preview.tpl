{include file="header.tpl"}
{assign var=task_id value=$task->getTaskId()}

<section>
	<div class="page-header">
		<h1>{$task->getTitle()} <small>Translation task</small></h1>
	</div>

	<h3>Save the file to your computer</h3>
	<p>
		Your browser should have now asked you to download this task file. If that didn't happen you can <a href="{urlFor name="download-task" options="task_id.$task_id"}">click here to download the file directly</a>.
	</p>
</section>

<section>
	<h1>Do you want to translate this file? <small>After downloading</small></h1>
	<hr>
	<h3>Review this checklist for your downloaded file <small>Will you be able to translate this file?</small></h3>
	<ol>
		<li>Can you <strong>open the file</strong> on your computer?</li>
		<li><strong>Will you have enough time to translate</strong> this file? Check how long the file is.</li>
		{if $task->getTargetId()}
			<li>Do you think you're capable of translating this file <strong>to {Languages::languageNameFromId($task->getTargetId())}</strong>?</li>
		{/if}
	</ol>
</section>

<section>
	<form class="well" method="post" action="{urlFor name="claim-task"}">
		<h3>It&rsquo;s time to decide</h3>
		<p>
			Do you want to translate this file? When you are finished translating the file, you will need to upload it again.
		</p>
		<p>
			<input type="hidden" name="task_id" value="{$task_id}">
			<button type="submit" class="btn btn-primary">Yes, I promise I will translate this file</button>
			<a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">No, just bring me back to the task page</a>
		</p>
	</form>
</section>

<iframe src="{urlFor name="download-task" options="task_id.$task_id"}" width="1" height="1" frameborder="no"></iframe>

{include file="footer.tpl"}
