{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>Review this completed task</small></h1>
</div>

{include file="handle-flash-messages.tpl"}

<p>
    The volunteer, {$translator->getDisplayName()}, has completed work on this task. You can download
    the output file <a href="{urlFor name="download-task-latest-version" options="task_id.$taskId"}">
    here</a>. Please provide a review of this task to help improve the system.
</p>

{include file="task.review-form.tpl"}

{include file="footer.tpl"}
