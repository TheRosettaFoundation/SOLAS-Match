{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_TRANSLATION_TASK_CLAIMED)} <small>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_PLEASE_TRANSLATE_IT)}</small></h1>
        </div>
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}</strong> {Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_0)} &ldquo;<strong>{$task->getTitle()}</strong>&rdquo;.
        </div>
    </section>
        
    <section>
            <h1>What now? <small>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_WE_NEED_YOUR_TRANSLATION)}</small></h1>
            <p>{Localisation::getTranslation(Strings::COMMON_THIS_THIS_WHAT_YOU_NEED_TO_DO_AS_SOON_AS_POSSIBLE)}</p>
            <ol>
                <li>{Localisation::getTranslation(Strings::COMMON_CAN_YOU_OPEN_FILE)}</li>
                <li>{sprintf(Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_TRANSLATE_THE_FILE_TO), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
                <li>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_5)}</li>
            </ol>

        {if isset($user)}
            <p>{sprintf(Localisation::getTranslation(Strings::COMMON_WE_HAVE_ALSO_EMAILED_YOU_THESE_INSTRUCTIONS_TO), {$user->getEmail()})}</p>
        {/if}
    </section>

    <section>
        <h3>{Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_4)}</h3>
        <p></p>
        <p>
            <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation(Strings::TASK_CLAIMED_TRANSLATION_UPLOAD_TRANSLATED_TASK)}
            </a>
            <a href="{urlFor name="home"}" class="btn">
                <i class="icon-arrow-left icon-black"></i> {Localisation::getTranslation(Strings::COMMON_NO_JUST_BRING_ME_BACK_TO_THE_TASK_PAGE)}
            </a>
        </p>
    </section>

    <p>
        <small>
            ({Localisation::getTranslation(Strings::COMMON_CANT_FIND_THE_FILE_ON_YOUR_DESKTOP)}
            {sprintf(Localisation::getTranslation(Strings::COMMON_DOWNLOAD_THE_FILE), {urlFor name="download-task" options="task_id.$task_id"})})
        </small>
    </p>

