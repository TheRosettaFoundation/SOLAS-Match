{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_DESEGMENTATION_TASK)}</small></h1>
        </div>
    </section>

    <section>
        <h2>{Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_0)} <small>{Localisation::getTranslation(Strings::COMMON_AFTER_DOWNLOADING)}</small></h2>
        <hr />
        <h3>{Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_1)} <small>{Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_2)}</small></h3>
        <p style="margin-bottom:20px;"></p>
        <ol>
            {if $taskMetadata->getContentType() != ''}
                <li>{Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_3)} <strong>{$taskMetadata->getContentType()}</strong> {Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_ON_YOUR_COMPUTER)}</li>
            {/if}
            <li><strong>{Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_4)}</strong> {Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_5)}</li>
            <li>{Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_6)} <strong>{$targetLanguage->getName()}</strong>?</li>
        </ol>
    </section>

    <section>
        <h3>{Localisation::getTranslation(Strings::COMMON_IT_IS_TIME_TO_DECIDE)}</h3>
        <p> 
            {Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_7)} {Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_8)}
        </p>
        <p> 
            <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation(Strings::TASK_CLAIM_DESEGMENTATION_9)}
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation(Strings::COMMON_NO_JUST_BRING_ME_BACK_TO_THE_TASK_PAGE)}
                </a>
            </form>
        </p>
    </section>
