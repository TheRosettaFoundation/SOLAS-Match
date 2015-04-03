{include file="header.tpl"}

    <div class="grid_8">
        <div class="page-header">
            <h1>
                {Localisation::getTranslation('project_create_create_a_project')} <small>{Localisation::getTranslation('project_create_0')}</small><br>   
                <small>
                    {Localisation::getTranslation('common_denotes_a_required_field')}
                </small>
            </h1>
        </div>           
    </div>  

    <project-create-form userid="{$user_id}" orgid="{$org_id}" maxfilesize="{$maxFileSize}">
    </project-create-form>
    
{include file="footer.tpl"}
