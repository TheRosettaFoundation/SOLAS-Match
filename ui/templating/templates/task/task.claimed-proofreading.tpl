{assign var=task_id value=$task->getId()}



    <section>
        <h2 class="fw-bold">{Localisation::getTranslation('common_what_happens_now')}</h2>
        {if $translations_not_all_complete}
        <p>This is what you need to do:</p>
        <ol>
            <li>Please take a look at our <a href="https://community.translatorswb.org/t/the-kato-translators-toolkit/3138" target="_blank">Translator’s Toolkit</a> before working on this task.</li>
            <li>You will be notified by email when the translations that this task depends on are complete.</li>
            <li>The email will contain a link to Phrase TMS, our translation tool, where you can revise the task. You can also find the link in your Claimed Tasks page.</li>
            <li>Click on the link to start proofreading.</li>
        </ol>
       {else}
        <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}:</p>
        <ol>
               <li>Please take a look at our <a href="https://community.translatorswb.org/t/the-kato-translators-toolkit/3138" target="_blank">Translator’s Toolkit</a> before working on this task.</li>
                <li>{sprintf('Proofread the <strong>%s</strong> translation to ensure that it meets <a href="https://community.translatorswb.org/t/what-is-translation-quality-for-translators-without-borders/10295" target="_blank">quality standards</a>:', {TemplateHelper::getLanguage($task->getTargetLocale())})}<br />
                    <a href="{$matecat_url}" class="btn btn-primary" target="_blank">
                    {if !empty($memsource_task)}Proofread using Phrase TMS{else}{Localisation::getTranslation('task_claimed_proofread_using_kato')}{/if}</a></li>
        </ol>
        {/if}
            <section>
        <h3>When you have finished proofreading:</h3>
        <p>
            {if isset($user)}
            <a href="{urlFor name="claimed-tasks" options="user_id.{$user->getId()}"}" class="btn">
            {else}
            <a href="{urlFor name="home"}" class="btn">
            {/if}
                <i class="icon-arrow-left icon-black"></i> {Localisation::getTranslation('common_no_just_bring_me_back_to_claimed_tasks')}
            </a>
            {if $isSiteAdmin}
            <a href="{urlFor name="project-view" options="project_id.{$task->getProjectId()}"}" class="btn">
                <i class="icon-arrow-left icon-black"></i> Just bring me back to the project page.
            </a>
            {/if}
        </p>
    </section>
        {if isset($user)}
            <div class=" btn btn btn-gray"> <img src="{urlFor name='home'}ui/img/info.svg" alt="user feedaback icon" class="mx-1" /> {sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</div>
        {/if}
    </section>

   
