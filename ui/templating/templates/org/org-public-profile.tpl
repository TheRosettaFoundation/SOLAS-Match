{include file='header.tpl'}

{if isset($org)}
    {if isset($flash['error'])}
        <div class="alert alert-error">
            {$flash['error']}
        </div>
    {/if}
    {if isset($flash['success'])}
        <div class="alert alert-success">
            {$flash['success']}
        </div>
    {/if}
    {if isset($flash['info'])}
        <div class="alert alert-info">
            {$flash['info']}
        </div>
    {/if}
    <div class='page-header'>
        <h1>
            {if $org->getName() != ''}
                {$org->getName()}
            {else}
                {Localisation::getTranslation('common_organisation_profile')}
            {/if}
            <small>{sprintf(Localisation::getTranslation('org_public_profile_an_organisation_of'), Settings::get("site.name"))}</small>
            {assign var="org_id" value=$org->getId()}
            {if isset($user)}
                <div class="pull-right">
                    {if !$isMember}
                        <form id="trackedOrganisationForm" method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                            {if $isMember || $adminAccess}
                            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='btn btn-primary'>
                                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('org_public_profile_edit_organisation_details')}
                            </a>
                            {/if}
                            {if false}
                            <a href="{urlFor name="org-request-membership" options="org_id.$org_id"}" class='btn btn-primary'>
                                <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation('org_public_profile_request_membership')}
                            </a>
                            {/if}
                            {if $userSubscribedToOrganisation}
                                <input type="hidden" name="trackOrganisation" value="0" />
                                <a class="btn btn-small btn-inverse" onclick="$('#trackedOrganisationForm').submit();" >
                                    <i class="icon-remove-circle icon-white"></i>{Localisation::getTranslation('org_public_profile_untrack_organisation')}
                                </a>
                            {else}
                                <input type="hidden" name="trackOrganisation" value="1" />
                                <a class="btn btn-small" onclick="$('#trackedOrganisationForm').submit();" >
                                    <i class="icon-envelope icon-black"></i>{Localisation::getTranslation('org_public_profile_track_organisation')}
                                </a>
                            {/if}
                        </form>
                    {/if}
                    {if $isMember && $adminAccess}
                        <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='btn btn-primary'>
                            <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('org_public_profile_edit_organisation_details')}
                        </a>
                    {/if}
                </div>
            {/if}
        </h1>
    </div>
{/if}

    <div class="well">
        <table>
            <tr valign="top">
                <td  style="width: 48%">
                    <div>
                        <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                            <thead>                
                                <th align="left">{Localisation::getTranslation('org_private_profile_organisation_overview')}<hr/></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getBiography() != ''}
                                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org->getBiography())}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_activity')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptionsSemicolon($activitys)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_website')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getHomepage() != 'http://' && $org->getHomepage() != ''}
                                            <a href="{$org->getHomepage()}">{$org->getHomepage()}</a>
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_home_page_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_facebook')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org2->getFacebook() != 'http://' && $org2->getFacebook() != ''}
                                            <a href="{$org2->getFacebook()}">{$org2->getFacebook()}</a>
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_linkedin')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org2->getLinkedin() != 'http://' && $org2->getLinkedin() != ''}
                                            <a href="{$org2->getLinkedin()}">{$org2->getLinkedin()}</a>
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_twitter')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org2->getPrimaryContactEmail() != 'http://' && $org2->getPrimaryContactEmail() != ''}
                                            <a href="{$org2->getPrimaryContactEmail()}">{$org2->getPrimaryContactEmail()}</a>
                                        {/if}
                                    </td>
                                </tr>

                                {if $isMember || $adminAccess}
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_name')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::uiCleanseHTML($org2->getPrimaryContactName())}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_title')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::uiCleanseHTML($org2->getPrimaryContactTitle())}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_email')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getEmail() != ''}
                                            <a href="mailto:{$org->getEmail()}">{$org->getEmail()}</a>
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_email_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_phone')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::uiCleanseHTML($org2->getPrimaryContactPhone())}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_other_contacts')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getOtherContacts())}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_structure')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getStructure())}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_affiliations')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getAffiliations())}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_url_video_1')}<br />(1)</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org2->getUrlVideo1() != 'http://' && $org2->getUrlVideo1() != ''}
                                            <a href="{$org2->getUrlVideo1()}">{$org2->getUrlVideo1()}</a>
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>(2)</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org2->getUrlVideo2() != 'http://' && $org2->getUrlVideo2() != ''}
                                            <a href="{$org2->getUrlVideo2()}">{$org2->getUrlVideo2()}</a>
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>(3)</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org2->getUrlVideo3() != 'http://' && $org2->getUrlVideo3() != ''}
                                            <a href="{$org2->getUrlVideo3()}">{$org2->getUrlVideo3()}</a>
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_employee')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($employees)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_funding')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($fundings)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_find')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($finds)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_translation')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($translations)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_request')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($requests)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_content')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($contents)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_subject_matters')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getSubjectMatters())}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_pages')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($pages)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_source')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($sources)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_target')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($targets)}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('org_private_profile_organisation_often')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {TemplateHelper::expandSelectedOptions($oftens)}
                                    </td>
                                </tr>
                                {/if}
                            </tbody>
                        </table>
                    </div>
                </td>
                <td style="width: 4%"/>
                <td style="width: 48%">            
                    <div>
                        <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                            <thead>                
                                <th align="left" width="48%">{Localisation::getTranslation('common_address')}<hr/></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getAddress() != ''}
                                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org->getAddress())}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_address_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_city')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCity() != ''}
                                            {TemplateHelper::uiCleanseHTML($org->getCity())}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_city_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_country')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCountry() != ''}
                                            {TemplateHelper::uiCleanseHTML($org->getCountry())}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_country_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_regional_focus')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getRegionalFocus() != ''}
                                            {$org->getRegionalFocus()}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_regional_focus_listed')}
                                        {/if}
                                    </td>  
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
                        
                
    <p style="margin-bottom: 60px" />         
    <h1 class="page-header">
        {Localisation::getTranslation('common_badges')}
        <small>{Localisation::getTranslation('org_public_profile_badge_overview')}</small>

        {if isset($user)}

            {if $isMember || $adminAccess}
                <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                    <i class="icon-star icon-white"></i> {Localisation::getTranslation('org_public_profile_create_badge')}
                </a>
            {/if}
        {/if}
    </h1>  
    <p style="margin-bottom: 40px" />   

