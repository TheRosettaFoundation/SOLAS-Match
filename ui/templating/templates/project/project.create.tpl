{include file="header.tpl"}

    <div class="grid_8">
        <div class="page-header">
            <h1>
                {Localisation::getTranslation(Strings::PROJECT_CREATE_CREATE_A_PROJECT)} <small>{Localisation::getTranslation(Strings::PROJECT_CREATE_0)}.</small><br>   
                <small>
                    {Localisation::getTranslation(Strings::COMMON_NOTE)}:
                    <span style="color: red">*</span>
                    {Localisation::getTranslation(Strings::COMMON_DENOTES_A_REQUIRED_FIELD)}.
                </small>
            </h1>
        </div>           
    </div>  
    <p style="margin-bottom:20px;"/>

    <project-create-form userid="{$user_id}" orgid="{$org_id}" maxfilesize="{$maxFileSize}">
    </project-create-form>
    <p style="margin-bottom:20px;"></p>
    
{include file="footer.tpl"}
