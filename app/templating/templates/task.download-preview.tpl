{include file="header.tpl"}

<section>
	<div class="page-header">
		<h1>{$task->getTitle()} <small>Translation task</small></h1>
	</div>

	{assign var=task_id value=$task->getTaskId()}
	<iframe src="{urlFor name="download-task" options="task_id.$task_id"}" width="1" height="1" frameborder="no"></iframe>

	<h3>Downloading preview of task...</h3>
	<p>
		Your browser should ask you to download this task file. If that didn't happen you can <a href="{urlFor name="download-task" options="task_id.$task_id"}">click here to download the file directly</a>.
	</p>
</section>

<section>
	<h1>Do you want to translate this file?</h1>
	<hr>
	<p>This is what it means to be a volunteer here.</p>
	<h3>Tips for Reviewing the File <small>Will you be able to translate this file?</small></h3>

	<ol>
		<li><strong>Review the file</strong> that you have downloaded to your computer.</li>
		<li>Make sure that you <strong>can open the file</strong> on your computer.</li>
		<li>Check how long the file is, <strong>will you have enough time to translate</strong> this file?</li>
		{if $task->getTargetId()}
			<li>Are you sure you are comfortable translating the file <strong>to {Languages::languageNameFromId($task->getTargetId())}</strong>?</li>
		{/if}
	</ol>

	<h3>It's time to decide</h3>
	<p>
		It's now time to decide if you want to commit to translating this file. When you are finished translating the file, you will need to upload it again.
	</p>

	<p>
		<a href="#" class="btn btn-primary">Yes, I promise I will translate this file</a> <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">No, just bring me back to the task page</a>
	</p>
</section>
{include file="footer.tpl"}
