{assign var=task_id value=$task->getId()}




  

    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <section class="py-4 container">

     <div class="card  py-2">

                <div class="card-body">


                        <h5>{Localisation::getTranslation('task_claim_proofreading_0')}</h5>
                        <p style="margin-bottom:20px;"></p>
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


                </div>


                <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="mx-1 img-thumbnail rounded float-end w-25 h-25" />


     
     
     </div>
        
    </section>

  
