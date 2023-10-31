
<div id="accordionAdmins">
    <h3>{sprintf(Localisation::getTranslation('admin_site_admins_list_all_current_administrators_of'), $siteName)}</h3>
    <div name="adminList">
        <table class="table table-striped">
            <thead>
                <th>email</th>
                <th>Roles</th>
                <th>Revoke All Administrator Rights</th>
            </thead>
            {foreach $adminList as $admin}
            <tr>
                <td>                
                    <a href="{urlFor name="user-public-profile" options="user_id.{$admin['id']}"}">{TemplateHelper::uiCleanseHTML($admin['email'])}</a>
                </td>
                <td>
                    {if $admin['roles']&$SITE_ADMIN}TWB ADMIN{if $admin['roles']&($PROJECT_OFFICER + $COMMUNITY_OFFICER)},{/if}{/if} {if $admin['roles']&$PROJECT_OFFICER}PROJECT OFFICER{if $admin['roles']&$COMMUNITY_OFFICER},{/if}{/if} {if $admin['roles']&$COMMUNITY_OFFICER}COMMUNITY OFFICER{/if}
                </td>
                <td>
                    <form method="post" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
                        <i class="icon-fire icon-white" style="position:relative; right:-25px; top:1px;"></i>
                        <input type="hidden" name="userId" value="{$admin['id']}" />
                        <input type="submit" class='btn btn-inverse' name="revokeAdmin" value="    {Localisation::getTranslation('admin_site_admins_list_revoke')}" 
                       onclick="return confirm('{Localisation::getTranslation('admin_site_admins_list_confirm_revoke')}')"/>
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

