
<div id="accordionAdmins">
    <h3>All current Administrators of {$siteName}.</h3>
    <div name="adminList">
        <table class="table table-striped">
            <thead>
                <th>Display Name</th>
                <th>Revoke Administrator Rights</th>
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
                        <input type="submit" class='btn btn-inverse' name="revokeAdmin" value="    Revoke" 
                       onclick="return confirm('Are you sure you want to revoke this user\'s administrator privileges?')"/>
                    </form> 
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>

