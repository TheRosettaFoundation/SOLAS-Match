{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('common_translation_task')}</small></h1>
        </div>
    </section>

    <section>
        <h2>{Localisation::getTranslation('task_claim_translation_0')}</h2>
        <p style="margin-bottom:20px;"></p>
        <ol>
            <li>{Localisation::getTranslation('task_claim_translation_2')}</li>
            <li>{sprintf(Localisation::getTranslation('task_claim_translation_3'), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
            {if !empty($matecat_url)}
            <li>{sprintf(Localisation::getTranslation('task_claim_warning_kato'), {Localisation::getTranslation('task_claim_view_on_kato')}, {Localisation::getTranslation('common_download_file')}, {Localisation::getTranslation('task_claim_translation_5')})}</li>
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
            <h3>{Localisation::getTranslation('common_it_is_time_to_decide')}</h3>
            <p>
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation('task_claim_translation_5')}
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                </a>
            </p>
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
    </section>
    
