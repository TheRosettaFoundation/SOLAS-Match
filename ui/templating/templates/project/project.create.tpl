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

        {if isset($error)}
            <div class="alert alert-error">
                    {$error}
            </div>
        {/if}
    </div>  
    <p style="margin-bottom:20px;"/>

    <div class="well">
        <div is="x-project-create-form" id="ProjectCreateForm" user-id="{$user_id}" org-id="{$org_id}"></div>

        <script src="{urlFor name="home"}ui/dart/deploy/web/packages/browser/dart.js"></script>
        <script src="{urlFor name="home"}ui/dart/deploy/web/packages/browser/interop.js"></script>
        <script type="application/dart" src="{urlFor name="home"}ui/dart/web/Routes/Projects/ProjectCreate.dart"></script>
    </div>
    <p style="margin-bottom:20px;"></p>
    
{include file="footer.tpl"}
