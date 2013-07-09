{include file="header.tpl"}

    <div class="page-header">
        <h1>{Localisation::getTranslation(Strings::TASK_UPLOADED_0)}</h1>
    </div>

    <div class="alert alert-success">
        <strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}</strong> {Localisation::getTranslation(Strings::TASK_UPLOADED_1)}
    </div>

    {include file="handle-flash-messages.tpl"}

    <section>
            <h1>{Localisation::getTranslation(Strings::COMMON_WHAT_HAPPENS_NOW)} <small>{Localisation::getTranslation(Strings::TASK_UPLOADED_2)}</small></h1>
            <p>{Localisation::getTranslation(Strings::COMMON_HERE_IS_WHAT_WILL_NOW_HAPPEN)}</p>
            <ol>
                <li>{Localisation::getTranslation(Strings::TASK_UPLOADED_YOUR_TASK)} <strong>{Localisation::getTranslation(Strings::TASK_UPLOADED_IS_COMPLETE)}</strong>.</li>
                <li>{Localisation::getTranslation(Strings::TASK_UPLOADED_3)}</li>
                <li>{$org_name} {Localisation::getTranslation(Strings::TASK_UPLOADED_4)} {Localisation::getTranslation(Strings::TASK_UPLOADED_THANK_YOU)}</li>
            </ol>
    </section>
    <section>    
        {if isset($tip)}
            <hr>
                <p><strong>{Localisation::getTranslation(Strings::TASK_UPLOADED_5)}</strong></p>
                <p>
                    <i>{$tip}</i>
                </p>
            <hr>
        {/if}
    </section>
    <section>
        <p>
            <a href="{urlFor name="home"}" class="btn btn-primary">
                <i class="icon-search icon-white"></i> {Localisation::getTranslation(Strings::TASK_UPLOADED_FIND_A_NEW_TASK)}
            </a>
        </p>
    </section>

{include file="footer.tpl"}
