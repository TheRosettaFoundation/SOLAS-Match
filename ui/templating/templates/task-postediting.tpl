{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>Post-Editing task details</small></h1>
</div>

{if $errorMessage}
    <p class="alert alert-error">{$errorMessage}</p>
{/if}

<h2>Download</h2>
<p>Download the following files and merge them</p>

{foreach from=$preReqTasks item=pTask}
    {assign var="pTaskId" value=$pTask->getId()}
    <p>Download {$pTask->getTitle()} <a href="{urlFor name="download-task" options="task_id.$pTaskId"}">here</a></p>
{/foreach}

<h2>Upload</h2>
<p>Upload the merged file here:</p>
<form method="post" enctype="multipart/form-data" action="{urlFor name="task" options="task_id.$taskId"}">
    <p><input type="file" name="{$fieldName}" id="{$fieldName}" /></p>
    <p><input type="submit" class="btn btn-primary" /></p>
</form>

{include file="footer.tpl"}