{if $org_badges != NULL && count($org_badges) > 0}                
    <table class="table table-striped">
        <thead>            
            <th style="text-align: left">{Localisation::getTranslation('common_name')}</th>
            <th>{Localisation::getTranslation('common_description')}</th>

            {if $isMember || $adminAccess}
                <th>{Localisation::getTranslation('common_edit')}</th>
                <th>{Localisation::getTranslation('common_assign')}</th>
                <th>{Localisation::getTranslation('common_delete')}</th>
            {/if}
        </thead>
        <tbody>
        {foreach $org_badges as $badge}
            {assign var="badge_id" value=$badge->getId()}
            {assign var="org_id" value=$org->getId()}
            <tr>
                <td style="text-align: left" width="20%">
                    <strong>{$badge->getTitle()}</strong>
                </td>
                <td width="35%">
                    {$badge->getDescription()}
                </td>
                {if ($isMember || $adminAccess) && isset($user)}
                    <td>
                        <a href="{urlFor name="org-edit-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                            <i class="icon-wrench icon-black"></i> {Localisation::getTranslation('org_public_profile_edit_badge')}
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                            <i class="icon-plus-sign icon-black"></i> {Localisation::getTranslation('common_assign_badge')}
                        </a>
                    </td>
                    <td>                        
                        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                            <input type="hidden" name="badge_id" value="{$badge_id}" />
                            <input type="submit" class='btn btn-inverse' name="deleteBadge" value="    {Localisation::getTranslation('org_public_profile_delete_badge')}"
                              onclick="return confirm('{Localisation::getTranslation('org_public_profile_confirm_delete_badge')}')" />                                 
                        </form> 
                        <i class="icon-fire icon-white" style="position:relative; right:44px; top:-40px;"></i> 
                    </td>  
                {/if}
            </tr>
        {/foreach}
        </tbody>
    </table>
<br />
{else}
    <p class="alert alert-info">
        {Localisation::getTranslation('org_public_profile_no_badges_associated')}
    </p>
    <p style="margin-bottom:20px;"></p>
{/if}
      

