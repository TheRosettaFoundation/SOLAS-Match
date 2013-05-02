
<div id="accordionBannedUsers">
    <h3>All banned Users of {$siteName}.</h3>
    <div name="bannedOrgList">
        <table class="table table-striped">
            <thead>
                <th>User Display Name</th>
                <th>Banned By</th>
                <th>Ban Duration</th>
                <th>Ban Reason</th>
                <th>Banned Date</th>
                <th>Restore</th>
            </thead>
            {foreach $bannedUserList as $bannedUser}
            <tr>
                <td>                
                    <a href="{urlFor name="user-public-profile" options="user_id.{$bannedUser->getUserId()}"}">{$bannedUser['user']->getDisplayName()}</a>
                </td>
                <td>                
                    <a href="{urlFor name="user-public-profile" options="user_id.{$bannedUser->getUserIdAdmin()}"}">{$bannedUser['adminUser']->getDisplayName()}</a>
                </td>
                <td>                
                    {$bannedUser->getBanType()}
                </td>
                <td>                
                    {$bannedUser->getComment()}
                </td>
                <td>                
                    {date(Settings::get("ui.date_format"), strtotime($bannedUser->getBannedDate()))}
                </td>
                <td>
                    <form method="post" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
                        <i class="icon-upload icon-white" style="position:relative; right:-25px; top:1px;"></i>
                        <input type="hidden" name="userId" value="{$bannedUser->getUserId()}" />
                        <input type="submit" class='btn btn-primary' name="unBanUser" value="    Restore" 
                       onclick="return confirm('Are you sure you want to unban this User?')"/>
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

