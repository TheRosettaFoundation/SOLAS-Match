{include file="header.tpl"}

    <div class="page-header">
            <h1>User Management for {$orgName}</h1>
    </div>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="{urlFor name="org-public-profile" options="org_id.$org_id"}">Close ×</a>
            <p>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        </div>
    {/if}

    {if isset($flash['info'])}
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert" href="{urlFor name="org-public-profile" options="org_id.$org_id"}">Close ×</a>
            <p><strong>{Localisation::getTranslation('common_note')}: </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        </div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <a class="close" data-dismiss="alert" href="{urlFor name="org-public-profile" options="org_id.$org_id"}">Close ×</a>
            <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
        </div>
    {/if}
<div class="row-fluid">
    <div class="span4">
        <form method="post" action="{urlFor name="invite_admins" options="org_id.$org_id"}" accept-charset="utf-8">
            <label for="role"><strong>Select Role</strong></label>
            <select name ="role" style="width: 300px">
                <option value= "{$NGO_LINGUIST + $LINGUIST}">LINGUIST (for this organization and all others)</option>
                <option value= "{$NGO_LINGUIST}">LINGUIST (for this organization only)</option>
                <option value= "{$NGO_PROJECT_OFFICER}">PROJECT OFFICER</option>
               {if $roles&($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                 <option value= "{$NGO_ADMIN}">ADMIN</option>
               {/if}
             </select>

            <label for="email"><strong>{Localisation::getTranslation('common_email')}</strong></label>
            <input type="text" name="email" id="email" style="width: 294px" />
            
            <div>
                <button type="submit" name="change-role" class="btn btn-primary">
                    <i class="icon-share icon-white"></i> Submit
                </button>
            </div>
        </form>
    </div>
    <div class="span2">
        Select a role and introduce the account email.<br />
        Existing users will be given the new role, new users will be invited to join the platform.
        <a href="https://communitylibrary.translatorswb.org/books/12-self-managed-partners/page/twb-platform" target="_blank">Read more here</a>
    </div>
</div>

{include file="invitations.tpl"}        
     
{include file="footer.tpl"}
