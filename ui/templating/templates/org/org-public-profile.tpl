 
 {* {include file='header.tpl'} *}
{include file='new_header.tpl'}

{if isset($org)}
    {if isset($flash['error'])}
        <div class="alert alert-danger">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </div>
    {/if}
    {if isset($flash['success'])}
        <div class="alert alert-success">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
        </div>
    {/if}
    {if isset($flash['info'])}
        <div class="alert alert-info">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}
        </div>
    {/if}
    
    <div class="container-fluid py-4">
        <div class="page-header mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h1 class="mb-3 mb-md-0">
                    {if $org->getName() != ''}
                        {$org->getName()}
                    {else}
                        {Localisation::getTranslation('common_organisation_profile')}
                    {/if}
                </h1>
                
                {assign var="org_id" value=$org->getId()}
                {if isset($user)}
                    <div class="d-flex flex-wrap gap-2">
                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                        <a href="{urlFor name="org-projects" options="org_id.$org_id"}" class='btn btn-primary'>
                            <i class="fa fa-briefcase me-1"></i> Organization Dashboard
                        </a>
                        {/if}

                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                        <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='btn btn-primary'>
                            <i class="fa fa-wrench me-1"></i> {Localisation::getTranslation('org_public_profile_edit_organisation_details')}
                        </a>
                        {/if}

                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                        <a class="btn btn-success" href="{urlFor name="project-create" options="org_id.$org_id"}">
                            <i class="fa fa-upload me-1"></i> New Phrase Project
                        </a>
                        {/if}

                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($org_id, $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                        <a class="btn btn-success" href="{urlFor name="project-create-empty" options="org_id.$org_id"}">
                            <i class="fa fa-upload me-1"></i> New non-Phrase Project
                        </a>
                        {/if}
                    </div>
                {/if}
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title border-bottom pb-2">{Localisation::getTranslation('org_private_profile_organisation_overview')}</h5>
                        <p class="card-text fst-italic">
                            {if $org->getBiography() != ''}
                                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org->getBiography())}
                            {else}
                                {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                            {/if}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_activity')}</h5>
                        <p class="card-text fst-italic">
                            {TemplateHelper::expandSelectedOptionsSemicolon($activitys)}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_website')}</h5>
                        <p class="card-text fst-italic">
                            {if $org->getHomepage() != 'https://' && $org->getHomepage() != ''}
                                <a href="{$org->getHomepage()}" target="_blank">{$org->getHomepage()}</a>
                            {else}
                                {Localisation::getTranslation('org_public_profile_no_home_page_listed')}
                            {/if}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_facebook')}</h5>
                        <p class="card-text fst-italic">
                            {if $org2->getFacebook() != 'https://' && $org2->getFacebook() != ''}
                                <a href="{$org2->getFacebook()}" target="_blank">{$org2->getFacebook()}</a>
                            {/if}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_linkedin')}</h5>
                        <p class="card-text fst-italic">
                            {if $org2->getLinkedin() != 'https://' && $org2->getLinkedin() != ''}
                                <a href="{$org2->getLinkedin()}" target="_blank">{$org2->getLinkedin()}</a>
                            {/if}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_twitter')}</h5>
                        <p class="card-text fst-italic">
                            {if $org2->getPrimaryContactEmail() != 'https://' && $org2->getPrimaryContactEmail() != ''}
                                <a href="{$org2->getPrimaryContactEmail()}" target="_blank">{$org2->getPrimaryContactEmail()}</a>
                            {/if}
                        </p>

                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_primary_contact_name')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::uiCleanseHTML($org2->getPrimaryContactName())}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_primary_contact_title')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::uiCleanseHTML($org2->getPrimaryContactTitle())}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_primary_contact_email')}</h5>
                            <p class="card-text fst-italic">
                                {if $org->getEmail() != ''}
                                    <a href="mailto:{$org->getEmail()}">{$org->getEmail()}</a>
                                {else}
                                    {Localisation::getTranslation('org_public_profile_no_email_listed')}
                                {/if}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_primary_contact_phone')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::uiCleanseHTML($org2->getPrimaryContactPhone())}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_other_contacts')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getOtherContacts())}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_structure')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getStructure())}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_affiliations')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getAffiliations())}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_url_video_1')} (1)</h5>
                            <p class="card-text fst-italic">
                                {if $org2->getUrlVideo1() != 'https://' && $org2->getUrlVideo1() != ''}
                                    <a href="{$org2->getUrlVideo1()}" target="_blank">{$org2->getUrlVideo1()}</a>
                                {/if}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">(2)</h5>
                            <p class="card-text fst-italic">
                                {if $org2->getUrlVideo2() != 'https://' && $org2->getUrlVideo2() != ''}
                                    <a href="{$org2->getUrlVideo2()}" target="_blank">{$org2->getUrlVideo2()}</a>
                                {/if}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">(3)</h5>
                            <p class="card-text fst-italic">
                                {if $org2->getUrlVideo3() != 'https://' && $org2->getUrlVideo3() != ''}
                                    <a href="{$org2->getUrlVideo3()}" target="_blank">{$org2->getUrlVideo3()}</a>
                                {/if}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_employee')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($employees)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_funding')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($fundings)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_find')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($finds)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_translation')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($translations)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_request')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($requests)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_content')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($contents)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_subject_matters')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org2->getSubjectMatters())}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_pages')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($pages)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_source')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($sources)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_target')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($targets)}
                            </p>

                            <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('org_private_profile_organisation_often')}</h5>
                            <p class="card-text fst-italic">
                                {TemplateHelper::expandSelectedOptions($oftens)}
                            </p>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title border-bottom pb-2">{Localisation::getTranslation('common_address')}</h5>
                        <p class="card-text fst-italic">
                            {if $org->getAddress() != ''}
                                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org->getAddress())}
                            {else}
                                {Localisation::getTranslation('org_public_profile_no_address_listed')}
                            {/if}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('common_city')}</h5>
                        <p class="card-text fst-italic">
                            {if $org->getCity() != ''}
                                {TemplateHelper::uiCleanseHTML($org->getCity())}
                            {else}
                                {Localisation::getTranslation('org_public_profile_no_city_listed')}
                            {/if}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('common_country')}</h5>
                        <p class="card-text fst-italic">
                            {if $org->getCountry() != ''}
                                {TemplateHelper::uiCleanseHTML($org->getCountry())}
                            {else}
                                {Localisation::getTranslation('org_public_profile_no_country_listed')}
                            {/if}
                        </p>

                        <h5 class="card-title border-bottom pb-2 mt-4">{Localisation::getTranslation('common_regional_focus')}</h5>
                        <p class="card-text fst-italic">
                            {if $org->getRegionalFocus() != ''}
                                {$org->getRegionalFocus()}
                            {else}
                                {Localisation::getTranslation('org_public_profile_no_regional_focus_listed')}
                            {/if}
                        </p>

                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN*0)}
                            <h5 class="card-title border-bottom pb-2 mt-4">Work Report</h5>
                            <p class="card-text fst-italic">
                                <a href="{urlFor name="partner_deals" options="org_id.$org_id"}" target="_blank">Work Report</a>
                            </p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
                
        <div class="mb-5"></div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                {Localisation::getTranslation('common_badges')}
                <small class="text-muted">{Localisation::getTranslation('org_public_profile_badge_overview')}</small>
            </h2>

            {if isset($user) && $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}" class='btn btn-success'>
                    <i class="fa fa-star me-1"></i> {Localisation::getTranslation('org_public_profile_create_badge')}
                </a>
            {/if}
        </div>

        {if !empty($org_badges)}
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>            
                        <th>{Localisation::getTranslation('common_name')}</th>
                        <th>{Localisation::getTranslation('common_description')}</th>
                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                            <th class="text-center">{Localisation::getTranslation('common_edit')}</th>
                            <th class="text-center">{Localisation::getTranslation('common_assign')}</th>
                            <th class="text-center">{Localisation::getTranslation('common_delete')}</th>
                        {/if}
                    </thead>
                    <tbody>
                    {foreach $org_badges as $badge}
                        {assign var="badge_id" value=$badge->getId()}
                        {assign var="org_id" value=$org->getId()}
                        <tr>
                            <td>
                                <strong>{TemplateHelper::uiCleanseHTML($badge->getTitle())}</strong>
                            </td>
                            <td>
                                {TemplateHelper::uiCleanseHTML($badge->getDescription())}
                            </td>
                            {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && isset($user)}
                                <td class="text-center">
                                    <a href="{urlFor name="org-edit-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn btn-sm btn-outline-primary'>
                                        <i class="fa fa-wrench me-1"></i> {Localisation::getTranslation('org_public_profile_edit_badge')}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn btn-sm btn-outline-primary'>
                                        <i class="fa fa-plus-circle me-1"></i> {Localisation::getTranslation('common_assign_badge')}
                                    </a>
                                </td>
                                <td class="text-center">                        
                                    <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="d-inline">
                                        <input type="hidden" name="badge_id" value="{$badge_id}" />
                                        <button type="submit" class='btn btn-sm btn-danger' name="deleteBadge" 
                                            onclick="return confirm('{Localisation::getTranslation('org_public_profile_confirm_delete_badge')}')">
                                            <i class="fa fa-trash me-1"></i> {Localisation::getTranslation('org_public_profile_delete_badge')}
                                        </button>                                 
                                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                    </form> 
                                </td>  
                            {/if}
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <div class="alert alert-info">
                {Localisation::getTranslation('org_public_profile_no_badges_associated')}
            </div>
        {/if}
          
        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
            <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                <h2>
                    {Localisation::getTranslation('org_public_profile_organisation_members')}
                    <small class="text-muted">{Localisation::getTranslation('org_public_profile_member_list')}</small>
                </h2>

                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                    <a href="{urlFor name="invite_admins" options="org_id.$org_id"}" class='btn btn-success'>
                        <i class="fa fa-user-plus me-1"></i> Add users and assign roles
                    </a>
                {/if}
            </div>
            
            {if !empty($orgMembers)}
                <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <th>{Localisation::getTranslation('org_public_profile_member_type')}</th>
                                <th>Email</th>
                                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                                    <th>Remove Role</th>
                                    <th>Add Role</th>
                                {/if}
                            </thead>
                            <tbody>
                                {foreach $orgMembers as $member}
                                    <tr>
                                        <td>
                                            {if $member['roles'] & $NGO_ADMIN}
                                                <span class="badge bg-danger">ADMIN</span>
                                            {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                                <span class="badge bg-warning text-dark">PROJECT OFFICER</span>
                                            {else}
                                                <span class="badge bg-info">LINGUIST{if !($member['roles'] & $LINGUIST)} (exclusive){/if}</span>
                                            {/if}
                                        </td>
                                        <td>
                                            <a href="{urlFor name="user-public-profile" options="user_id.{$member['id']}"}">{$member['email']}</a>
                                        </td>
                                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                                        <td>
                                            {if $member['roles'] & $NGO_ADMIN}
                                                <button type="submit" name="revokeOrgAdmin" value="{$member['id']}" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to revoke ADMIN role from this user?')">
                                                    <i class="fa fa-user-times me-1"></i> Remove ADMIN Role
                                                </button>
                                            {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                                <button type="submit" name="revokeOrgPO" value="{$member['id']}" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to revoke PROJECT OFFICER role from this user?')">
                                                    <i class="fa fa-user-times me-1"></i> Remove PROJECT OFFICER Role
                                                </button>
                                            {else}
                                                <button type="submit" name="revokeUser" value="{$member['id']}" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to permanently remove this user from Organization?')">
                                                    <i class="fa fa-user-times me-1"></i> Remove LINGUIST
                                                </button>
                                            {/if}
                                        </td>
                                        <td>
                                            {if $member['roles'] & $NGO_ADMIN}
                                            {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                                <button type="submit" name="makeOrgAdmin" value="{$member['id']}" class="btn btn-sm btn-outline-success" 
                                                        onclick="return confirm('Are you sure you want to make this user an ADMIN of this organization?')"> 
                                                        <i class="fa fa-user-plus me-1"></i> Create ADMIN
                                                </button>
                                            {else}
                                                <button type="submit" name="makeOrgPO" value="{$member['id']}" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Are you sure you want to make this user a PROJECT OFFICER of this organization?')">
                                                        <i class="fa fa-user-plus me-1"></i> Create PROJECT OFFICER
                                                </button>
                                            {/if}
                                        </td>
                                    {/if}
                                  </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            {else}
                <div class="alert alert-info">{Localisation::getTranslation('org_public_profile_no_members')}</div>
            {/if}
        {/if}

        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
            <div class="mt-5">
                <h2 class="mb-4">Asana Board for Partner</h2>
                <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="row g-3">
                    <div class="col-md-8">
                        <label for="asana_board" class="form-label"><strong>Asana ID (not full URL) for this Partner's Board/Project</strong></label>
                        <input type="text" class="form-control" name="asana_board" id="asana_board" maxlength="20" value="{$asana_board_for_org['asana_board']}" />
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" value="set_asana_board" name="set_asana_board" class="btn btn-primary">
                            <i class="fa fa-refresh me-1"></i> Update Asana ID
                        </button>
                    </div>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </div>

            <div class="mt-5">
                <h2 class="mb-4">Resources</h2>
                <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="row g-3">
                    <div class="col-md-8">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mt_for_org" id="mt_for_org" value="1" {if $mt_for_org}checked="checked"{/if} />
                            <label class="form-check-label" for="mt_for_org">
                                Use machine translation in projects for this organization
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" value="set_mt_for_org" name="set_mt_for_org" class="btn btn-primary">
                            <i class="fa fa-refresh me-1"></i> Update Resources
                        </button>
                    </div>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </div>

            <div class="mt-5">
                <h2 class="mb-4">
                    Subscription
                    <small class="text-muted">Set or update subscription for this organisation.</small>
                </h2>
                <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="row g-3">
                    {if $no_subscription}
                    <div class="col-12">
                        <div class="alert alert-info"><strong>There is no Subscription for this organisation, set one if desired...</strong></div>
                    </div>
                    {/if}
                    
                    <div class="col-md-6">
                        <label for="level" class="form-label"><strong>Level</strong></label>
                        <select class="form-select" name="level" id="level">
                            <option value="10"   {if $subscription['level'] ==   10}selected="selected"{/if}>Intermittent use for year</option>
                            <option value="20"   {if $subscription['level'] ==   20}selected="selected"{/if}>Moderate use for year</option>
                            <option value="30"   {if $subscription['level'] ==   30}selected="selected"{/if}>Heavy use for year</option>
                            <option value="100"  {if $subscription['level'] ==  100}selected="selected"{/if}>Partner</option>
                            <option value="1000" {if $subscription['level'] == 1000}selected="selected"{/if}>Free because unable to pay</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="start_date_field" class="form-label"><strong>Start Date</strong></label>
                        {if $start_date_error != ''}
                            <div class="alert alert-danger">
                                {$start_date_error}
                            </div>
                        {/if}
                        <input class="form-control hasDatePicker" type="text" id="start_date_field" name="start_date_field" value="{$subscription['start_date']}" />
                        <input type="hidden" name="start_date" id="start_date" />
                    </div>
                    
                    <div class="col-12">
                        <label for="comment" class="form-label"><strong>Comment</strong></label>
                        <input type="text" class="form-control" name="comment" id="comment" maxlength="255" value="{$subscription['comment']|escape:'html':'UTF-8'}" />
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" onclick="return validateForm();" value="setSubscription" name="setsubscription" class="btn btn-primary">
                            <i class="fa fa-refresh me-1"></i> Update Subscription
                        </button>
                    </div>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </div>
        {/if}

        {if 0 & $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
            <div class="mt-5">
                <h2 class="mb-4">
                    {Localisation::getTranslation('required_qualification_level')}
                    <small class="text-muted">{Localisation::getTranslation('set_default_required_qualification_level')}</small>
                </h2>
                <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="row g-3">
                    <div class="col-md-4">
                        <select class="form-select" name="required_qualification_level" id="required_qualification_level">
                            <option value="1" {if $required_qualification_level == 1}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_1')}</option>
                            <option value="2" {if $required_qualification_level == 2}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_2')}</option>
                            <option value="3" {if $required_qualification_level == 3}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_3')}</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" value="set_required_qualification_level" name="set_required_qualification_level" class="btn btn-primary">
                            <i class="fa fa-refresh me-1"></i> {Localisation::getTranslation('update_required_qualification_level')}
                        </button>
                    </div>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </div>
        {/if}
    </div>
{/if}

{include file='footer.tpl'}
 
 #########################################################################################################################################################################################################################################################################################################################################################################################
 {* {include file='header.tpl'} 
{include file='new_header.tpl'}

{if isset($org)}
    {if isset($flash['error'])}
        <div class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </div>
    {/if}
    {if isset($flash['success'])}
        <div class="alert alert-success">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
        </div>
    {/if}
    {if isset($flash['info'])}
        <div class="alert alert-info">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}
        </div>
    {/if}
    <div class='page-header'>
        <h1>
            {if $org->getName() != ''}
                {$org->getName()}
            {else}
                {Localisation::getTranslation('common_organisation_profile')}
            {/if}
            {assign var="org_id" value=$org->getId()}
            {if isset($user)}
                <div class="pull-right">
                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                            <a href="{urlFor name="org-projects" options="org_id.$org_id"}" class='btn btn-primary'>
                                <i class="icon-briefcase icon-white"></i> Organization Dashboard
                            </a>
                            {/if}

                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='btn btn-primary'>
                                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('org_public_profile_edit_organisation_details')}
                            </a>
                            {/if}

                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                            <a class="btn btn-success" href="{urlFor name="project-create" options="org_id.$org_id"}">
                                <i class="icon-upload icon-white"></i> New Phrase Project
                            </a>
                            {/if}

                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($org_id, $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                            <a class="btn btn-success" href="{urlFor name="project-create-empty" options="org_id.$org_id"}">
                                <i class="icon-upload icon-white"></i> New non-Phrase Project
                            </a>
                            {/if}
                </div>
            {/if}
        </h1>
    </div>
{/if}

    <div class="table-responsive">
        <table class="w-100 ">
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
                                        {if $org->getHomepage() != 'https://' && $org->getHomepage() != ''}
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
                                        {if $org2->getFacebook() != 'https://' && $org2->getFacebook() != ''}
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
                                        {if $org2->getLinkedin() != 'https://' && $org2->getLinkedin() != ''}
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
                                        {if $org2->getPrimaryContactEmail() != 'https://' && $org2->getPrimaryContactEmail() != ''}
                                            <a href="{$org2->getPrimaryContactEmail()}">{$org2->getPrimaryContactEmail()}</a>
                                        {/if}
                                    </td>
                                </tr>

                                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
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
                                        {if $org2->getUrlVideo1() != 'https://' && $org2->getUrlVideo1() != ''}
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
                                        {if $org2->getUrlVideo2() != 'https://' && $org2->getUrlVideo2() != ''}
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
                                        {if $org2->getUrlVideo3() != 'https://' && $org2->getUrlVideo3() != ''}
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
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>

                                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN*0)}
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>Work Report</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        <a href="{urlFor name="partner_deals" options="org_id.$org_id"}" target="_blank">Work Report</a>
                                    </td>
                                </tr>
                                {/if}

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

            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                    <i class="icon-star icon-white"></i> {Localisation::getTranslation('org_public_profile_create_badge')}
                </a>
            {/if}
        {/if}
    </h1>  
    <p style="margin-bottom: 40px" />   

{if !empty($org_badges)}
    <table class="table table-striped">
        <thead>            
            <th style="text-align: left">{Localisation::getTranslation('common_name')}</th>
            <th>{Localisation::getTranslation('common_description')}</th>

            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
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
                    <strong>{TemplateHelper::uiCleanseHTML($badge->getTitle())}</strong>
                </td>
                <td width="35%">
                    {TemplateHelper::uiCleanseHTML($badge->getDescription())}
                </td>
                {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && isset($user)}
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
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
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
      
{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
    <h1 class="page-header">
        {Localisation::getTranslation('org_public_profile_organisation_members')}
        <small>{Localisation::getTranslation('org_public_profile_member_list')}</small>

      {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
        <a href="{urlFor name="invite_admins" options="org_id.$org_id"}" class='pull-right btn btn-success'>
            <i class="icon-star icon-white"></i> Add users and assign roles
        </a>
      {/if}
    </h1>
    {if !empty($orgMembers)}
        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            <table class="table table-striped">
                <thead>
                    <th>{Localisation::getTranslation('org_public_profile_member_type')}</th>
                    <th>email</th>
                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                        <th>Remove Role</th>
                        <th>Add Role</th>
                    {/if}
                </thead>
                <tbody>
                    {foreach $orgMembers as $member}
                        <tr>
                            <td>
                                {if $member['roles'] & $NGO_ADMIN}
                                    <span class="marker org-admin-marker">ADMIN</span>
                                {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                    <span class="marker org-member-marker">PROJECT OFFICER</span>
                                {else}
                                    <span class="marker org-member-marker">LINGUIST{if !($member['roles'] & $LINGUIST)} (exclusive){/if}</span>
                                {/if}
                            </td>
                            <td>
                                <a href="{urlFor name="user-public-profile" options="user_id.{$member['id']}"}">{$member['email']}</a>
                            </td>
                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                            <td>
                                {if $member['roles'] & $NGO_ADMIN}
                                    <button type="submit" name="revokeOrgAdmin" value="{$member['id']}" class="btn btn-inverse"
                                            onclick="return confirm('Are you sure you want to revoke ADMIN role from this user?')">
                                        <i class="icon-fire icon-white"></i> Remove ADMIN Role and Make PROJECT OFFICER
                                    </button>
                                {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                    <button type="submit" name="revokeOrgPO" value="{$member['id']}" class="btn btn-inverse"
                                            onclick="return confirm('Are you sure you want to revoke PROJECT OFFICER role from this user?')">
                                        <i class="icon-fire icon-white"></i> Remove PROJECT OFFICER Role and Make LINGUIST
                                    </button>
                                {else}
                                    <button type="submit" name="revokeUser" value="{$member['id']}" class="btn btn-inverse"
                                            onclick="return confirm('Are you sure you want to permanently remove this user from Organization?')">
                                        <i class="icon-fire icon-white"></i> Remove LINGUIST Permanently from this Organization
                                    </button>
                                {/if}
                            </td>
                            <td>
                                {if $member['roles'] & $NGO_ADMIN}
                                {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                    <button type="submit" name="makeOrgAdmin" value="{$member['id']}" class="btn btn-success" 
                                            onclick="return confirm('Are you sure you want to make this user an ADMIN of this organization?')"> 
                                            <i class="icon-star icon-white"></i> Create ADMIN
                                    </button>
                                {else}
                                    <button type="submit" name="makeOrgPO" value="{$member['id']}" class="btn btn-success"
                                            onclick="return confirm('Are you sure you want to make this user a PROJECT OFFICER of this organization?')">
                                            <i class="icon-star icon-white"></i> Create PROJECT OFFICER
                                    </button>
                                {/if}
                            </td>
                        {/if}
                      </tr>
                    {/foreach}
                </tbody>
            </table>
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
    {else}
        <p class="alert alert-info">{Localisation::getTranslation('org_public_profile_no_members')}</p>
    {/if}
{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
    <p style="margin-bottom: 40px"></p>
    <h1 class="page-header">Asana Board for Partner</h1>
    <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
        <table>
            <tr>
                <td>
                    <label for="asana_board" style="font-size: large"><strong>Asana ID (not full URL) for this Partner's Board/Project</strong></label>
                    <input type="text" name="asana_board" id="asana_board" maxlength="20" value="{$asana_board_for_org['asana_board']}" style="width: 80%" />
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" value="set_asana_board" name="set_asana_board" class="btn btn-primary">
                        <i class="icon-refresh icon-white"></i> Update Asana ID
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>

    <p style="margin-bottom: 40px"></p>
    <h1 class="page-header">Resources</h1>
    <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
        <table>
            <tr>
                <td>
                    <input type="checkbox" name="mt_for_org" id="mt_for_org" value="1" {if $mt_for_org}checked="checked"{/if} /> Use machine translation in projects for this organization
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" value="set_mt_for_org" name="set_mt_for_org" class="btn btn-primary">
                        <i class="icon-refresh icon-white"></i> Update Resources
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>

    <p style="margin-bottom: 40px" />
    <h1 class="page-header">
        Subscription
        <small>Set or update subscription for this organisation.</small>
    </h1>
    <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
        <table>
            {if $no_subscription}
            <tr>
                <td>
                    <div style="font-size: large"><strong>There is no Subscription for this organisation, set one if desired...<br /><br /></strong></div>
                </td>
            </tr>
            {/if}
            <tr>
                <td>
                    <label for="level" style="font-size: large"><strong>Level</strong></label>
                    <select name="level" id="level" style="width: 82%">
                        <option value="10"   {if $subscription['level'] ==   10}selected="selected"{/if}>Intermittent use for year</option>
                        <option value="20"   {if $subscription['level'] ==   20}selected="selected"{/if}>Moderate use for year</option>
                        <option value="30"   {if $subscription['level'] ==   30}selected="selected"{/if}>Heavy use for year</option>
                        <option value="100"  {if $subscription['level'] ==  100}selected="selected"{/if}>Partner</option>
                        <option value="1000" {if $subscription['level'] == 1000}selected="selected"{/if}>Free because unable to pay</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="start_date" style="font-size: large"><strong>Start Date</strong></label>
                    {if $start_date_error != ''}
                        <div class="alert alert-error">
                            {$start_date_error}
                        </div>
                    {/if}
                    <p>
                        <input class="hasDatePicker" type="text" id="start_date_field" name="start_date_field" value="{$subscription['start_date']}" style="width: 80%" />
                        <input type="hidden" name="start_date" id="start_date" />
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="comment" style="font-size: large"><strong>Comment</strong></label>
                    <input type="text" name="comment" id="comment" maxlength="255" value="{$subscription['comment']|escape:'html':'UTF-8'}" style="width: 80%" />
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" onclick="return validateForm();" value="setSubscription" name="setsubscription" class="btn btn-primary">
                        <i class="icon-refresh icon-white"></i> Update Subscription
                    </button>
                </td>
            </tr>
        </table>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
{/if}

{if 0 & $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
    <p style="margin-bottom: 40px" />
    <h1 class="page-header">
        {Localisation::getTranslation('required_qualification_level')}<br />
        <small>{Localisation::getTranslation('set_default_required_qualification_level')}</small>
    </h1>
    <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
        <select name="required_qualification_level" id="required_qualification_level" style="width: 30%">
            <option value="1" {if $required_qualification_level == 1}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_1')}</option>
            <option value="2" {if $required_qualification_level == 2}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_2')}</option>
            <option value="3" {if $required_qualification_level == 3}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_3')}</option>
        </select>
        <br />
        <button type="submit" value="set_required_qualification_level" name="set_required_qualification_level" class="btn btn-primary" style="width: 30%">
            <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('update_required_qualification_level')}
        </button>
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
{/if}

{include file='footer.tpl'} *}
