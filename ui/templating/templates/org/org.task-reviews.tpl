{include file="header.tpl"}

<div class="page-header">
    <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>All reviews for this task</small></h1>
</div>

<h2>Input file reviews</h2>
<br />
<p>The following are a list of reviews provided for the input file(s) of this task</p>
{if isset($preReqTasks) && count($preReqTasks) > 0}
    {foreach $preReqTasks as $reviewedTask}
      {if !empty($reviewedTask->getId()) && !empty($reviews[$reviewedTask->getId()])}
        {foreach $reviews[$reviewedTask->getId()] as $review}
            {include file="task/task.review-form.tpl"}
        {/foreach}
      {/if}
    {/foreach}
{elseif isset($projectData) && isset($projectReviews) && count($projectReviews) > 0}
    {assign var="reviewedTask" value=$projectData}
    {foreach $projectReviews as $review}
        {include file="task/task.review-form.tpl"}
    {/foreach}
{else}
    <p class="alert alert-info">No reviews found for this tasks input file(s).</p>
{/if}

<h2>Output file reviews</h2>
<br />
<p>The following are a list of reviews provided for the output file(s) of this task</p>
{if !empty($task->getId()) && !empty($reviews[$task->getId()]) && count($reviews[$task->getId()]) > 0}
    {assign var="reviewedTask" value=$task}
    {foreach $reviews[$task->getId()] as $review}
        {include file="task/task.review-form.tpl"}
    {/foreach}
{else}
    <p class="alert alert-info">No reviews found for this task.</p>
{/if}

{include file="footer.tpl"}
