
<div id="accordionBannedOrgs">
    <h3>All banned Organisations of {$siteName}.</h3>
    <div name="bannedOrgList">
        <table class="table table-striped">
            <thead>
                <th>Organisation Name</th>
                <th>Banned By</th> 
                <th>Ban Duration</th>
                <th>Ban Reason</th>
                <th>Banned Date</th>
                <th>Restore</th>
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
                        <input type="submit" class='btn btn-primary' name="unBanOrg" value="    Restore" 
                       onclick="return confirm('Are you sure you want to unban this Organisation and its members?')"/>
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

