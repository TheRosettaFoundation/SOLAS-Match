{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{Localisation::getTranslation('task_claimed_desegmentation_desegmentation_task_claimed')} <small>{Localisation::getTranslation('task_claimed_desegmentation_0')}</small></h1>
        </div>
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation('common_success')}</strong> {sprintf(Localisation::getTranslation('task_claimed_desegmentation_1'), {$task->getTitle()})}
        </div>
    </section>

    <section>
        <h1>{Localisation::getTranslation('common_what_happens_now')}?</h1>
        <p>{Localisation::getTranslation('common_this_is_what_you_need_to_do_as_soon_as_possible')}</p>
        <ol>
            <li>{sprintf(Localisation::getTranslation('task_claimed_desegmentation_2'), {urlFor name="task" options="task_id.$task_id"})}</li>
            <li>{Localisation::getTranslation('task_claimed_desegmentation_3')}</li>
        </ol>
        {if isset($user)}
            <p>{sprintf(Localisation::getTranslation('common_we_have_also_emailed_you_these_instructions_to'), {$user->getEmail()})}</p>
        {/if}
    </section>

    <section>
        <h3>{Localisation::getTranslation('task_claimed_desegmentation_want_to_get_started')}</h3>
        <p></p>
        <p>
            <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-share-alt icon-white"></i> {Localisation::getTranslation('task_claimed_desegmentation_merge_files')}
            </a>
            <a href="{urlFor name="home"}" class="btn">
                <i class="icon-arrow-left icon-black"></i> {Localisation::getTranslation('task_claimed_desegmentation_5')}
            </a>
        </p>
    </section>
