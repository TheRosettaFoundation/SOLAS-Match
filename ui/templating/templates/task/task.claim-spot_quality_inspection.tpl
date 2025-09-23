
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

     <div class="d-md-flex justify-content-between ">

                <div class="d-flex flex-column justify-content-between  p-4 >

                     <div >

                        <h3 class="mb-4 fw-bold">Do you want to spot quality inspect this file?</h4>
                        
                        <ol>
                            <li>Will you have enough time to spot quality inspect this file? Check how long the file is.</li>
                            <li>
                                {sprintf('Do you think you are capable of spot quality inspecting a file in <strong>%s?</strong>', {TemplateHelper::getLanguage($task->getTargetLocale())})}
                            </li>
                            {if empty($memsource_task)}
                            <li>
                                {sprintf(Localisation::getTranslation('task_claim_proofreading_6'), $projectFileDownload)}
                            </li>
                            {/if}
                            {if !empty($matecat_url)}
                            <li>
                                {sprintf(Localisation::getTranslation('task_claim_warning_kato'), {Localisation::getTranslation('task_claim_view_on_kato')}, {Localisation::getTranslation('common_download_file')}, 'Yes, I promise I will spot quality inspect this file')}
                            </li>
                            {/if}
                            {if !empty($memsource_task) || empty($allow_download)}
                            <li>
                                Also please note that you must wait for translation/revision to be complete (100% translated/revised) before starting spot quality inspection.
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

                       <div class="bg-grayish h-100 text-center d-inline-block ms-4 flex-grow-1 ">

                            <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="object-fit-cover" />
               
                        </div>     

              </div>

                             
     
     </div>

    </div>

    </section>

    </div>

 