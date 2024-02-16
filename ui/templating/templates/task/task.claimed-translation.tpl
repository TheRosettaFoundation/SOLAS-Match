{assign var=task_id value=$task->getId()}

      
    <section>
            <h2 class="fw-bold">{Localisation::getTranslation('common_what_happens_now')} <small class="text-body fs-5">{Localisation::getTranslation('task_claimed_translation_we_need_your_translation')}</small></h2>
            <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}:</p>
            <ol>
                   {if $matecat_url != ''}
                <li>{if !empty($memsource_task)}Please take a look at our <a href="https://community.translatorswb.org/t/the-kato-translators-toolkit/3138" target="_blank">Translator’s Toolkit</a> before working on this task.{else}{Localisation::getTranslation('task_claimed_please_read_kato')}{/if}</li>
                <li>{sprintf(Localisation::getTranslation('task_claimed_translation_translate_the_file_to_plain'), {TemplateHelper::getLanguage($task->getTargetLocale())})}<br />
                    <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                    <i class="icon-th-list icon-white"></i> {if !empty($memsource_task)}Translate using Phrase TMS{else}{Localisation::getTranslation('task_claimed_translate_using_kato')}{/if}</a></li>
                {else}
                <li>{Localisation::getTranslation('common_can_you_open_file')}</li>
                <li>{sprintf(Localisation::getTranslation('task_claimed_translation_translate_the_file_to'), {TemplateHelper::getLanguage($task->getTargetLocale())})}</li>
                <li>{Localisation::getTranslation('task_claimed_translation_5')}</li>
               {/if}
            </ol>


        {if $matecat_url != ''}
                
                <div class="py-2">
                {sprintf(Localisation::getTranslation('task_claimed_translation_translate_the_file_to_plain'), {TemplateHelper::getLanguage($task->getTargetLocale())})}<br />
                    <a href="{$matecat_url}" class="btn btn-grayish" target="_blank">
                    <img src="{urlFor name='home'}ui/img/lang.svg" alt="phrase-lang-icon" class="mx-1" />  {if !empty($memsource_task)}Translate using Phrase TMS{else}{Localisation::getTranslation('task_claimed_translate_using_kato')}{/if}</a></div>    
        {/if}
        {if isset($user)}
            <div class=" btn btn-gray"> <img src="{urlFor name='home'}ui/img/info.svg" alt="user feedaback icon"  />  {sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</div>
        {/if}
    </section>


   

   
