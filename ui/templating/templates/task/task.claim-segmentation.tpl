{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{$task->getTitle()} <small>{Localisation::getTranslation(Strings::COMMON_SEGMENTATION_TASK)}</small></h1>
        </div>
    </section>

    <section>
        <h2>{Localisation::getTranslation(Strings::TASK_CLAIM_SEGMENTATION_0)} <small>{Localisation::getTranslation(Strings::COMMON_AFTER_DOWNLOADING)}</small></h2>
        <hr />
        <h3>{Localisation::getTranslation(Strings::COMMON_REVIEW_THIS_CHECKLIST_FOR_YOUR_DOWNLOADED_FILE)} <small>{Localisation::getTranslation(Strings::TASK_CLAIM_SEGMENTATION_1)}</small></h3>
        <p style="margin-bottom:20px;"></p>
        <ol>
            <li>{Localisation::getTranslation(Strings::COMMON_CAN_YOU_OPEN_FILE)}</li>
            <li>{Localisation::getTranslation(Strings::TASK_CLAIM_SEGMENTATION_5)}</li>
        </ol>
    </section>

    <section>
         <h3>{Localisation::getTranslation(Strings::COMMON_IT_IS_TIME_TO_DECIDE)}</h3>
         <p> 
             {Localisation::getTranslation(Strings::TASK_CLAIM_SEGMENTATION_2)} {Localisation::getTranslation(Strings::TASK_CLAIM_SEGMENTATION_3)}
         </p>
         <p> 
            <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                 <button type="submit" class="btn btn-primary">
                     <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation(Strings::TASK_CLAIM_SEGMENTATION_4)}
                 </button>
                 <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                     <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation(Strings::COMMON_NO_JUST_BRING_ME_BACK_TO_THE_TASK_PAGE)}
                 </a>
            </form>
        </p>
    </section>

    <iframe src="{urlFor name="download-task" options="task_id.$task_id"}" width="1" height="1" frameborder="no"></iframe>
