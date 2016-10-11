{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('task_claim_desegmentation_desegmentation_task')}</small></h1>
        </div>
    </section>

    <section>
        <h2>{Localisation::getTranslation('task_claim_desegmentation_0')} <small>{Localisation::getTranslation('common_after_downloading')}</small></h2>
        <hr />
        <h3>{Localisation::getTranslation('task_claim_desegmentation_1')} <small>{Localisation::getTranslation('task_claim_desegmentation_2')}</small></h3>
        <p style="margin-bottom:20px;"></p>
        <ol>
            {if $taskMetadata->getContentType() != ''}
                <li>{sprintf(Localisation::getTranslation('task_claim_desegmentation_3'), {$taskMetadata->getContentType()})}</li>
            {/if}
            <li>{Localisation::getTranslation('task_claim_desegmentation_4')}</li>
            <li>{sprintf(Localisation::getTranslation('task_claim_desegmentation_6'), {$targetLanguage->getName()})}</li>
        </ol>
    </section>

    <section>
        <p> 
            <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                <h3>{Localisation::getTranslation('common_it_is_time_to_decide')}</h3>
                <p> 
                    {Localisation::getTranslation('task_claim_desegmentation_7')} {Localisation::getTranslation('task_claim_desegmentation_8')}
                </p>
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation('task_claim_desegmentation_9')}
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                </a>
                <input type="hidden" name="sesskey" value="{$sesskey}" />
            </form>
        </p>
    </section>
