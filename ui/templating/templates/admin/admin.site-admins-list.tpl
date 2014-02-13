
<div id="accordionAdmins">
    <h3>{sprintf(Localisation::getTranslation('admin_site_admins_list_all_current_administrators_of'), $siteName)}</h3>
    <div name="adminList">
        <table class="table table-striped">
            <thead>
                <th>{Localisation::getTranslation('admin_site_admins_list_display_name')}</th>
                <th>{Localisation::getTranslation('admin_site_admins_list_revoke_administrator_rights')}</th>
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
                        <input type="submit" class='btn btn-inverse' name="revokeAdmin" value="    {Localisation::getTranslation('admin_site_admins_list_revoke')}" 
                       onclick="return confirm('{Localisation::getTranslation('admin_site_admins_list_0')}')"/>
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

