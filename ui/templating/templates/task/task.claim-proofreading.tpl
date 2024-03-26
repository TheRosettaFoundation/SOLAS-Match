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

     <div class=" d-block d-md-flex justify-content-between ">

                <div class="d-flex flex-column justify-content-between  p-4 >

                     <div>

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

  
