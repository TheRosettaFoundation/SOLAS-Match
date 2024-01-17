{assign var=task_id value=$task->getId()}

 
    {if isset($flash['error'])}
       
        <div class="page-header">
            <h1>Proofreading and Approval task claimed <small>Please proofread it!</small></h1>
        </div>
        
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf('You have claimed the Proofreading and Approval task <strong>%s</strong>.', {TemplateHelper::uiCleanseHTML($task->getTitle())})}
        </p>
    {/if}

  <section class="container  ">


  <div class="bg-body">

     <div class="d-flex justify-content-between  flex-wrap">

                <div class="d-flex flex-column justify-content-between flex-grow-1 p-4 ">

                    <div class="flex-grow-1">

                        <h1>{Localisation::getTranslation('common_what_happens_now')}</h1>
                            {if $translations_not_all_complete}
                                <p>This is what you need to do:</p>
                                <ol>
                                    <li>Please take a look at our <a href="https://community.translatorswb.org/t/the-kato-translators-toolkit/3138" target="_blank">Translator’s Toolkit</a> before working on this task.</li>
                                    <li>You will be notified by email when the translations and revisions that this task depends on are complete.</li>
                                    <li>The email will contain a link to Phrase TMS, our translation tool, where you can proofread the task. You can also find the link in your Claimed Tasks page.</li>
                                    <li>Click on the link to start proofreading.</li>
                                </ol>
                            {else}
                                <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}:</p>
                                <ol>
                                        <li>Please take a look at our <a href="https://community.translatorswb.org/t/the-kato-translators-toolkit/3138" target="_blank">Translator’s Toolkit</a> before working on this task.</li>
                                        <li>{sprintf('Proofread the <strong>%s</strong> translation to ensure that it meets <a href="https://community.translatorswb.org/t/what-is-translation-quality-for-translators-without-borders/10295" target="_blank">quality standards</a>:', {TemplateHelper::getLanguage($task->getTargetLocale())})}<br />
                                            <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                                            <i class="icon-th-list icon-white"></i> {if !empty($memsource_task)}Proofread using Phrase TMS{else}{Localisation::getTranslation('task_claimed_proofread_using_kato')}{/if}</a></li>
                                </ol>
                            {/if} 

                            {if isset($user)}
                                <p>{sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</p>
                            {/if}           
                    </div>

                    <div>
                  

                        <button class="btn btn-grayish fs-6 ">  <img src="{urlFor name='home'}ui/img/download.svg" alt="download-icon" class="me-1" />  ({Localisation::getTranslation('common_cant_find_the_file_on_your_desktop')}
                        {sprintf('Download the <a href="%s">original file</a> in its source language and save it to your desktop.', {urlFor name="download-task" options="task_id.$task_id"})})
                         </button>
                    
                    
                    </div>

                    
                   

                </div>
       
                  <div class="bg-grayish h-100 text-center d-inline-block ms-4 ">

                        <img src="{urlFor name='home'}ui/img/translator.svg" alt="translator" class="mx-1 object-fit-cover" />

                  </div>

            </div>  

       </div>      

    </section>
</div>

 
