{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{Localisation::getTranslation('task_claimed_proofreading_proofreading_task_claimed')} <small>{Localisation::getTranslation('task_claimed_proofreading_please_proofread_it')}</small></h1>
        </div>
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf(Localisation::getTranslation('task_claimed_proofreading_0'), {TemplateHelper::uiCleanseHTML($task->getTitle())})}
        </div>
    </section>

    <section>
        <h1>{Localisation::getTranslation('common_what_happens_now')}</h1>
        <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}</p>
        <ol>
                {if $matecat_url != ''}
                <li>{Localisation::getTranslation('task_claimed_please_read_kato')}</li>
                <li>Also please note that you must wait for translation to be complete (100% translated) before starting revising.</li>
                <li>{sprintf(Localisation::getTranslation('task_claimed_proofreading_proofread_the_file_in'), {TemplateHelper::getLanguage($task->getTargetLocale())})}<br />
                    <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                    <i class="icon-th-list icon-white"></i> {Localisation::getTranslation('task_claimed_proofread_using_kato')}</a></li>
                {else}
                <li>{Localisation::getTranslation('task_claimed_proofreading_1')}</li>
                <li>{sprintf(Localisation::getTranslation('task_claimed_proofreading_proofread_the_file_in'), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
                <li>{Localisation::getTranslation('task_claimed_proofreading_upload_the_proofread_file')}</li>
                {/if}
        </ol>
        {if isset($user)}
            <p>{sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</p>
        {/if}
    </section>

    <section>
        <h3>When you have finished revising:</h3>
        <p>
            <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation('task_claimed_proofreading_upload_proofread_task')}
            </a>
            {if isset($user)}
            <a href="{urlFor name="claimed-tasks" options="user_id.{$user->getId()}"}" class="btn">
            {else}
            <a href="{urlFor name="home"}" class="btn">
            {/if}
                <i class="icon-arrow-left icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_claimed_tasks')}
            </a>
            {if $isSiteAdmin}
            <a href="{urlFor name="project-view" options="project_id.{$task->getProjectId()}"}" class="btn">
                <i class="icon-arrow-left icon-black"></i> Just bring me back to the project page.
            </a>
            {/if}
        </p>
    </section>

    {if !empty($allow_download)}
    <p>
        <small>({Localisation::getTranslation('common_cant_find_the_file_on_your_desktop')}
            {sprintf(Localisation::getTranslation('common_download_the_file'), {urlFor name="download-task" options="task_id.$task_id"})})
        </small>
    </p>
    {/if}
