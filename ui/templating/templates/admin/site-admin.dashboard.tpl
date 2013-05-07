{include file='header.tpl'}

<div class="page-header">
    <h1>Administration Dashboard <small>The site administrator's dashboard.</small></h1>
</div>

<div class="well">
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.{$adminUserId}"}">
        <table style="width: 40%">
            <tr>
                <td colspan="2"> 
                    <label for="addAdmin"><h2>Create Administrator:</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Add new site administrator to <strong>{$siteName}</strong>. <br/><strong>Warning:</strong> Only add trusted users.
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="User e-mail address goes here." style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="addAdmin" value="1"><i class="icon-star icon-white"></i> Add Admin</button>
                </td>
            </tr>
        </table> 
    </form>
    
    {if !empty($adminList)}
        <hr/>
        {include file="admin/admin.site-admins-list.tpl" adminList=$adminList adminUserId=$adminUserId}
        <hr/>
    {/if}    
    
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
        <table>
            <tr>
                <td colspan="3"> 
                    <label for="banOrganisation"><h2>Ban Organisation:</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 20px">
                    Apply a site-wide ban to an organisation. <br/><strong>Note:</strong> All organisation members will be banned.
                </td>
            </tr>
            <tr style="font-weight: bold">
                <td >Organisation Name:</td>
                <td colspan="2"> Length of ban:</td>                
            </tr>
            <tr>
                <td width="40%">
                    <input type="text" name="orgName" placeholder="Organisation name goes here." style="width: 96%"/>
                </td>
                <td width="25%">
                    <select name="banTypeOrg" style="width: 96%">
                        <option value="{BanTypeEnum::DAY}">Day</option>
                        <option value="{BanTypeEnum::WEEK}">Week</option>
                        <option value="{BanTypeEnum::MONTH}">Month</option>
                        <option value="{BanTypeEnum::PERMANENT}">Permanent</option>
                    </select>
                </td>
                <td align="center" valign="top">
                    <button class="btn btn-danger" type="submit" name="banOrg" value="1"><i class="icon-exclamation-sign icon-white"></i> Ban Organisation</button>
                </td>
            </tr>
            <tr>
                <td colspan="3">                    
                    <label for='banReasonOrg'><strong>Reason for ban:</strong></label>
                    <textarea name='banReasonOrg' cols='40' rows='7' style="width: 99%"></textarea>
                </td>
            </tr>
        </table> 
    </form>
                
    {if !empty($bannedOrgList)}
        <hr/>
        {include file="admin/admin.banned-orgs-list.tpl" bannedOrgList=$bannedOrgList adminUserId=$adminUserId}
        <hr/>
    {/if}  
        
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
        <table>
            <tr>
                <td colspan="3"> 
                    <label for="banUser"><h2>Ban User:</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 20px">
                    Apply a site-wide ban to a user.
                </td>
            </tr>
            <tr style="font-weight: bold">
                <td >User E-Mail Address:</td>
                <td colspan="2"> Length of ban:</td>                
            </tr>
            <tr>
                <td width="40%">
                    <input type="text" name="userEmail" placeholder="User e-mail address goes here." style="width: 96%"/>
                </td>
                <td width="25%">
                    <select name="banTypeUser" style="width: 96%">
                        <option value="{BanTypeEnum::DAY}">Day</option>
                        <option value="{BanTypeEnum::WEEK}">Week</option>
                        <option value="{BanTypeEnum::MONTH}">Month</option>
                        <option value="{BanTypeEnum::PERMANENT}">Permanent</option>
                    </select>
                </td>
                <td align="center" valign="top">
                    <button class="btn btn-danger" type="submit" name="banUser" value="1"><i class="icon-exclamation-sign icon-white"></i> Ban User</button>
                </td>
            </tr>
            <tr>
                <td colspan="3">                    
                    <label for='banReasonUser'><strong>Reason for ban:</strong></label>
                    <textarea name='banReasonUser' cols='40' rows='7' style="width: 99%"></textarea>
                </td>
            </tr>
        </table> 
    </form>
                
    {if !empty($bannedUserList)}
        <hr/>
        {include file="admin/admin.banned-users-list.tpl" bannedUserList=$bannedUserList adminUserId=$adminUserId}
        <hr/>
    {/if} 
</div>

{include file='footer.tpl'}
