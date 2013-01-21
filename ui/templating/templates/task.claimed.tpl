{include file="header.tpl"}
{assign var=task_id value=$task->getId()}

<section>
    <div class="page-header">
            <h1>Task claimed <small>Please translate it!</small></h1>
    </div>

    <div class="alert alert-success">
            <strong>Success</strong> You have claimed the task &ldquo;<strong>{$task->getTitle()}</strong>&rdquo;.
    </div>
</section>

<section>
	<h1>What now? <small>We need your translation</small></h1>

	<p>This this what you need to do (as soon as possible):</p>
        
	<ol>
		<li><strong>Open the file</strong> that you have already saved to your computer.</li>
		{if $task->getTargetLanguageId()}
			<li><strong>Translate the file</strong> to <strong>{TemplateHelper::languageNameFromId($task->getTargetLanguageId())}</strong> using your favourite translation software.</li>
		{/if}
		<li><strong>Upload your finished translated file</strong> to the task page.</li>
	</ol>

    {if isset($user)}
        <p>We have also emailed you these instructions to <b>{$user->getEmail()}</b>.</p>
    {/if}
</section>

<section>
	<h3>When you have finished translating the file you downloaded:</h3>
        <p></p>
	<p>
            <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-share-alt icon-white"></i> Visit The Task Page To Upload My Translation
            </a>
            <a href="{urlFor name="home"}" class="btn">
                <i class="icon-arrow-left icon-black"></i> Go Back Home
            </a>
        </p>
</section>

    <p><small>(Can't find the file on your desktop? 
    <a href="{urlFor name="download-task" options="task_id.$task_id"}">Download the file</a>
    and save it to your desktop.)</small></p>

{include file="footer.tpl"}
