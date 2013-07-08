{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::COMMON_PROOFREADING_TASK)}</small></h1>
        </div>
    </section>

    <section>
        <h2>{Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_0)} <small>{Localisation::getTranslation(Strings::COMMON_AFTER_DOWNLOADING)}</small></h2>
        <hr />
        <h3>{Localisation::getTranslation(Strings::COMMON_REVIEW_THIS_CHECKLIST_FOR_YOUR_DOWNLOADED_FILE)} <small>{Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_1)}</small></h3>
        <p style="margin-bottom:20px;"></p>
        <ol>
            <li>{Localisation::getTranslation(Strings::COMMON_CAN_YOU)} <strong>{Localisation::getTranslation(Strings::COMMON_OPEN_THE_FILE)}</strong> {Localisation::getTranslation(Strings::COMMON_ON_YOUR_COMPUTER)}</li>
            <li><strong>{Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_2)}</strong> {Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_THIS_FILE)} {Localisation::getTranslation(Strings::COMMON_CHECK_HOW_LONG_THE_FILE_IS)}.</li>
            <li>{Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_3)} <strong>in {TemplateHelper::getLanguage($task->getTargetLocale())}</strong>?</li>
        </ol>
    </section>

    <section>
        <h3>{Localisation::getTranslation(Strings::COMMON_IT_IS_TIME_TO_DECIDE)}</h3>
        <p> 
            {Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_0)} {Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_4)}
        </p>
        <p> 
            <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation(Strings::TASK_CLAIM_PROOFREADING_5)}
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation(Strings::COMMON_NO_JUST_BRING_ME_BACK_TO_THE_TASK_PAGE)}
                </a>
            </form>
        </p>
    </section>

    <iframe src="{urlFor name="download-task" options="task_id.$task_id"}" width="1" height="1" frameborder="no"></iframe>

