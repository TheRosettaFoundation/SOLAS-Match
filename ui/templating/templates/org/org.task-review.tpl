{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>Review this completed task</small></h1>
</div>

<p>Click the button below to Download the output file for this task</p>
<p>
    <a class="btn btn-primary" href="{urlFor name="download-task-latest-version" options="task_id.$taskId"}">
        <i class="icon-download icon-white"></i> Download Output File
    </a>
</p>

<h2 class="page-header">Review this file <small>Helps us match volunteers and tasks</small></h2>
{include file="handle-flash-messages.tpl"}

<p>
    The volunteer, <a href="{urlFor name="user-public-profile" options="user_id.{$translator->getId()}"}">
    {$translator->getDisplayName()}</a>, has completed work on this task. Please provide a review of this task 
    to help improve the system.
</p>

{include file="task/task.review-form.tpl"}

{include file="footer.tpl"}
