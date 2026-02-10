{include file='header.tpl'}

<div class="page-header">
    <h1>
        {Localisation::getTranslation('site_admin_dashboard_administration_dashboard')}
        <small>
            {Localisation::getTranslation('site_admin_dashboard_the_site_administrators_dashboard')}
        </small>
     </h1>
</div>

{if isset($flash['revoke_admin_success'])}
    <p class="alert alert-success">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['revoke_admin_success'])}</p>
{/if}
{if isset($flash['generate_invoices_error'])}
    <p class="alert alert-error">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['generate_invoices_error'])}</p>
{/if}
{if isset($flash['generate_invoices_success'])}
    <p class="alert alert-success">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['generate_invoices_success'])}</p>
{/if}


<div class="well">
{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
  {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
    <p><a href="{urlFor name="all_deals_report"}" target="_blank">List all deals.</a></p>
    <p><a href="{urlFor name="paid_projects"}" target="_blank">List all paid projects.</a></p>
    <p><a href="{urlFor name="po_readyness_report"}" target="_blank">Purchase Order Readiness Report.</a></p>
    <p><a href="{urlFor name="pr_report"}" target="_blank">List purchase requisitions.</a></p>
    <p><a href="{urlFor name="po_report"}" target="_blank">List purchase orders.</a></p>
  {/if}
    <p><a href="{urlFor name="community_dashboard"}">[Download community dashboard report]</a></p>
    <p><a href="{urlFor name="users_review"}">List user certificates to be reviewed</a></p>
    <hr />
{elseif $roles & (128)}
    <p><a href="{urlFor name="all_deals_report"}" target="_blank">List all deals.</a></p>
    <p><a href="{urlFor name="paid_projects"}" target="_blank">List all paid projects.</a></p>
    <p><a href="{urlFor name="pr_report"}" target="_blank">List purchase requisitions.</a></p>
    <p><a href="{urlFor name="po_report"}" target="_blank">List purchase orders.</a></p>
    <hr />
{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
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
                    <input type="text" name="search_user" placeholder="User name or email." style="width: 95%"/>
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
{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
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
                    <input type="text" name="search_organisation" placeholder="Organisation name or email." style="width: 95%"/>
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
{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"}" accept-charset="utf-8">
        {if isset($flash['search_project_fail'])}
            <p class="alert alert-error">{$flash['search_project_fail']}</p>
        {/if}
        {if isset($flash['search_project_results'])}
            <table class="alert alert-success">
                {foreach $flash['search_project_results'] as $item}
                    <tr>
                        <td><a href="{urlFor name="project-view" options="project_id.{$item['proj_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($item['proj_title'])}</a></td>
                        <td>{if $item['task_id'] != ''}<a href="{urlFor name="task-view" options="task_id.{$item['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($item['task_title'])}</a>{/if}</td>
                    </tr>
                {/foreach}
                {if count($flash['search_project_results']) == 20}<tr><td>Only 20 shown.</td></tr>{/if}
            </table>
        {/if}

        <table style="width: 40%">
            <tr>
                <td>
                    <input type="text" name="search_project" placeholder="Project or Task name." style="width: 95%"/>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="search_project_submit" value="1">
                        <i class="icon-search icon-white"></i>
                        Search Project
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
    <hr />
{/if}

{if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER + $NGO_ADMIN)}
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table style="width: 40%">
            <tr>
                <td colspan="2">
                    <label for="verify"><h2>{Localisation::getTranslation('email_verification_email_verification')}</h2></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>Verify an email for a user who registered with email and password (but did not act on the verification email) so they can now login.</p>
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
{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"}" accept-charset="utf-8">
        <table style="width: 40%">
            <tr>
                <td colspan="2">
                    <label for="sync_hubspot"><h2>Sync HubSpot</h2></label>
                </td>
            </tr>
            {if isset($flash['sync_hubspot_error'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-error">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['sync_hubspot_error'])}</p>
                    </td>
                </tr>
            {/if}
            {if isset($flash['sync_hubspot_success'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-success">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['sync_hubspot_success'])}</p>
                    </td>
                </tr>
            {/if}
            <tr>
                <td>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="sync_hubspot" value="1" id="sync_hubspot">
                        <i class="icon-star icon-white"></i>
                        Sync
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
    <hr />
{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + 128)}

  {if !($roles & ($SITE_ADMIN + 128))}
        <table>
            <tr>
                <td>
                    <h2>Purchase Order Creation Cutoff Date <small>(tasks completed before the end of day for this date will have SUN POs generated)</small></h2>
                </td>
            </tr>
            <tr>
                <td valign="top">
                        <input type="date" value="{substr($po_cut_off, 0, 10)}" disabled />
                </td>
            </tr>
        </table>
  {/if}
  {if $roles & ($SITE_ADMIN + 128)}
        <table>
            <tr>
                <td>
                    <h2>Purchase Order Creation Cutoff Date <small>(tasks completed before the end of day for this date will have SUN POs generated)</small></h2>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"}" accept-charset="utf-8">
                        <input type="date" name="po_cut_off" id="po_cut_off" value="{substr($po_cut_off, 0, 10)}" />
                        <button class="btn btn-success" type="submit" name="set_cut_off" id="set_cut_off" value="1">
                            <i class="icon-star icon-white"></i>
                            Set Cutoff Date
                        </button>
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                </td>
            </tr>
        </table>

        <table style="width: 40%">
            <tr>
                <td colspan="2">
                    <h2>Generate Invoices (for all tasks with purchase orders)</h2>
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td valign="top">
                    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"}" accept-charset="utf-8">
                    <button class="btn btn-success" type="submit" name="generate_invoices" value="1" id="generate_invoices">
                        <i class="icon-star icon-white"></i>
                        Generate Invoices
                    </button>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                </td>
            </tr>
        </table>
  {/if}
  <a href="{urlFor name="sow_report"}" target="_blank">SoW Report</a>&nbsp;&nbsp;&nbsp;<a href="{urlFor name="sow_linguist_report"}" target="_blank">SoW Linguist Report</a>
  <hr />
{/if}

{if $roles & ($SITE_ADMIN)}
    <form method="post" enctype="multipart/form-data" action="{urlFor name="site-admin-dashboard"  options="user_id.$adminUserId"}" accept-charset="utf-8">
        <table>
            <tr>
                <td colspan="2"> 
                    <h2>Add New Site Role to Existing TWB Platform User specified by email</h2>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p><strong>{Localisation::getTranslation('site_admin_dashboard_only_add_trusted')}</strong></p>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="userEmail" placeholder="{Localisation::getTranslation('site_admin_dashboard_email_here')}" style="width: 95%"/>
                </td>
                <td valign="top">
                    <select name="admin_type">
                        <option value="{$COMMUNITY_OFFICER}">COMMUNITY OFFICER</option>
                        <option value="{$PROJECT_OFFICER}">PROJECT OFFICER</option>
                        <option value="{$SITE_ADMIN}">TWB ADMIN</option>
                    </select>
                </td>
            </tr>
            {if isset($flash['add_admin_error'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-error">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['add_admin_error'])}</p>
                    </td>
                </tr>
            {/if}
            {if isset($flash['add_admin_success'])}
                <tr>
                    <td colspan="2">
                        <p class="alert alert-success">{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['add_admin_success'])}</p>
                    </td>
                </tr>
            {/if}
            <tr>
                <td>
                </td>
                <td valign="top">
                    <button class="btn btn-success" type="submit" name="addAdmin" value="1">
                        <i class="icon-star icon-white"></i>
                        Add this Role to this User
                    </button>
                </td>
            </tr>
        </table> 
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>

    <hr />
    <table>
        <tr>
            <td>
                <h2>Invite New User from Outside TWB Platform to be Assigned Role</h2>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="{urlFor name="invite_site_admins"}" class='btn btn-success'>
                    <i class="icon-star icon-white"></i> Invite New Admin User
                </a>
            </td>
        </tr>
    </table>

    {if !empty($adminList)}
        <hr/>
        {include file="admin/admin.site-admins-list.tpl" adminList=$adminList adminUserId=$adminUserId}
        <hr/>
    {/if}    
{/if}

{if $roles & ($SITE_ADMIN)}
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
{/if}
        
{if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}
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
{/if}

{if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}
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
{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
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
                    {Localisation::getTranslation('site_admin_dashboard_revoke_task_from_user')} (User will not be added to deny list.)
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
{/if}

{include file='footer.tpl'}
