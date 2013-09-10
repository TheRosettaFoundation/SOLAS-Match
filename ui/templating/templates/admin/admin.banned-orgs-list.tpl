
<div id="accordionBannedOrgs">
    <h3>{sprintf(Localisation::getTranslation(Strings::ADMIN_BANNED_ORGS_LIST_ALL_BANNED_ORGANISATIONS_OF), Settings::get("site.name"))}</h3>
    <div name="bannedOrgList">
        <table class="table table-striped">
            <thead>
                <th>{Localisation::getTranslation(Strings::COMMON_ORGANISATION_NAME)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_BANNED_BY)}</th> 
                <th>{Localisation::getTranslation(Strings::COMMON_BAN_DURATION)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_BAN_REASON)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_BANNED_DATE)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_RESTORE)}</th>
            </thead>
            {foreach $bannedOrgList as $bannedOrg}
            <tr>
                <td>                
                    <a href="{urlFor name="org-public-profile" options="org_id.{$bannedOrg->getOrgId()}"}">{$bannedOrg['org']->getName()}</a>
                </td>
                <td>                
                    <a href="{urlFor name="user-public-profile" options="user_id.{$bannedOrg->getUserIdAdmin()}"}">{$bannedOrg['adminUser']->getDisplayName()}</a>
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
                        <input type="submit" class='btn btn-primary' name="unBanOrg" value="    {Localisation::getTranslation(Strings::COMMON_RESTORE)}" 
                       onclick="return confirm('{Localisation::getTranslation(Strings::ADMIN_BANNED_ORGS_LIST_0)}')"/>
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

