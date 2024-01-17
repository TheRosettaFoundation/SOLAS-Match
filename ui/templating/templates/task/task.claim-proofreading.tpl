{assign var=task_id value=$task->getId()}

    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <section class="container  mb-4 ">

     <div class="d-flex justify-content-between flex-wrap ">

                <div class="flex-grow-1 py-4">

                     <div>

                        <h4 class="mb-2">{Localisation::getTranslation('task_claim_proofreading_0')}</h4>
                        
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


                        {if !empty($matecat_url)}
                            <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                                <i class="icon-th-list icon-white"></i> {Localisation::getTranslation('task_claim_view_on_kato')}
                            </a>
                        {/if}

                        {if !empty($allow_download)}
                        <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
                            <i class="icon-download icon-white"></i> {Localisation::getTranslation('common_download_file')}</a>
                        {/if}
                        {if !empty($memsource_task)}
                        <a href="{urlFor name="download-task" options="task_id.$task_id"}" class=" btn btn-primary">
                            <img src="{urlFor name='home'}ui/img/download.svg" alt="download-icon"  /></i> Download Original File in its source language</a>
                        {/if}
                     
                     
                     </div>

                       <div class="bg-grayish h-100 text-center d-inline-block ">

                            <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="class="object-fit-fill"" />
               
                        </div>     

              </div>

                             
     
     </div>
        
    </section>

    </div>

  
