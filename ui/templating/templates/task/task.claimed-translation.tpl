{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{Localisation::getTranslation('task_claimed_translation_translation_task_claimed')} <small>{Localisation::getTranslation('task_claimed_translation_please_translate_it')}</small></h1>
        </div>
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation('common_success')}</strong> {Localisation::getTranslation('task_claimed_translation_0')} &ldquo;<strong>{TemplateHelper::uiCleanseHTML($task->getTitle())}</strong>&rdquo;.
        </div>
    </section>
        
    <section>
            <h1>{Localisation::getTranslation('common_what_happens_now')} <small>{Localisation::getTranslation('task_claimed_translation_we_need_your_translation')}</small></h1>
            <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}</p>
            <ol>
                {if empty($allow_download)}
                <li>{Localisation::getTranslation('task_claimed_click')} <strong>{Localisation::getTranslation('task_claimed_translate_using_kato')}</strong></li>
                <li>{sprintf(Localisation::getTranslation('task_claimed_translation_translate_the_file_to_plain'), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
                {else}
                <li>{Localisation::getTranslation('common_can_you_open_file')}</li>
                <li>{sprintf(Localisation::getTranslation('task_claimed_translation_translate_the_file_to'), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
                <li>{Localisation::getTranslation('task_claimed_translation_5')}</li>
               {/if}
            </ol>

        {if isset($user)}
            <p>{sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</p>
        {/if}
        {if $matecat_url != ''}
            <p>{if !empty($allow_download)}{Localisation::getTranslation('task_claimed_alternative_option')} {/if}<a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                <i class="icon-th-list icon-white"></i> {Localisation::getTranslation('task_claimed_translate_using_kato')}</a><br />
                {Localisation::getTranslation('task_claimed_please_read_kato')}
            </p>
        {/if}
    </section>

    <section>
        {if !empty($allow_download)}
        <h3>{Localisation::getTranslation('task_claimed_translation_4')}</h3>
        {/if}
        <p></p>
        <p>
            {if !empty($allow_download)}
            <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation('task_claimed_translation_upload_translated_task')}
            </a>
            {/if}
            {if isset($user)}
            <a href="{urlFor name="claimed-tasks" options="user_id.{$user->getId()}"}" class="btn">
            {else}
            <a href="{urlFor name="home"}" class="btn">
            {/if}
                <i class="icon-arrow-left icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_claimed_tasks')}
            </a>
            <a href="{urlFor name="project-view" options="project_id.{$task->getProjectId()}"}" class="btn">
                <i class="icon-arrow-left icon-black"></i> Just bring me back to the project page.
            </a>
        </p>
    </section>

    {if !empty($allow_download)}
    <p>
        <small>
            ({Localisation::getTranslation('common_cant_find_the_file_on_your_desktop')}
            {sprintf(Localisation::getTranslation('common_download_the_file'), {urlFor name="download-task" options="task_id.$task_id"})})
        </small>
    </p>
    {/if}
