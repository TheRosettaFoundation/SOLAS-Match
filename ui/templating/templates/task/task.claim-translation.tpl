{assign var=task_id value=$task->getId()}

 
    {if isset($flash['error'])}
      <div class="container">
        <p class=" alert alert-warning alert-dismissible fade show mt-2">
        
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          
        </p>
      </dib>
    {/if}

  <section class="container  ">

  <div class="bg-body">
  
     <div class="d-flex justify-content-between  flex-wrap">

                <div class="d-flex flex-column justify-content-between flex-grow-1 p-4 ">

                    <div class="flex-grow-1 " > 

                         <h3 class="mb-4 fw-bold">{Localisation::getTranslation('task_claim_translation_0')}</h3>
                   
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
                        <a href="{urlFor name="download-task" options="task_id.$task_id"}"  class="btn btn-grayish fs-6 ">
                        <img src="{urlFor name='home'}ui/img/download.svg" alt="download-icon" class="me-1" /> Download original file in its source language </a>
                      {/if}
                    
                    
                    </div>

                    
                   

                </div>
       
                  <div class="bg-grayish h-100 text-center d-inline-block ms-4 ">

                        <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="mx-1 object-fit-cover" />

                  </div>

            </div>  

       </div>      

    </section>
</div>

 
