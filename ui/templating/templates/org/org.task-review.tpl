{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_COMPLETED_TASK)}</small></h1>
</div>

<h2 class="page-header">{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_FILE)} <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_1)}</small></h2>
{include file="handle-flash-messages.tpl"}

<p>
    {sprintf(Localisation::getTranslation(Strings::ORG_TASK_REVIEW_THE_VOLUNTEER), {urlFor name="user-public-profile" options="user_id.{$translator->getId()}"}, $translator->getDisplayName())}
</p>

{assign var="reviewedTask" value=$task}
{include file="task/task.review-form.tpl"}

{include file="footer.tpl"}
