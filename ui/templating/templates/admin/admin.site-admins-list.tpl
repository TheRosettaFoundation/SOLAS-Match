
<div id="accordionAdmins">
    <h3>{Localisation::getTranslation(Strings::ADMIN_SITE_ADMINS_LIST_ALL_CURRENT_ADMINISTRATORS_OF)} {$siteName}.</h3>
    <div name="adminList">
        <table class="table table-striped">
            <thead>
                <th>{Localisation::getTranslation(Strings::ADMIN_SITE_ADMINS_LIST_DISPLAY_NAME)}</th>
                <th>{Localisation::getTranslation(Strings::ADMIN_SITE_ADMINS_LIST_REVOKE_ADMINISTRATOR_RIGHTS)}</th>
            </thead>
            {foreach $adminList as $admin}
            <tr>
                <td>                
                    <a href="{urlFor name="user-public-profile" options="user_id.{$admin->getId()}"}">{$admin->getDisplayName()}</a>
                </td>
                <td>
                    <form method="post" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
                        <i class="icon-fire icon-white" style="position:relative; right:-25px; top:1px;"></i>
                        <input type="hidden" name="userId" value="{$admin->getId()}" />
                        <input type="submit" class='btn btn-inverse' name="revokeAdmin" value="    {Localisation::getTranslation(Strings::ADMIN_SITE_ADMINS_LIST_REVOKE)}" 
                       onclick="return confirm('{Localisation::getTranslation(Strings::ADMIN_SITE_ADMINS_LIST_0)}')"/>
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