{if $isMember || $adminAccess}               
     <p style="margin-bottom: 40px" />         
     <h1 class="page-header">
         {Localisation::getTranslation('org_public_profile_membership_requests')}
         <small>{Localisation::getTranslation('org_public_profile_membership_request_overview')}</small>

             <a href="{urlFor name="org-request-queue" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                 <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_add_user')}
             </a>
     </h1>                  
     <p style="margin-bottom: 40px" />               

     {if isset($membershipRequestUsers) && count($membershipRequestUsers) > 0}
         <table class="table table-striped">
             <thead>            
                 <th style="text-align: left"><strong>{Localisation::getTranslation('common_name')}</strong></th>
                 <th><strong>{Localisation::getTranslation('common_biography')}</strong></th>
                 <th>{Localisation::getTranslation('org_public_profile_accept')}</th>
                 <th>{Localisation::getTranslation('org_public_profile_deny')}</th>
             </thead>
             <tbody>
             {foreach $membershipRequestUsers as $nonMember}
                 <tr>
                     {assign var="user_id" value=$nonMember->getId()}                        
                     {if $nonMember->getDisplayName() != ''}
                         <td style="text-align: left">
                             <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{$nonMember->getDisplayName()}</a>
                         </td>
                     {/if}
                     <td width="50%">
                         <i>
                         {if $nonMember->getBiography() != ''}
                             {TemplateHelper::uiCleanseHTMLNewlineAndTabs($nonMember->getBiography())}
                         {else}
                             {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                         {/if}
                         </i>
                     </td>
                     <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                         <input type="hidden" name="user_id" value="{$nonMember->getId()}" />
                         <td>
                             <input type="submit" name="accept" value="    Accept Request" class="btn btn-primary" />
                             <i class="icon-ok-circle icon-white" style="position:relative; right:126px; top:2px;"></i>
                         </td>
                         <td>
                             <input type="submit" name="refuse" value="    Refuse Request" class="btn btn-inverse" />
                             <i class="icon-remove-circle icon-white" style="position:relative; right:126px; top:2px;"></i>
                         </td>
                     </form>
                 </tr>
             {/foreach}
             </tbody>
         </table>   
     {else}
         <p class="alert alert-info">
             {Localisation::getTranslation('org_public_profile_no_membership_requests_associated')}
         </p>
     {/if}
     <p style="margin-bottom: 40px" />               
 {/if}


{if $isMember || $adminAccess}
    <h1 class="page-header">
        {Localisation::getTranslation('org_public_profile_organisation_members')}
        <small>{Localisation::getTranslation('org_public_profile_member_list')}</small>
    </h1>
    {if isset($orgMembers) && count($orgMembers) > 0}
        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            <table class="table table-striped">
                <thead>
                    <th>{Localisation::getTranslation('org_public_profile_member_type')}</th>
                    <th>{Localisation::getTranslation('org_public_profile_username')}</th>
                    {if $adminAccess}
                        <th>{Localisation::getTranslation('org_public_profile_remove_user')}</th>
                        <th>{Localisation::getTranslation('org_public_profile_alter_permissions')}</th>
                    {/if}
                </thead>
                <tbody>
                    {foreach $orgMembers as $member}
                        <tr>
                            <td>
                                {if $memberIsAdmin[{$member->getId()}]}
                                    <span class="marker org-admin-marker">{Localisation::getTranslation('org_public_profile_administrator')}</span>
                                {else}
                                    <span class="marker org-member-marker">{Localisation::getTranslation('org_public_profile_member')}</span >
                                {/if}
                            </td>
                            <td>
                                <a href="{urlFor name="user-public-profile" options="user_id.{$member->getId()}"}">{$member->getDisplayName()}</a>
                            </td>
                        {if $adminAccess}
                            <td>
                                <button type="submit" name="revokeUser" value="{$member->getId()}" class="btn btn-inverse" 
                                        onclick="return confirm('{Localisation::getTranslation('org_public_profile_confirm_revoke_membership')}')">
                                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_public_profile_revoke_membership')}
                                </button>
                            </td>
                            <td>
                                {if $memberIsAdmin[{$member->getId()}]}
                                    <button type="submit" name="revokeOrgAdmin" value="{$member->getId()}" class="btn btn-inverse" 
                                            onclick="return confirm('{Localisation::getTranslation('org_public_profile_confirm_revoke_admin')}')">
                                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_public_profile_revoke_administrator')}
                                    </button>
                                {else}
                                    <button type="submit" name="makeOrgAdmin" value="{$member->getId()}" class="btn btn-success" 
                                            onclick="return confirm('{Localisation::getTranslation('org_public_profile_confirm_make_admin')}')"> 
                                            <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_create_administrator')}
                                    </button>
                                {/if}
                            </td>
                        {/if}
                      </tr>
                    {/foreach}
                </tbody>
            </table>
        </form>
    {else}
        <p class="alert alert-info">{Localisation::getTranslation('org_public_profile_no_members')}</p>
    {/if}
{/if}

{include file='footer.tpl'}
