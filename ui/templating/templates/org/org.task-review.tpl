{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_COMPLETED_TASK)}</small></h1>
</div>

<p>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_0)}</p>
<p>
    <a class="btn btn-primary" href="{urlFor name="download-task-latest-version" options="task_id.$taskId"}">
        <i class="icon-download icon-white"></i> {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_DOWNLOAD_OUTPUT_FILE)}
    </a>
</p>

<h2 class="page-header">{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_FILE)} <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_1)}</small></h2>
{include file="handle-flash-messages.tpl"}

<p>
    {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_THE_VOLUNTEER)}, <a href="{urlFor name="user-public-profile" options="user_id.{$translator->getId()}"}">
    {$translator->getDisplayName()}</a>, {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_2)}
</p>

{include file="task/task.review-form.tpl"}

{include file="footer.tpl"}
