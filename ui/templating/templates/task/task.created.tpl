{include file="header.tpl"}

    <div class="page-header">
        <h1>{Localisation::getTranslation(Strings::TASK_CREATED_TASK_IS_NOW_LIVE)}</h1>
    </div>

    <div class="alert alert-success">
        <p>
            <strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}</strong> - {Localisation::getTranslation(Strings::TASK_CREATED_0)}
        </p>
        <p>
            {Localisation::getTranslation(Strings::TASK_CREATED_1)} <a href="{urlFor name="project-view" options="project_id.$project_id"}">{Localisation::getTranslation(Strings::COMMON_PROJECT_VIEW)}</a>.
        </p>
        <p>
            {Localisation::getTranslation(Strings::TASK_CREATED_5)} <a href="{urlFor name="task-view" options="task_id.$task_id"}">{Localisation::getTranslation(Strings::TASK_CREATED_TASK_VIEW)}</a>
        <p>        
    </div>

    <h1>{Localisation::getTranslation(Strings::COMMON_WHAT_HAPPENS_NOW)} <small>{Localisation::getTranslation(Strings::COMMON_WAIT_FOR_TRANSLATORS)}</small></h1>

    <p>{Localisation::getTranslation(Strings::COMMON_HERE_IS_WHAT_WILL_NOW_HAPPEN)}</p>
    <p style="margin-bottom:20px;"/>
    <ol>
            <li>{Localisation::getTranslation(Strings::TASK_CREATED_2)}</li>
            <li>{Localisation::getTranslation(Strings::TASK_CREATED_3)}</li>
            <li>{Localisation::getTranslation(Strings::TASK_CREATED_4)}</li>
    </ol>
    <p style="margin-bottom:20px;"/>

    <p>
        <a href="{urlFor name="home"}" class="btn btn-primary">
            <i class="icon-arrow-left icon-white"></i> {Localisation::getTranslation(Strings::COMMON_BACK_TO_HOME_PAGE)}
        </a>
        <a href="{urlFor name="task-create" options="project_id.$project_id"}" class="btn btn-success">
            <i class="icon-circle-arrow-up icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CREATE_NEW_TASK)}
        </a> 
    </p>

{include file="footer.tpl"}
