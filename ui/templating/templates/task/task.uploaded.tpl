{include file="header.tpl"}

    <div class="page-header">
        <h1>{Localisation::getTranslation('task_uploaded_0')}</h1>
    </div>

    <div class="alert alert-success">
        <strong>{Localisation::getTranslation('common_success')}</strong> {Localisation::getTranslation('task_uploaded_1')}
    </div>

    {include file="handle-flash-messages.tpl"}

    <section>
            <h1>{Localisation::getTranslation('common_what_happens_now')} <small>{Localisation::getTranslation('task_uploaded_2')}</small></h1>
            <p>{Localisation::getTranslation('common_here_is_what_will_now_happen')}</p>
            <ol>
                <li>{Localisation::getTranslation('task_uploaded_is_complete')}</li>
                <li>{Localisation::getTranslation('task_uploaded_3')}</li>
                <li>{sprintf(Localisation::getTranslation('task_uploaded_4'), {$org_name})}</li>
            </ol>
    </section>
    <section>    
        {if isset($tip)}
            <hr>
                <p><strong>{Localisation::getTranslation('task_uploaded_5')}</strong></p>
                <p>
                    <i>{$tip}</i>
                </p>
            <hr>
        {/if}
    </section>
    <section>
        <p>
            <a href="{urlFor name="home"}" class="btn btn-primary">
                <i class="icon-search icon-white"></i> {Localisation::getTranslation('task_uploaded_find_a_new_task')}
            </a>
        </p>
    </section>

{include file="footer.tpl"}
