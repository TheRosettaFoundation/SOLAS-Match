
{include file='header.tpl'}

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
                    
                    {* <div class="col-md-6">
                        <label for="start_date_field" class="form-label"><strong>Start Date</strong></label>
                        {if $start_date_error != ''}
                            <div class="alert alert-danger">
                                {$start_date_error}
                            </div>
                        {/if}
                        <input class="form-control hasDatePicker" type="text" id="start_date_field" name="start_date_field" value="{$subscription['start_date']}" />
                        <input type="hidden" name="start_date" id="start_date" />
                    </div> *}
                    <div class="col-md-6">
                    <input class="d-none" type="text" id="start_date_sub" name="start_date_sub" value="{$subscription['start_date']}" style="width: 400px" />
                     
                    <label for="start_date_sub" class="form-label"><strong>Start Date</strong></label>
                   
                      
                    <div
                      class="input-group log-event"
                      id="datetimepicker2"
                      data-td-target-input="nearest"
                      data-td-target-toggle="nearest"
                    >
                      <input
                        id="datetimepicker2Input"
                        type="text"
                        class="form-control"
                        data-td-target="#datetimepicker2"
                      />
                      <span
                        class="input-group-text"
                        data-td-target="#datetimepicker2"
                        data-td-toggle="datetimepicker"
                      >
                        <i class="fas fa-calendar"></i>
                      </span>
                    </div>
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

{include file="footer2.tpl"}
