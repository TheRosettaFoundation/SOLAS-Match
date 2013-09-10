{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::COMMON_TRANSLATION_TASK)}</small></h1>
        </div>
    </section>

    <section>
        <h2>{Localisation::getTranslation(Strings::TASK_CLAIM_TRANSLATION_0)} <small>{Localisation::getTranslation(Strings::COMMON_AFTER_DOWNLOADING)}</small></h2>
        <hr />
        <h3>{Localisation::getTranslation(Strings::COMMON_REVIEW_THIS_CHECKLIST_FOR_YOUR_DOWNLOADED_FILE)} <small>{Localisation::getTranslation(Strings::TASK_CLAIM_TRANSLATION_1)}</small></h3>
        <p style="margin-bottom:20px;"></p>
        <ol>
            <li>{Localisation::getTranslation(Strings::COMMON_CAN_YOU_OPEN_FILE)}</li>
            <li>{Localisation::getTranslation(Strings::TASK_CLAIM_TRANSLATION_2)}</li>
            <li>{sprintf(Localisation::getTranslation(Strings::TASK_CLAIM_TRANSLATION_3), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
        </ol>
    </section>

    <section>
        <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
            <h3>{Localisation::getTranslation(Strings::COMMON_IT_IS_TIME_TO_DECIDE)}</h3>
            <p>
                {Localisation::getTranslation(Strings::TASK_CLAIM_TRANSLATION_0)} {Localisation::getTranslation(Strings::TASK_CLAIM_TRANSLATION_4)}
            </p>
            <p>
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation(Strings::TASK_CLAIM_TRANSLATION_5)}
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation(Strings::COMMON_NO_JUST_BRING_ME_BACK_TO_THE_TASK_PAGE)}
                </a>
            </p>
        </form>
    </section>

    <iframe src="{urlFor name="download-task" options="task_id.$task_id"}" width="1" height="1" frameborder="no"></iframe>
