{include file='header.tpl'}

<div class="page-header">
    <h1>
        {Localisation::getTranslation('site_admin_dashboard_administration_dashboard')}
        <small>
            {Localisation::getTranslation('site_admin_dashboard_the_site_administrators_dashboard')}
        </small>
     </h1>
</div>

<div class="well">
    <p><a href="{urlFor name="active_now"}" target="_blank">List all tasks currently in progress showing information about the volunteer working on them.</a></p>
    <p><a href="{urlFor name="unclaimed_tasks"}" target="_blank">List all tasks still unclaimed and the email address of their creators.</a></p>
    <p><a href="{urlFor name="active_users"}" target="_blank">List all volunteers who have ever claimed a task (and which task is still on the system and not archived), the corresponding task and the task creator's email address.</a> <a href="{urlFor name="download_active_users"}">[Download]</a></p>
    <p><a href="{urlFor name="active_users_unique"}" target="_blank">List all volunteers who have ever claimed a task (and which task is still on the system and not archived) showing their email address and Display Name.</a></p>
    <p><a href="{urlFor name="all_users"}" target="_blank">List all users currently registered on Trommons showing information about them.</a> <a href="{urlFor name="download_all_users"}">[Download]</a></p>
    <p><a href="{urlFor name="all_users_plain"}" target="_blank">(Same as above but plain layout.)</a></p>
    <p><a href="{urlFor name="user_languages" options="code.full"}" target="_blank">List all languages indicated on user's profiles showing information about the user (if you replace the "full" in the URL with a language code like "es", you will just get that language).</a> <a href="{urlFor name="download_user_languages"}">[Download]</a></p>
    <p><a href="{urlFor name="community_stats"}">[Download community report]</a></p>
    <hr />

    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"}" accept-charset="utf-8">
        {if isset($flash['search_user_fail'])}
            <p class="alert alert-error">{$flash['search_user_fail']}</p>
        {/if}
        {if isset($flash['search_user_results'])}
            <table class="alert alert-success">
                {foreach $flash['search_user_results'] as $item}
                    <tr>
                        <td><a href="mailto:{$item['email']}">{$item['email']}</a></td>
                        <td><a href="{urlFor name="user-public-profile" options="user_id.{$item['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($item['name'])}</a></td>
                    </tr>
                {/foreach}
                {if count($flash['search_user_results']) == 20}<tr><td>Only 20 shown.</td></tr>{/if}
            </table>
        {/if}

        <table style="width: 40%">
            <tr>
                <td>
                    <input type="text" name="search_user" placeholder="User name or e-mail." style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="search_user_submit" value="1">
                        <i class="icon-search icon-white"></i>
                        Search User
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
    <hr />

    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"}" accept-charset="utf-8">
        {if isset($flash['search_organisation_fail'])}
            <p class="alert alert-error">{$flash['search_organisation_fail']}</p>
        {/if}
        {if isset($flash['search_organisation_results'])}
            <table class="alert alert-success">
                {foreach $flash['search_organisation_results'] as $item}
                    <tr>
                        <td><a href="{urlFor name="org-public-profile" options="org_id.{$item['org_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($item['name'])}</a></td>
                        <td>{if $item['email'] != ''}<a href="mailto:{$item['email']}">{$item['email']}</a>{/if}</td>
                    </tr>
                {/foreach}
                {if count($flash['search_organisation_results']) == 20}<tr><td>Only 20 shown.</td></tr>{/if}
            </table>
        {/if}

        <table style="width: 40%">
            <tr>
                <td>
                    <input type="text" name="search_organisation" placeholder="Organisation name or e-mail." style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="search_organisation_submit" value="1">
                        <i class="icon-search icon-white"></i>
                        Search Organisation
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
    <hr />

    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table style="width: 40%">
            <tr>
                <td colspan="2">
                    <label for="verify"><h2>{Localisation::getTranslation('email_verification_email_verification')}</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>{sprintf(Localisation::getTranslation('email_verification_email_verification'), $siteName)}</p>
                </td>
            </tr>
            {if isset($flash['verifyError'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-error">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['verifyError'])}</p>
                    </td>
                </tr>
            {/if}
            {if isset($flash['verifySuccess'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-success">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['verifySuccess'])}</p>
                    </td>
                </tr>
            {/if}
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation('site_admin_dashboard_email_here')}" style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="verify" value="1">
                        <i class="icon-star icon-white"></i>
                        {Localisation::getTranslation('email_verification_email_verification')}
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
    <hr />

    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table style="width: 40%">
            <tr>
                <td colspan="2"> 
                    <label for="addAdmin"><h2>{Localisation::getTranslation('common_create_administrator')}</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>{sprintf(Localisation::getTranslation('site_admin_dashboard_add_new_admin'), $siteName)}</p>
                    <p><strong>{Localisation::getTranslation('site_admin_dashboard_only_add_trusted')}</strong></p>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation('site_admin_dashboard_email_here')}" style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="addAdmin" value="1">
                        <i class="icon-star icon-white"></i>
                        {Localisation::getTranslation('site_admin_dashboard_add_admin')}
                    </button>
                </td>
            </tr>
        </table> 
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
    
    {if !empty($adminList)}
        <hr/>
        {include file="admin/admin.site-admins-list.tpl" adminList=$adminList adminUserId=$adminUserId}
        <hr/>
    {/if}    
    
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard" options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table>
            <tr>
                <td colspan="3"> 
                    <label for="banOrganisation"><h2>{Localisation::getTranslation('common_ban_organisation')}</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 20px">
                    <p>{Localisation::getTranslation('site_admin_dashboard_sitewide_org_ban')}</p>
                    <p><strong>{Localisation::getTranslation('site_admin_dashboard_will_ban_all_members')}</strong></p>
                </td>
            </tr>
            <tr style="font-weight: bold">
                <td>{Localisation::getTranslation('common_organisation_name')}</td>
                <td colspan="2"> {Localisation::getTranslation('site_admin_dashboard_ban_duration')}</td>                
            </tr>
            <tr>
                <td width="40%">
                    <input type="text" name="orgName" placeholder="{Localisation::getTranslation('site_admin_dashboard_organisation_name_goes_here')}" style="width: 96%"/>
                </td>
                <td width="25%">
                    <select name="banTypeOrg" style="width: 96%">
                        <option value="{BanTypeEnum::DAY}">{Localisation::getTranslation('common_day')}</option>
                        <option value="{BanTypeEnum::WEEK}">{Localisation::getTranslation('common_week')}</option>
                        <option value="{BanTypeEnum::MONTH}">{Localisation::getTranslation('common_month')}</option>
                        <option value="{BanTypeEnum::PERMANENT}">{Localisation::getTranslation('common_permanent')}</option>
                    </select>
                </td>
                <td align="center" valign="top">
                    <button class="btn btn-danger" type="submit" name="banOrg" value="1"><i class="icon-exclamation-sign icon-white"></i> {Localisation::getTranslation('common_ban_organisation')}</button>
                </td>
            </tr>
            <tr>
                <td colspan="3">                    
                    <label for='banReasonOrg'><strong>{Localisation::getTranslation('site_admin_dashboard_ban_reason')}</strong></label>
                    <textarea name='banReasonOrg' cols='40' rows='7' style="width: 99%"></textarea>
                </td>
            </tr>
        </table> 
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
                
    {if !empty($bannedOrgList)}
        <hr/>
        {include file="admin/admin.banned-orgs-list.tpl" bannedOrgList=$bannedOrgList adminUserId=$adminUserId}
        <hr/>
    {/if}  
        
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard" options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table>
            <tr>
                <td colspan="3"> 
                    <label for="banUser"><h2>{Localisation::getTranslation('site_admin_dashboard_ban_user')}</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 20px">
                    {Localisation::getTranslation('site_admin_dashboard_sitewide_user_ban')}
                </td>
            </tr>
            <tr style="font-weight: bold">
                <td >{Localisation::getTranslation('site_admin_dashboard_user_email_address')}</td>
                <td colspan="2"> {Localisation::getTranslation('site_admin_dashboard_ban_duration')}</td>                
            </tr>
            <tr>
                <td width="40%">
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation('site_admin_dashboard_email_here')}" style="width: 96%"/>
                </td>
                <td width="25%">
                    <select name="banTypeUser" style="width: 96%">
                        <option value="{BanTypeEnum::DAY}">{Localisation::getTranslation('common_day')}</option>
                        <option value="{BanTypeEnum::WEEK}">{Localisation::getTranslation('common_week')}</option>
                        <option value="{BanTypeEnum::MONTH}">{Localisation::getTranslation('common_month')}</option>
                        <option value="{BanTypeEnum::PERMANENT}">{Localisation::getTranslation('common_permanent')}</option>
                    </select>
                </td>
                <td align="center" valign="top">
                    <button class="btn btn-danger" type="submit" name="banUser" value="1"><i class="icon-exclamation-sign icon-white"></i> {Localisation::getTranslation('site_admin_dashboard_ban_user')}</button>
                </td>
            </tr>
            <tr>
                <td colspan="3">                    
                    <label for='banReasonUser'><strong>{Localisation::getTranslation('site_admin_dashboard_ban_reason')}</strong></label>
                    <textarea name='banReasonUser' cols='40' rows='7' style="width: 99%"></textarea>
                </td>
            </tr>
        </table> 
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
                
    {if !empty($bannedUserList)}
        <hr/>
        {include file="admin/admin.banned-users-list.tpl" bannedUserList=$bannedUserList adminUserId=$adminUserId}
        <hr/>
    {/if} 
    
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table style="width: 40%">
            <tr>
                <td colspan="2"> 
                    <label for="deleteUser"><h2>{Localisation::getTranslation('site_admin_dashboard_delete_user')}</h2></label>
                </td>
            </tr>
            {if isset($flash['deleteError'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-error">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['deleteError'])}</p>
                    </td>
                </tr>
            {/if}
            {if isset($flash['deleteSuccess'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-success">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['deleteSuccess'])}</p>
                    </td>
                </tr>
            {/if}
            <tr>
                <td colspan="2">
                    {sprintf(Localisation::getTranslation('site_admin_dashboard_permanently'), $siteName)}
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation('site_admin_dashboard_email_here')}" style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-inverse" type="submit" name="deleteUser" value="1" onclick="return confirm(
                                        '{Localisation::getTranslation('site_admin_dashboard_confirm_delete_user')}')"/>
                        <i class="icon-fire icon-white"></i> {Localisation::getTranslation('site_admin_dashboard_delete_user')}
                    </button>
                </td>
            </tr>
        </table> 
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
    <hr/>

    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table style="width: 40%">
            <tr>
                <td colspan="2">
                    <label for="revokeTask"><h2>{Localisation::getTranslation('site_admin_dashboard_revoke_task')}</h2></label>
                </td>
            </tr>
            {if isset($flash['revokeTaskError'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-error">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['revokeTaskError'])}</p>
                    </td>
                </tr>
            {/if}
            {if isset($flash['revokeTaskSuccess'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-success">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['revokeTaskSuccess'])}</p>
                    </td>
                </tr>
            {/if}
            <tr>
                <td colspan="2">
                    {Localisation::getTranslation('site_admin_dashboard_revoke_task_from_user')}
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation('site_admin_dashboard_email_here')}" style="width: 95%"/>
                    <input type="text" name="taskId" placeholder="{Localisation::getTranslation('site_admin_dashboard_task_id_here')}" style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-inverse" type="submit" name="revokeTask" value="1" onclick="return confirm(
                                        '{Localisation::getTranslation('site_admin_dashboard_confirm_revoke_task')}')"/>
                        <i class="icon-fire icon-white"></i> {Localisation::getTranslation('site_admin_dashboard_revoke_task')}
                    </button>
                </td>
            </tr>
        </table> 
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
</div>

{include file='footer.tpl'}
