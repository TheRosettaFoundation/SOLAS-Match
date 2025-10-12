{assign var=task_id value=$task->getId()}



    <section>
        <h2 class="fw-bold">{Localisation::getTranslation('common_what_happens_now')}</h2>
        {if $translations_not_all_complete}
        <p>This is what you need to do:</p>
        <ol>
           <li>Please take a look at our <a href="https://community.translatorswb.org/t/the-kato-translators-toolkit/3138" target="_blank">Translator’s Toolkit</a> before working on this task.</li>
            <li>You will be notified by email when the translations and revisions that this task depends on are complete.</li>
            <li>The email will contain a link to Phrase TMS, our translation tool, where you can spot quality inspect the task. You can also find the link in your My Tasks page.</li>
            <li>Click on the link to start spot quality inspection.</li>
        </ol>
       {else}
        <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}:</p>
        <ol>
               <li>Please take a look at our <a href="https://community.translatorswb.org/t/the-kato-translators-toolkit/3138" target="_blank">Translator’s Toolkit</a> before working on this task.</li>
                <li>{sprintf('Spot quality inspect the <strong>%s</strong> translation to ensure that it meets <a href="https://community.translatorswb.org/t/what-is-translation-quality-for-translators-without-borders/10295" target="_blank">quality standards</a>:', {TemplateHelper::getLanguage($task->getTargetLocale())})}<br />
                    <a href="{$matecat_url}" class="btn btn-grayish" target="_blank">
                    <i class="icon-th-list icon-white"></i> {if !empty($memsource_task)}Spot quality inspect using Phrase TMS{else}{Localisation::getTranslation('task_claimed_proofread_using_kato')}{/if}</a></li>
        </ol>
        {/if}

        {if isset($user)}
            <div class=" btn btn btn-gray"> <img src="{urlFor name='home'}ui/img/info.svg" alt="user feedaback icon" class="mx-1" /> {sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</div>
        {/if}
    </section>
     


   
