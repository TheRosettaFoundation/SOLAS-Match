
{assign var=task_id value=$task->getId()}

    {if isset($flash['error'])}
       <div class="container">
        <p class=" alert alert-warning alert-dismissible fade show mt-2">    
         
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </p>
        </div>
    {/if}

    <section class="container  mb-4 ">
    
    <div class="bg-body">

     <div class="d-flex justify-content-between flex-wrap ">

                <div class="d-flex flex-column justify-content-between flex-grow-1 p-4 >

                     <div class="flex-grow-1">

                        <h3 class="mb-4 fw-bold">{Localisation::getTranslation('task_claim_proofreading_0')}</h4>
                        
                        <ol>
                            <li>{Localisation::getTranslation('task_claim_proofreading_2')}</li>
                            <li>
                                {sprintf(Localisation::getTranslation('task_claim_proofreading_3'), {TemplateHelper::getLanguage($task->getTargetLocale())})}
                            </li>
                            {if empty($memsource_task)}
                            <li>
                                {sprintf(Localisation::getTranslation('task_claim_proofreading_6'), $projectFileDownload)}
                            </li>
                            {/if}
                            {if !empty($matecat_url)}
                            <li>
                                {sprintf(Localisation::getTranslation('task_claim_warning_kato'), {Localisation::getTranslation('task_claim_view_on_kato')}, {Localisation::getTranslation('common_download_file')}, {Localisation::getTranslation('task_claim_proofreading_5')})}
                            </li>
                            {/if}
                            {if !empty($memsource_task) || empty($allow_download)}
                            <li>
                                Also please note that you must wait for translation to be complete (100% translated) before starting revising.
                            </li>
                            {/if}
                        </ol>


                        <div>
                            {if !empty($memsource_task)}
                            <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-grayish fs-6">
                                <img src="{urlFor name='home'}ui/img/download.svg" alt="download-icon"  /></i> Download Original File in its source language</a>
                            {/if}
                        </div>
                     
                     
                     </div>

                       <div class="bg-grayish h-100 text-center d-inline-block ms-4 ">

                            <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="object-fit-cover" />
               
                        </div>     

              </div>

                             
     
     </div>

    </div>

    </section>

    </div>

  

###############
{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>Proofreading and Approval Task</small></h1>
        </div>
    </section>

    {if isset($flash['error'])}
        <div class="container">
        <p class=" alert alert-warning alert-dismissible fade show mt-2">  
         
         
        <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </p>
        </div>
    {/if}

    <section>
        <h2>Do you want to proofread this file?</h2>
        <p style="margin-bottom:20px;"></p>
        <ol>
            <li>Will you have enough time to proofread this file? Check how long the file is.</li>
            <li>
                {sprintf('Do you think you are capable of proofreading a file in <strong>%s?</strong>', {TemplateHelper::getLanguage($task->getTargetLocale())})}
            </li>
            {if empty($memsource_task)}
            <li>
                {sprintf(Localisation::getTranslation('task_claim_proofreading_6'), $projectFileDownload)}
            </li>
            {/if}
            {if !empty($matecat_url)}
            <li>
                {sprintf(Localisation::getTranslation('task_claim_warning_kato'), {Localisation::getTranslation('task_claim_view_on_kato')}, {Localisation::getTranslation('common_download_file')}, 'Yes, I promise I will proofread this file')}
            </li>
            {/if}
            {if !empty($memsource_task) || empty($allow_download)}
            <li>
                Also please note that you must wait for translation/revision to be complete (100% translated/revised) before starting proofreading.
            </li>
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
         <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-grayish fs-6">
            <i class="icon-download icon-white"></i> {Localisation::getTranslation('common_download_file')}</a>
        {/if}
        {if !empty($memsource_task)}
        <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-grayish fs-6">
            <i class="icon-download icon-white"></i> Download Original File in its source language</a>
        {/if}
        <h3>{Localisation::getTranslation('common_it_is_time_to_decide')}</h3>
        <p> 
                <button type="submit" class="btn btn-grayish fs-6">
                    <i class="icon-ok-circle icon-white"></i> Yes, I promise I will proofread this file
                </button>
                <a href="{urlFor name="task-view" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_the_task_page')}
                </a>
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        </p>
    </section>
