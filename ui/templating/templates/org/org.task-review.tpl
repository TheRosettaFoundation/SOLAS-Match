{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>{Localisation::getTranslation('org_task_review_review_this_completed_task')}</small></h1>
</div>

<h2 class="page-header">{Localisation::getTranslation('org_task_review_review_this_file')} <small>{Localisation::getTranslation('org_task_review_1')}</small></h2>
{include file="handle-flash-messages.tpl"}

<p>
    {sprintf(Localisation::getTranslation('org_task_review_the_volunteer'), {urlFor name="user-public-profile" options="user_id.{$translator->getId()}"}, $translator->getDisplayName())}
</p>

{assign var="reviewedTask" value=$task}
{include file="task/task.review-form.tpl"}

{include file="footer.tpl"}
