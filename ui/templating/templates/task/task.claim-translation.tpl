{assign var=task_id value=$task->getId()}

 
    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

  <section class=" container card bg-grayish ">

     <div class="d-flex  py-3 justify-content-between align-items-center flex-wrap ">

                <div class="w-50 d-flex flex-column justify-content-end py-2">

                    <div> 

                         <h5>{Localisation::getTranslation('task_claim_translation_0')}</h5>
                   
                        <ul>
                            <li>{Localisation::getTranslation('task_claim_translation_2')}</li>
                            <li>{sprintf(Localisation::getTranslation('task_claim_translation_3'), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
                            {if !empty($matecat_url)}
                            <li>{sprintf(Localisation::getTranslation('task_claim_warning_kato'), {Localisation::getTranslation('task_claim_view_on_kato')}, {Localisation::getTranslation('common_download_file')}, {Localisation::getTranslation('task_claim_translation_5')})}</li>
                            {/if}
                        </ul>
                    
                    
                    </div>

                    <div>
                      
                      {if !empty($memsource_task)}
                        <button class="btn btn-grayish fs-6"> Download original file in its source language </button>
                      {/if}
                    
                    
                    </div>

                    
                   

                </div>
       
                  <div class="bg-grayish  text-center">

                        <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="mx-1 rounded  w-75" />

                  </div>

            </div>   

    </section>

 
