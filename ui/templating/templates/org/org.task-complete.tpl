{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())} <small>{Localisation::getTranslation('org_task_review_review_this_completed_task')}</small></h1>
</div>

<p>
    {if $claimant != NULL}
        {sprintf(Localisation::getTranslation("org_task_review_3"), $claimantProfile, TemplateHelper::uiCleanseHTML($userName))}
    {else}
        {Localisation::getTranslation("org_task_review_claimant_unavailable")}
    {/if}
    {if !empty($allow_download)}
    {Localisation::getTranslation('org_task_review_0')}
    {/if}
</p>
<p>
    {if !empty($allow_download)}
    <a class="btn btn-primary" href="{urlFor name="download-task-latest-version" options="task_id.$taskId"}">
        <i class="icon-download icon-white"></i> {Localisation::getTranslation('org_task_review_download_output_file')}
    </a>
    {/if}
</p>

<h2 class="page-header">
    {Localisation::getTranslation('org_task_review_review_this_file')}
    <small>{Localisation::getTranslation('org_task_review_1')}</small>
</h2>

<p>{Localisation::getTranslation('org_task_complete_provide_or_view_review')}</p>
<p>
    <a class="btn btn-primary" href="{urlFor name="org-task-review" options="org_id.$orgId|task_id.$taskId"}">
        <i class="icon-list-alt icon-white"></i>{Localisation::getTranslation('org_task_complete_provide_a_review')}
    </a>
    <a class="btn btn-primary" href="{urlFor name="org-task-reviews" options="org_id.$orgId|task_id.$taskId"}">
        <i class="icon-list icon-white"></i>{Localisation::getTranslation('org_task_complete_view_reviews')}
    </a>
</p>

{include file="footer.tpl"}
