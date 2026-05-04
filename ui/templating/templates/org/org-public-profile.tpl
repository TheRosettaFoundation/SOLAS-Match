{include file="new_header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

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

    {assign var="org_id" value=$org->getId()}

    <div class="container">

        <div class="row align-items-center mt-3 mb-4 g-3">
            <div class="col">
                <h1 class="h3 fw-bold mb-0" style="color: var(--twb-blue);">{TemplateHelper::uiCleanseHTML($org->getName())}</h1>
                <p class="text-muted mb-0 small">Organization Profile</p>
            </div>
            <div class="col-md-auto d-flex gap-2">
                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                    <a href="{urlFor name="ngo_projects" options="org_id.$org_id"}" class="btn btn-twb-primary btn-sm px-3">
                        <i class="fas fa-briefcase me-1"></i> Projects
                    </a>
                {/if}
                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                    <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='btn btn-outline-secondary btn-sm px-3'>
                        <i class="fas fa-cog"></i>
                    </a>
                {/if}
            </div>
        </div>

        <div class="row g-4 mb-5">

            <div class="col-lg-4">
                <div class="twb-card blue-border-top p-4 h-100 text-center">
                    <div class="mb-3">
                        <img src="data:image/jpeg;base64,{$org_image}" class="org-logo-full border" alt="{TemplateHelper::uiCleanseHTML($org->getName())} logo" title="{TemplateHelper::uiCleanseHTML($org->getName())}">
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="twb-card accent-border-top p-4 h-100">
                    <h5 class="fw-bold mb-3">Overview</h5>
                    <p class="text-muted fst-italic">{if $org->getBiography() != NULL}{TemplateHelper::uiCleanseHTMLNewlineAndTabs($org->getBiography())}{/if}</p>

                    <div class="row mt-auto pt-4">
                        <div class="col-sm-6">
                            <label class="small text-uppercase fw-bold text-muted d-block mb-1">Country</label>
                            <span class="fw-semibold">{if $org->getCountry() != NULL}{TemplateHelper::uiCleanseHTML($org->getCountry())}{/if}</span>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <label class="small text-uppercase fw-bold text-muted d-block mb-1">Social Links</label>
                            <div class="fs-5">

                                {if $org->getHomepage() != NULL && $org->getHomepage() != 'https://' && $org->getHomepage() != ''}
                                    <a href="{$org->getHomepage()}"      class="me-3 text-decoration-none" style="color: var(--twb-blue);"><i class="fas fa-globe"></i></a>
                                {/if}

                                {if $org->getCity() != NULL && $org->getCity() != 'https://' && $org->getCity() != ''}
                                    <a href="{$org->getCity()}"          class="me-3 text-decoration-none" style="color: var(--twb-blue);"><i class="fab fa-linkedin"></i></a>
                                {/if}

                                {if $org->getAddress() != NULL && $org->getAddress() != 'https://' && $org->getAddress() != ''}
                                    <a href="{$org->getAddress()}"       class="me-3 text-decoration-none" style="color: var(--twb-blue);"><i class="fab fa-facebook"></i></a>
                                {/if}

                                {if $org->getRegionalFocus() != NULL && $org->getRegionalFocus() != 'https://' && $org->getRegionalFocus() != ''}
                                    <a href="{$org->getRegionalFocus()}" class="text-decoration-none"      style="color: var(--twb-blue);"><i class="fab fa-twitter"></i></a>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
        <div class="mb-5">
            <h4 class="fw-bold mb-3">Packages and subscriptions</h4>
            <div class="twb-card overflow-hidden">
                {if !empty($entitlements)}

                <div class="row g-0 p-3 bg-light border-bottom fw-bold small text-muted d-none d-md-flex">
                    <div class="col-md-4">PACKAGE NAME</div>
                    <div class="col-md-4">VALIDITY PERIOD</div>
                    <div class="col-md-4">ALLOCATION & USAGE</div>
                </div>

                {foreach $entitlements as $entitlement}
                <div class="row g-0 p-3 align-items-center border-bottom">
                    <div class="col-md-4 fw-bold text-dark">{if $entitlement['service'] == 0}Translation{else}Other{/if}{if $entitlement['inactive']} (Inactive){/if}</div>
                    <div class="col-md-4 text-muted small">{substr($entitlement['validity_start'], 0, 10)} - {substr($entitlement['validity_end'], 0, 10)}</div>
                    <div class="col-md-4">
                        {if $entitlement['limit_type']}
                            <span class="small text-muted">Unlimited</span>
                        {else}
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar" style="width: {min($entitlement['metric_used']/$entitlement['limit_value']*100, 100)}%; background-color: var(--twb-blue);"></div>
                            </div>
                            <span class="small text-muted">{$entitlement['metric_used']} / {$entitlement['limit_value']} words used</span>
                        {/if}
                    </div>
                </div>
                {/foreach}

                {else}
                <div fw-bold text-dark>There are no subscriptions for this organization.</div>
                {/if}

            </div>
        </div>
        {/if}

        {if $roles&($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0 text-dark">Organization Members</h4>
                    <a href="{urlFor name="invite_admins" options="org_id.$org_id"}" class='btn btn-outline-secondary btn-sm rounded-pill px-4'>
                        <i class="fas fa-user-plus me-1"></i> Add member
                    </a>
                </div>

                {if !empty($orgMembers)}
                    <div class="row g-4">
                        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">

                        {foreach [1, 0] as $display_admins}
                        {foreach $orgMembers as $member}
                        {if $display_admins && $member['roles']&($NGO_ADMIN + $NGO_PROJECT_OFFICER) || !$display_admins && !($member['roles']&($NGO_ADMIN + $NGO_PROJECT_OFFICER))}
                            {if $roles&($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER) || $org_id != 707 || $member['source_of_user']}
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="twb-card member-card shadow-sm border-0">
                                    <div>
                                        <div class="member-role">
                                            {if $member['roles']&$NGO_ADMIN}ADMINISTRATOR
                                            {elseif $member['roles']&$NGO_PROJECT_OFFICER}PROJECT OFFICER
                                            {else}LINGUIST{if !($member['roles'] & $LINGUIST)} (exclusive){/if} {$member['language_pairs']}
                                            {/if}
                                        </div>
                                        <div class="member-name">{TemplateHelper::uiCleanseHTML($member['first_name'])|capitalize} {TemplateHelper::uiCleanseHTML($member['last_name'])|capitalize}</div>
                                        <div class="member-email text-truncate">{$member['email']}</div>
                                    </div>

                                    {if $roles&($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN)}
                                    <div class="dropdown">
                                        <a class="btn btn-twb-outline btn-sm w-100 rounded-pill py-2 fw-bold dropdown-toggle no-caret" href="#" id="hover_drop_down" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage Member
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="hover_drop_down">

                                            {if $member['roles'] & $NGO_ADMIN}
                                            <li><button type="submit" name="revokeOrgAdmin" value="{$member['id']}" class="btn btn-inverse"
                                                    onclick="return confirm('Are you sure you want to revoke ADMIN role from this user?')">
                                                <i class="icon-fire icon-white"></i> Remove ADMIN Role and Make PROJECT OFFICER
                                            </button></li>

                                            {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                            <li><button type="submit" name="revokeOrgPO" value="{$member['id']}" class="btn btn-inverse"
                                                    onclick="return confirm('Are you sure you want to revoke PROJECT OFFICER role from this user?')">
                                                <i class="icon-fire icon-white"></i> Remove PROJECT OFFICER Role and Make LINGUIST
                                             </button></li>
                                            {/if}

                                            <li><button type="submit" name="revokeUser" value="{$member['id']}" class="btn btn-inverse"
                                                    onclick="return confirm('Are you sure you want to permanently remove this user from Organization?')">
                                                <i class="icon-fire icon-white"></i> Remove user Permanently from this Organization
                                            </button></li>

                                            {if $member['roles'] & $NGO_ADMIN}
                                            {elseif $member['roles'] & $NGO_PROJECT_OFFICER}
                                            <li><button type="submit" name="makeOrgAdmin" value="{$member['id']}" class="btn btn-success"
                                                    onclick="return confirm('Are you sure you want to make this user an ADMIN of this organization?')">
                                                <i class="icon-star icon-white"></i> Create ADMIN
                                            </button></li>

                                            {else}
                                            <li><button type="submit" name="makeOrgPO" value="{$member['id']}" class="btn btn-success"
                                                    onclick="return confirm('Are you sure you want to make this user a PROJECT OFFICER of this organization?')">
                                                <i class="icon-star icon-white"></i> Create PROJECT OFFICER
                                            </button></li>
                                            {/if}

                                        </ul>
                                    </div>
                                    {/if}

                                </div>
                            </div>
                            {/if}
                        {/if}
                        {/foreach}
                        {/foreach}

                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
                    </div>
                    <a href="{urlFor name="org_members" options="org_id.$org_id"}">Download Organization Members</a>
                {/if}
            </div>
        {/if}

        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
            <div class="bg-body-tertiary border border-2 rounded-4 p-4 p-md-5 mb-5 shadow-inner">
                <h4 class="fw-bold mb-4" style="color: var(--twb-blue);"><i class="fas fa-user-shield me-2"></i>Admin Section</h4>
                <div class="row g-4">

                    <div class="col-md-4">
                        <div class="twb-card p-4 h-100 border-0 shadow-sm">
                            <h6 class="fw-bold mb-3">Work Report</h6>
                            <p class="small text-muted mb-4">Access detailed financial and activity reports for this organization.</p>
                            <a href="{urlFor name="partner_deals" options="org_id.$org_id"}" target="_blank" class="btn btn-info btn-sm w-100 text-white fw-bold">View Report</a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="twb-card p-4 h-100 border-0 shadow-sm">
                            <h6 class="fw-bold mb-3">Asana Board</h6>
                            <p class="small text-muted mb-3">Enter the partner's Asana ID for their board/project below (not full URL).</p>
                            <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                                <div class="input-group">
                                    <input type="text" name="asana_board" id="asana_board" class="form-control form-control-sm" placeholder="e.g. 12048593" value="{$asana_board_for_org['asana_board']}">
                                    <button type="submit" value="set_asana_board" name="set_asana_board" class="btn btn-twb-primary btn-sm">Update</button>
                                </div>
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="twb-card p-4 h-100 border-0 shadow-sm">
                            <h6 class="fw-bold mb-3">Resources</h6>
                            <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" name="mt_for_org" id="mt_for_org" value="1" {if $mt_for_org}checked="checked"{/if}>
                                    <label class="form-check-label small fw-semibold" for="mt_for_org">Enable Machine Translation</label>
                                </div>
                                <button type="submit" value="set_mt_for_org" name="set_mt_for_org" class="btn btn-twb-primary btn-sm">Update</button>
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="twb-card p-4 h-100 border-0 shadow-sm">
                            <h6 class="fw-bold mb-3">Organization Image</h6>
                            <p class="small text-muted mb-3">JPEG, PNG or WEBP, but only JPEG will be resized to a suitable size.</p>
                            <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" enctype="multipart/form-data">
                                <div class="input-group">
                                    <input type="file" name="org_image" accept="image/jpeg" class="form-control form-control-sm" />
                                    <button type="submit" value="set_image_for_org" name="set_image_for_org" class="btn btn-twb-primary btn-sm">Update</button>
                                </div>
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        {/if}

    </div>

{include file="footer2.tpl"}
