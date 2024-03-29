{include file="header.tpl"}

    <div class="page-header">
            <h1>Invite a new User to TWB Platform to be Assigned an Admin Role</h1>
    </div>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="{urlFor name='site-admin-dashboard'}">Close ×</a>
            <p>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        </div>
    {/if}

    {if isset($flash['info'])}
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert" href="{urlFor name='site-admin-dashboard'}">Close ×</a>
            <p><strong>{Localisation::getTranslation('common_note')}: </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        </div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <a class="close" data-dismiss="alert" href="{urlFor name='site-admin-dashboard'}">Close ×</a>
            <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
        </div>
    {/if}
<div class="row-fluid">
        
        <form method="post" action="{urlFor name='invite_site_admins'}" accept-charset="utf-8">
            <label for="role"><strong>Select Role</strong></label>
            <select name ="role">
                <option value= "{$COMMUNITY_OFFICER}">COMMUNITY OFFICER</option>
                <option value= "{$PROJECT_OFFICER}">PROJECT OFFICER</option>
             </select>

            <label for="email"><strong>{Localisation::getTranslation('common_email')}</strong></label>
            <input type="text" name="email" id="email"/>

            <div>
                <button type="submit" name="change-role" class="btn btn-primary">
                    <i class="icon-share icon-white"></i> Submit
                </button>
            </div>
        </form>
</div>
        

{include file="invitations.tpl"}

{include file="footer.tpl"}
