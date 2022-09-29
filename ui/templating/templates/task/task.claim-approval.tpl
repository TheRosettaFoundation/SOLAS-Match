{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>Approval Task</small></h1>
        </div>
    </section>

    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <section>
        <h2>Do you want to approve this file?</h2>
        <p style="margin-bottom:20px;"></p>
        <ol>
            <li>Will you have enough time to approve this file? Check how long the file is.</li>
            <li>
                {sprintf('Do you think you are capable of revising a file in <strong>%s?</strong>', {TemplateHelper::getLanguage($task->getTargetLocale())})}
            </li>
            {if empty($memsource_task)}
            <li>
                {sprintf(Localisation::getTranslation('task_claim_proofreading_6'), $projectFileDownload)}
            </li>
            {/if}
            {if !empty($matecat_url)}
            <li>
                {sprintf(Localisation::getTranslation('task_claim_warning_kato'), {Localisation::getTranslation('task_claim_view_on_kato')}, {Localisation::getTranslation('common_download_file')}, 'Yes, I promise I will approve this file')}
            </li>
            {/if}
            {if !empty($memsource_task) || empty($allow_download)}
            <li>
                Also please note that you must wait for translation/revision to be complete (100% translated/revised) before starting approval.
            </li>
            {/if}
        </ol>
    </section>

    <section>
        <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
        {if !empty($matecat_url)}
        <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
            <i class="icon-th-list icon-white"></i> {Localisation::getTranslation('task_claim_view_on_kato')}
        </a>
        {/if}

        {if !empty($allow_download)}
         <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
            <i class="icon-download icon-white"></i> {Localisation::getTranslation('common_download_file')}</a>
        {/if}
        {if !empty($memsource_task)}
        <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
            <i class="icon-download icon-white"></i> Download Original File in its source language</a>
        {/if}
        <h3>{Localisation::getTranslation('common_it_is_time_to_decide')}</h3>
        <p> 
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> Yes, I promise I will approve this file
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                </a>
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        </p>
    </section>
