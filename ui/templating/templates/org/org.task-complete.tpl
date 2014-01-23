{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_COMPLETED_TASK)}</small></h1>
</div>

<p>
    {if $claimant != NULL}
        {sprintf(Localisation::getTranslation("org_task_review_3"), $claimantProfile, $claimant->getDisplayName())}
    {else}
        {Localisation::getTranslation("org_task_review_claimant_unavailable")}
    {/if}
    {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_0)}
</p>
<p>
    <a class="btn btn-primary" href="{urlFor name="download-task-latest-version" options="task_id.$taskId"}">
        <i class="icon-download icon-white"></i> {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_DOWNLOAD_OUTPUT_FILE)}
    </a>
</p>

<h2 class="page-header">
    {Localisation::getTranslation(Strings::ORG_TASK_REVIEW_REVIEW_THIS_FILE)}
    <small>{Localisation::getTranslation(Strings::ORG_TASK_REVIEW_1)}</small>
</h2>

<p>Use the buttons below to provide a review for the current task or view reviews already provided for this task</p>
<p>
    <a class="btn btn-primary" href="{urlFor name="org-task-review" options="org_id.$orgId|task_id.$taskId"}">
        <i class="icon-list-alt icon-white"></i> Provide a Review
    </a>
    <a class="btn btn-primary" href="{urlFor name="org-task-reviews" options="org_id.$orgId|task_id.$taskId"}">
        <i class="icon-list icon-white"></i> View Reviews
    </a>
</p>

{include file="footer.tpl"}
