{include file="header.tpl"}

    <div class="page-header">
        <h1>{Localisation::getTranslation('task_created_task_is_now_live')}</h1>
    </div>

    <div class="alert alert-success">
        <p>
            <strong>{Localisation::getTranslation('common_success')}</strong> - {Localisation::getTranslation('task_created_0')}
        </p>
        <p>
            {sprintf(Localisation::getTranslation('task_created_1'), {urlFor name="project-view" options="project_id.$project_id"})}
        </p>
        <p>
            {sprintf(Localisation::getTranslation('task_created_5'), {urlFor name="task-view" options="task_id.$task_id"})}
        <p>        
    </div>

    <h1>{Localisation::getTranslation('common_what_happens_now')} <small>{Localisation::getTranslation('common_wait_for_translators')}</small></h1>

    <p>{Localisation::getTranslation('common_here_is_what_will_now_happen')}</p>
    <p style="margin-bottom:20px;"/>
    <ol>
            <li>{Localisation::getTranslation('task_created_2')}</li>
            <li>{Localisation::getTranslation('task_created_3')}</li>
            <li>{Localisation::getTranslation('task_created_4')}</li>
    </ol>
    <p style="margin-bottom:20px;"/>

    <p>
        <a href="{urlFor name="home"}" class="btn btn-primary">
            <i class="icon-arrow-left icon-white"></i> {Localisation::getTranslation('common_back_to_home_page')}
        </a>
        <a href="{urlFor name="task-create" options="project_id.$project_id"}" class="btn btn-success">
            <i class="icon-circle-arrow-up icon-white"></i> {Localisation::getTranslation('common_create_new_task')}
        </a> 
    </p>

{include file="footer.tpl"}
