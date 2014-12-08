
<div id="accordionBannedOrgs">
    <h3>{sprintf(Localisation::getTranslation('admin_banned_orgs_list_all_banned_organisations_of'), $siteName)}</h3>
    <div name="bannedOrgList">
        <table class="table table-striped">
            <thead>
                <th>{Localisation::getTranslation('common_organisation_name')}</th>
                <th>{Localisation::getTranslation('common_banned_by')}</th> 
                <th>{Localisation::getTranslation('common_ban_duration')}</th>
                <th>{Localisation::getTranslation('common_ban_reason')}</th>
                <th>{Localisation::getTranslation('common_banned_date')}</th>
                <th>{Localisation::getTranslation('common_restore')}</th>
            </thead>
            {foreach $bannedOrgList as $bannedOrg}
            <tr>
                <td>                
                    <a href="{urlFor name="org-public-profile" options="org_id.{$bannedOrg->getOrgId()}"}">
                        {$bannedOrgNames[$bannedOrg->getOrgId()]}
                    </a>
                </td>
                <td>                
                    <a href="{urlFor name="user-public-profile" options="user_id.{$bannedOrg->getUserIdAdmin()}"}">
                        {$orgBannerAdminNames[$bannedOrg->getOrgId()]}
                    </a>
                </td>
                <td>                
                    {$bannedOrg->getBanType()}
                </td>
                <td>                
                    {$bannedOrg->getComment()}
                </td>
                <td>                
                    {date(Settings::get("ui.date_format"), strtotime($bannedOrg->getBannedDate()))}
                </td>
                <td>
                    <form method="post" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
                        <i class="icon-upload icon-white" style="position:relative; right:-25px; top:1px;"></i>
                        <input type="hidden" name="orgId" value="{$bannedOrg->getOrgId()}" />
                        <input type="submit" class='btn btn-primary' name="unBanOrg" value="    {Localisation::getTranslation('common_restore')}" 
                       onclick="return confirm('{Localisation::getTranslation('admin_banned_orgs_list_confirm_unban')}')"/>
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

