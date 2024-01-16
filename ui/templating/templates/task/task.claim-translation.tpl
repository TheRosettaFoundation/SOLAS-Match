{assign var=task_id value=$task->getId()}

 
    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

  <section class=" container card ">

     <div class="d-flex justify-content-between">

                <div class="flex-grow-1 py-4">

                    <div > 

                         <h2>{Localisation::getTranslation('task_claim_translation_0')}</h2>
                   
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
       
                  <div class="bg-grayish h-100 text-center d-inline-block">

                        <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="mx-1 object-fit-fill" />

                  </div>

            </div>   

    </section>

 
