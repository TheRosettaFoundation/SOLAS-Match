{include file='header.tpl'}

<div class="page-header">
    <h1>{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_ADMINISTRATION_DASHBOARD)} <small>{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_THE_SITE_ADMINISTRATORS_DASHBOARD)}.</small></h1>
</div>

<div class="well">
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.{$adminUserId}"}">
        <table style="width: 40%">
            <tr>
                <td colspan="2"> 
                    <label for="addAdmin"><h2>{Localisation::getTranslation(Strings::COMMON_CREATE_ADMINISTRATOR)}:</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_0)} <strong>{$siteName}</strong>. <br/><strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}:</strong> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_ONLY_ADD_TRUSTED_USERS)}.
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_1)}." style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="addAdmin" value="1"><i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_ADD_ADMIN)}</button>
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
                    <label for="banOrganisation"><h2>{Localisation::getTranslation(Strings::COMMON_BAN_ORGANISATION)}:</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 20px">
                    {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_2)}. <br/><strong>{Localisation::getTranslation(Strings::COMMON_NOTE)}:</strong> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_3)}.
                </td>
            </tr>
            <tr style="font-weight: bold">
                <td>{Localisation::getTranslation(Strings::COMMON_ORGANISATION_NAME)}:</td>
                <td colspan="2"> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_BAN_DURATION)}:</td>                
            </tr>
            <tr>
                <td width="40%">
                    <input type="text" name="orgName" placeholder="{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_ORGANISATION_NAME_GOES_HERE)}." style="width: 96%"/>
                </td>
                <td width="25%">
                    <select name="banTypeOrg" style="width: 96%">
                        <option value="{BanTypeEnum::DAY}">{Localisation::getTranslation(Strings::COMMON_DAY)}</option>
                        <option value="{BanTypeEnum::WEEK}">{Localisation::getTranslation(Strings::COMMON_WEEK)}</option>
                        <option value="{BanTypeEnum::MONTH}">{Localisation::getTranslation(Strings::COMMON_MONTH)}</option>
                        <option value="{BanTypeEnum::PERMANENT}">{Localisation::getTranslation(Strings::COMMON_PERMANENT)}</option>
                    </select>
                </td>
                <td align="center" valign="top">
                    <button class="btn btn-danger" type="submit" name="banOrg" value="1"><i class="icon-exclamation-sign icon-white"></i> {Localisation::getTranslation(Strings::COMMON_BAN_ORGANISATION)}</button>
                </td>
            </tr>
            <tr>
                <td colspan="3">                    
                    <label for='banReasonOrg'><strong>{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_BAN_REASON)}:</strong></label>
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
                    <label for="banUser"><h2>{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_BAN_USER)}:</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 20px">
                    {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_4)}
                </td>
            </tr>
            <tr style="font-weight: bold">
                <td >{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_USER_EMAIL_ADDRESS)}:</td>
                <td colspan="2"> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_BAN_DURATION)}:</td>                
            </tr>
            <tr>
                <td width="40%">
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_1)}." style="width: 96%"/>
                </td>
                <td width="25%">
                    <select name="banTypeUser" style="width: 96%">
                        <option value="{BanTypeEnum::DAY}">{Localisation::getTranslation(Strings::COMMON_DAY)}</option>
                        <option value="{BanTypeEnum::WEEK}">{Localisation::getTranslation(Strings::COMMON_WEEK)}</option>
                        <option value="{BanTypeEnum::MONTH}">{Localisation::getTranslation(Strings::COMMON_MONTH)}</option>
                        <option value="{BanTypeEnum::PERMANENT}">{Localisation::getTranslation(Strings::COMMON_PERMANENT)}</option>
                    </select>
                </td>
                <td align="center" valign="top">
                    <button class="btn btn-danger" type="submit" name="banUser" value="1"><i class="icon-exclamation-sign icon-white"></i> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_BAN_USER)}</button>
                </td>
            </tr>
            <tr>
                <td colspan="3">                    
                    <label for='banReasonUser'><strong>{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_BAN_REASON)}:</strong></label>
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
    
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.{$adminUserId}"}">
        <table style="width: 40%">
            <tr>
                <td colspan="2"> 
                    <label for="deleteUser"><h2>{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_DELETE_USER)}:</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_PERMANENTLY)}</strong> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_DELETE_A_USER_FROM)} <strong>{$siteName}</strong>.
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_1)}." style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-inverse" type="submit" name="deleteUser" value="1" onclick="return confirm('{Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_5)}.')"/>
                        <i class="icon-fire icon-white"></i> {Localisation::getTranslation(Strings::SITE_ADMIN_DASHBOARD_DELETE_USER)}
                    </button>
                </td>
            </tr>
        </table> 
    </form>
</div>

{include file='footer.tpl'}
