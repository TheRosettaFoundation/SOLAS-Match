{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_COMPLETED_TASK)}</small></h1>
</div>

<h2 class="page-header">{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_FILE)} <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_1)}</small></h2>
{include file="handle-flash-messages.tpl"}

<p>
    {assign var="translatorId" value=$translator->getId()}
    {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_THE_VOLUNTEER)}, <a href="{urlFor name="user-public-profile" options="user_id.$translatorId"}">
    {$translator->getDisplayName()}</a>, {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_2)}
</p>

{assign var="reviewedTask" value=$task}
{include file="task/task.review-form.tpl"}

{include file="footer.tpl"}
