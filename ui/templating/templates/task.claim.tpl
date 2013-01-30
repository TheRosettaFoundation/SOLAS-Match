{include file="header.tpl"}
{assign var=task_id value=$task->getId()}

<section>
	<div class="page-header">
		<h1>{$task->getTitle()} <small>Translation task</small></h1>
	</div>
</section>

<section>
	<h1>Do you want to translate this file? <small>After downloading</small></h1>
	<hr>
	<h3>Review this checklist for your downloaded file <small>Will you be able to translate this file?</small></h3>
        <p style="margin-bottom:20px;"></p>
	<ol>
		<li>Can you <strong>open the file</strong> on your computer?</li>
		<li><strong>Will you have enough time to translate</strong> this file? Check how long the file is.</li>
		{if $task->getTargetLanguageCode()}
			<li>Do you think you're capable of translating this file <strong>to {$targetLanguage->getName()}</strong>?</li>
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
			<button type="submit" class="btn btn-primary">
                            <i class="icon-ok-circle icon-white"></i> Yes, I promise I will translate this file
                        </button>
			<a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                            <i class="icon-ban-circle icon-black"></i> No, just bring me back to the task page
                        </a>
		</p>
	</form>
</section>

{include file="footer.tpl"}
