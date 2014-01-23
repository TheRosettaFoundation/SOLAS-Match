{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_SEGMENTATION_TASK_CLAIMED)} <small>{Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_PLEASE_SEGMENT_IT)}</small></h1>
        </div>

        <div class="alert alert-success">
            <strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}</strong> {Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_0)} &ldquo;<strong>{$task->getTitle()}</strong>&rdquo;.
        </div>
    </section>

    <section>
            <h1>{Localisation::getTranslation(Strings::COMMON_WHAT_HAPPENS_NOW)}</h1>
            <p>{Localisation::getTranslation(Strings::COMMON_THIS_THIS_WHAT_YOU_NEED_TO_DO_AS_SOON_AS_POSSIBLE)}</p>
            <ol>
                <li>{Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_5)}</li>
                <li>{Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_SEGMENT_THE_FILE)}</li>
                <li>{Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_2)}</li>
            </ol>

        {if isset($user)}
            <p>{sprintf(Localisation::getTranslation(Strings::COMMON_WE_HAVE_ALSO_EMAILED_YOU_THESE_INSTRUCTIONS_TO), {$user->getEmail()})}</p>
        {/if}
    </section>

    <section>
            <h3>{Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_4)}</h3>
            <p></p>
            <p>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                    <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation(Strings::TASK_CLAIMED_SEGMENTATION_UPLOAD_TASK_SEGMENTS)}
                </a>
                <a href="{urlFor name="home"}" class="btn">
                    <i class="icon-arrow-left icon-black"></i> {Localisation::getTranslation(Strings::COMMON_NO_JUST_BRING_ME_BACK_TO_THE_TASK_PAGE)}
                </a>
            </p>
    </section>

    <p>
        <small>({Localisation::getTranslation(Strings::COMMON_CANT_FIND_THE_FILE_ON_YOUR_DESKTOP)}
            {sprintf(Localisation::getTranslation(Strings::COMMON_DOWNLOAD_THE_FILE), {urlFor name="download-task" options="task_id.$task_id"})})
        </small>
    </p>

