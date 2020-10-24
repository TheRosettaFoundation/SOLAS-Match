{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{Localisation::getTranslation('task_claimed_segmentation_segmentation_task_claimed')} <small>{Localisation::getTranslation('task_claimed_segmentation_please_segment_it')}</small></h1>
        </div>

        <div class="alert alert-success">
            <strong>{Localisation::getTranslation('common_success')}</strong> {Localisation::getTranslation('task_claimed_segmentation_0')} &ldquo;<strong>{TemplateHelper::uiCleanseHTML($task->getTitle())}</strong>&rdquo;.
        </div>
    </section>

    <section>
            <h1>{Localisation::getTranslation('common_what_happens_now')}</h1>
            <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}</p>
            <ol>
                <li>{Localisation::getTranslation('task_claimed_segmentation_5')}</li>
                <li>{Localisation::getTranslation('task_claimed_segmentation_segment_the_file')}</li>
                <li>{Localisation::getTranslation('task_claimed_segmentation_2')}</li>
            </ol>

        {if isset($user)}
            <p>{sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</p>
        {/if}
    </section>

    <section>
            <h3>{Localisation::getTranslation('task_claimed_segmentation_4')}</h3>
            <p></p>
            <p>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                    <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation('task_claimed_segmentation_upload_task_segments')}
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

    <p>
        <small>({Localisation::getTranslation('common_cant_find_the_file_on_your_desktop')}
            {sprintf(Localisation::getTranslation('common_download_the_file'), {urlFor name="download-task" options="task_id.$task_id"})})
        </small>
    </p>

