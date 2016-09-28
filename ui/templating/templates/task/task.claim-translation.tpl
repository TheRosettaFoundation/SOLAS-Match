{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('common_translation_task')}</small></h1>
        </div>
    </section>

    <section>
        <h2>{Localisation::getTranslation('task_claim_translation_0')} <small>{Localisation::getTranslation('common_after_downloading')}</small></h2>
        <hr />
        <h3>{Localisation::getTranslation('common_review_this_checklist_for_your_downloaded_file')} <small>{Localisation::getTranslation('task_claim_translation_1')}</small></h3>
        <p style="margin-bottom:20px;"></p>
        <ol>
            <li>{Localisation::getTranslation('common_can_you_open_file')}</li>
            <li>{Localisation::getTranslation('task_claim_translation_2')}</li>
            <li>{sprintf(Localisation::getTranslation('task_claim_translation_3'), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
        </ol>
    </section>

    <section>
        <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
            <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
                <i class="icon-download icon-white"></i> {Localisation::getTranslation('common_download_file')}</a>
            <h3>{Localisation::getTranslation('common_it_is_time_to_decide')}</h3>
            <p>
                {Localisation::getTranslation('task_claim_translation_0')} {Localisation::getTranslation('task_claim_translation_4')}
            </p>
            <p>
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation('task_claim_translation_5')}
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                </a>
            </p>
        </form>
    </section>
    
