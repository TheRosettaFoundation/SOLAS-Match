{include file="header.tpl"}

    <div class="page-header">
            <h1>{Localisation::getTranslation('project_created_project_is_now_live')}</h1>
    </div>

    <div class="alert alert-success">
        <p>
            {Localisation::getTranslation('project_created_success')}
        </p>
        <p>
            {sprintf(Localisation::getTranslation('project_created_1'), {urlFor name="org-dashboard" options="org_id.$org_id"})}
        </p>
        <p>
            {sprintf(Localisation::getTranslation('project_created_2'), {urlFor name="project-view" options="project_id.$project_id"})}
        <p>        
    </div>

    <h1>{Localisation::getTranslation('common_what_happens_now')} <small>{Localisation::getTranslation('common_wait_for_translators')}</small></h1>

    <p>{Localisation::getTranslation('common_here_is_what_will_now_happen')}</p>
    <p style="margin-bottom:20px;"/>
    <ol>
        <li>{Localisation::getTranslation('project_created_4')}</li>
        <li>{Localisation::getTranslation('project_created_5')}</li>
        <li>{Localisation::getTranslation('common_this_may_take_days_weeks')}</li>
    </ol>
    <p style="margin-bottom:20px;"/>

    <p>
        <a href="{urlFor name="home"}" class="btn btn-primary">
            <i class="icon-arrow-left icon-white"></i> {Localisation::getTranslation('common_back_to_home_page')}
        </a>
        <a href="{urlFor name="project-create" options="org_id.$org_id"}" class="btn btn-success">
            <i class="icon-circle-arrow-up icon-white"></i> {Localisation::getTranslation('project_created_create_new_project')}
        </a> 
    </p>

{include file="footer.tpl"}
