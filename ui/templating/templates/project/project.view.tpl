{include file="new_header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

<div class="container-fluid ">
    <div class=" container py-4" >
                    <a  class="text-decoration-none text-body fw-bold"  href="/"> Home </a> <i class="fa-solid fa-chevron-right mx-1"> </i>
                    <a  href="#" class="text-primaryDark fw-bold text-decoration-none"> Project </a>       
    </div>


<section class="bg-light-subtle"> 

        <div class="container py-5 ">


            <div class="row">

               <div class="fw-bold primaryDark fs-3  col-md-5 text-break text-wrap">

                        <span class="d-none">
                    <!-- Parameters... -->
                        <div id="isSiteAdmin">{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}1{else}0{/if}</div>
                    </span>

                    <span style="height: auto; overflow-wrap: break-word; display: inline-block;">
                     {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}
                    <small class="text-light">{Localisation::getTranslation('project_view_overview_of_project_details')}</small>
                    </span>

                </div>


            {assign var="project_id" value=$project->getId()}
             <div class="col-md-7">
                    <div class="d-flex justify-content-sm-end">     

                    <form id="copyChunksProjectForm" class="d-flex flex-wrap" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                    {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER)) && !empty($matecat_analyze_url)}
                        <input type="hidden" name="copyChunks" value="1" />
                        <a class="btnSuccess mt-2 mt-md-0 me-2" onclick="$('#copyChunksProjectForm').submit();" >
                            <i class="fa-upload fa-solid me-1"></i> Sync Phrase TMS
                        </a>
                        <a href="{$matecat_analyze_url}" class="btnPrimary text-white mt-2 mt-md-0 me-2 " target="_blank">
                            {if !empty($memsource_project)}Phrase TMS Project{else}Kató TM analysis{/if}
                        </a>
                    {/if}
                    {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)) && !empty($memsource_project)}
                        <a href="{urlFor name="project-add-shell-tasks" options="project_id.$project_id"}" class="btnPrimary text-white mt-2 mt-md-0 me-2">
                            Add Shell Tasks
                        </a>
                    {/if}
                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                        <a href="{urlFor name="project-alter" options="project_id.$project_id"}" class='btnPrimary text-white mt-2 mt-md-0 '>
                        <i class="fa-solid fa-screwdriver-wrench me-2 "></i> {Localisation::getTranslation('common_edit_project')}
                        </a> 
                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                    </div>
            </div>
        </div>

        {if isset($flash['success'])}
            <p class="alert alert-success mt-2">
                {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
            </p>
        {/if}

        {if isset($flash['error'])}
            <p class="alert alert-warning mt-2">
                {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
            </p>
        {/if}

        <div class="bg-body p-2 border-secondary rounded-top-3 mt-2">
            <div class="table-responsive mt-4  ">   
        <table class="table table-borderless">
        <thead class="fs-5 align-middle">            
            <th style="text-align: left;"><strong>{Localisation::getTranslation('common_organisation')}</strong></th>
            <th>{Localisation::getTranslation('common_source_language')}</th>
            <th>{Localisation::getTranslation('common_reference')}</th>
            <th>{Localisation::getTranslation('common_word_count')}</th>
            <th>{Localisation::getTranslation('common_created')}</th>
            <th>{Localisation::getTranslation('project_view_project_deadline')}</th>
            {if isset($userSubscribedToProject)}
                <th>{Localisation::getTranslation('common_tracking')}</th>
            {/if}
        </thead>
        <tbody class="fs-4">
            <tr >
                <td >
                    {if isset($org)}
                        {assign var="org_id" value=$org->getId()}
                        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="custom-link">{$org->getName()|escape:'html':'UTF-8'}</a>
                    {/if}
                </td>
                <td>
                    {TemplateHelper::getTaskSourceLanguage($project)}
                </td>
                <td>
                    {if $project->getReference() != ''}
                        <a target="_blank" href="{TemplateHelper::uiCleanseHTML($project->getReference())}">{TemplateHelper::uiCleanseHTML($project->getReference())}</a>
                    {else}
                        -
                    {/if}
                </td>
                <td>
                    <span class="d-none">
                        <div id="siteLocationURL">{Settings::get("site.location")}</div>
                        <div id="project_id_for_updated_wordcount">{$project_id}</div>
                    </span>
                    <div id="put_updated_wordcount_here">{if $project->getWordCount() != '' && $project->getWordCount() > 1}{$project->getWordCount()}{else}-{/if}</div>
                </td>
                <td>
                    <div class="convert_utc_to_local" style="visibility: hidden">{$project->getCreatedTime()}</div><br />{$pm}
                </td>  
                <td>
                    <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$project->getDeadline()}</div>
                </td>
                
                {if isset($userSubscribedToProject)}
                    <td>

                        <form id="trackedProjectForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                             {if $userSubscribedToProject}
                                <p>
                                    <input type="hidden" name="trackProject" value="0" />
                                    <a class=" btngray mt-2" onclick="$('#trackedProjectForm').submit();" >
                                         <i class="fa-solid fa-ban fa-lg"></i>
                                         {Localisation::getTranslation('project_view_untrack_project')}
                                    </a>
                                </p>
                            {else}
                                <p>
                                    <input type="hidden" name="trackProject" value="1" />
                                    <a class=" btngray mt-2" onclick="$('#trackedProjectForm').submit();" >
                                         <i class="fa-solid fa-envelope fa-lg"></i>
                                         {Localisation::getTranslation('common_track_project')}
                                    </a>
                                </p>
                            {/if}
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
                    </td>
                {/if}
            </tr>
            <tr>
            </tr> 
        </tbody>
    </table>    
    </div>  
    </div>

    {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER)) && $one_paid}
    <div class="bg-body p-2 border-secondary rounded-top-3 mt-4">
    <div class="table-responsive mt-4  ">
    <table class="table table-borderless" >
        <thead class="fs-5">
            <th>Deal ID</th>
            <th>Project Price</th>
            <th>Allocated Budget</th>
            <th>Project Cost</th>
            <th>Remaining Budget</th>
            <th>Waived Tasks (In-kind, In-house, waived)</th>
        </thead>
        <tbody class="fs-4">
            <tr style="overflow-wrap: break-word;">
                <td>{if $project_complete_date['deal_id'] > 0}<a href="{urlFor name="deal_id_report" options="deal_id.{$project_complete_date['deal_id']}"}" class="custom-link" target="_blank">{$project_complete_date['deal_id']}</a>{else}{$project_complete_date['deal_id']}{/if}</td>
                <td><div >${round($total_expected_price, 2)}</div></td>
                <td>${round($project_complete_date['allocated_budget'], 2)}</td>
                <td>${round($total_expected_cost, 2)}</td>
                <td>${round($project_complete_date['allocated_budget'] - $total_expected_cost, 2)}</td>
                <td>${round($total_expected_cost_waived, 2)}</td>
            </tr>
        </tbody>
    </table>
    </div>
    </div>
    {/if}

       <div class="d-flex justify-content-between flex-wrap">
     <div class="bg-body p-2 border-secondary rounded-top-3 mt-4 flex-grow-1 me-md-2">
        <div class="table-responsive mt-4  ">   
        <table class="table table-borderless ">
            <thead class="fs-5">
            <th >{Localisation::getTranslation('common_description')}</th>
            <th></th>   
            </thead>
            <tbody class="fs-4 ">
                <tr >
                    <td>
                        {if $project->getDescription() != ''}
                            <div class="displayF">{TemplateHelper::clean_project_description($project->getDescription())}</div>
                        {else}
                            {Localisation::getTranslation('common_no_description_has_been_listed')}
                        {/if}  
                    </td>
                    <td></td>
                    
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
                
                 <tr>
                    <td colspan="2">
                        <strong>{Localisation::getTranslation('common_impact')}</strong>
                    </td>
                </tr>
                <tr>                
                    <td  colspan="2">
                        <i>
	                        {if $project->getImpact() != ''}
                              {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getImpact())}
	                        {else}
	                            {Localisation::getTranslation('No impact has been listed')}
	                        {/if}  
                        </i> 
                    </td>                
                </tr>
                <tr>
                    <td colspan="2"> </td>
                </tr>
                
                {if $project_id > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $project->getTitle())}
                <tr>
                    <td colspan="2" style="padding-bottom: 40px"></td>
                </tr>
                <tr valign="top">
                    <td colspan="2">
                        <strong>Questions or Comments on this project?</strong>
                    </td>
                </tr>
                <tr>
                    <td >
                        <a href="https://community.translatorswb.org/t/{$discourse_slug}" class=" btngray" target="_blank">Discuss project</a>
                    </td>
                </tr>
                {/if}
            </tbody>
        </table>
    </div>   
    </div>



  <div class="bg-body p-2 border-secondary rounded-top-3 mt-4 flex-grow-1 ms-md-2">
    <div class="table-responsive mt-4  ">
        <table class="table table-borderless">
            <thead class="fs-5">
        
            <th >{Localisation::getTranslation('common_project_image')}</th>
            </thead>
            <tbody class="fs-4">
                <tr class="p-4">
                    <td>
                        {if $project->getImageUploaded()}
                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
                                <div>
                                    <img class="mb-4" src="{urlFor name="download-project-image" options="project_id.$project_id"}?{$imgCacheToken}"/>
                                    {if !$project->getImageApproved()}
                                        <form id="projectImageApproveForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="imageApprove" value="0" />
                                            <a class="btngray" onclick="$('#projectImageApproveForm').submit();">
                                                {* <img src="{urlFor name='home'}ui/img/check.svg" class="approve" /> *}
                                                <i class="fa-regular fa-circle-check fa-lg me-2"></i>
                                                {Localisation::getTranslation('project_view_image_approve')}
                                            </a>
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    {else}
                                        <form id="projectImageApproveForm" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="imageApprove" value="1" />
                                            <a class=" btngray" onclick="$('#projectImageApproveForm').submit();"">
                                                <i class="fa-regular fa-circle-xmark fa-lg me-2"></i>
                                                {Localisation::getTranslation('project_view_image_disapprove')}
                                            </a>
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    {/if}
                                </div>
                            {else}
                                {if $project->getImageApproved()}
                                    <img class="project-image" src="{urlFor name="download-project-image" options="project_id.$project_id"}?{$imgCacheToken}"/>
                                {else}
                                    {Localisation::getTranslation('common_project_image_not_approved')}
                                {/if}
                            {/if}
                        {else}
                            {Localisation::getTranslation('common_project_image_not_uploaded')}
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{Localisation::getTranslation('common_tags')}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="nav nav-list unstyled" style="padding-left: 0px; padding-right: 0px;" colspan="2">
                    {if isset($project_tags) && is_array($project_tags)}
                        {foreach $project_tags as $ptag}
                            {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($ptag->getLabel())}
                            {assign var="tagId" value=$ptag->getId()}
                            <a class=" btngray me-2" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
                        {/foreach}
                    {else}
                        <i>{Localisation::getTranslation('common_there_are_no_tags_associated_with_this_project')}</i>                    
                    {/if}
                    </td>                
                </tr>

            </tbody>
        </table>

    </div>   
    </div>


    
    </div> 





    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}


        <div class="sticky-top bg-light-subtle d-flex justify-content-between mt-4 mb-4 flex-wrap align-items-center">

            <h3 class="fw-bold flex-grow-1 align-middle">{Localisation::getTranslation('project_view_tasks')}
                <small class="text-muted text-sm">{Localisation::getTranslation('project_view_0')}</small>
            </h3>
            

                {if !empty($memsource_project)}
                <div class="d-flex mt-2">
                    <select name="task_options" id="task_options" class="form-select me-1 text-muted">
                        <option value="">-- Choose --</option>
                        <option value="all_tasks">Select all Tasks</option>
                        <option value="all_translation_tasks">Select all Translation Tasks</option>
                        <option value="all_revision_tasks">Select all Revision Tasks</option>
                        <option value="all_revtrans_tasks">Select all Translation and Revision</option>
                        <option value="all_approval_tasks" id="all_approval_tasks">Select all Approval Tasks</option>
                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
                        <option value="all_paid_tasks" id="all_paid_tasks">Select all Paid Tasks</option>
                        <option value="all_tasks_ready_payment" id="all_tasks_ready_payment">Select all Tasks Ready for Payment</option>
                        {/if}
                        <option value="delesect_all">Deselect all</option>
                    </select>
        
                    <button class="menu_open btn  btn-primary text-white d-flex align-items-center" type="button" >
                   <span class="me-1">...</span><i class="fa-solid fa-caret-down fa-xs"></i>
                 </button>
         </div>
        {/if}

     <br />
     <br />

        <div class="menu_list d-none bg-body p-4 mt-4 mb-4 rounded-2">
        <div class="">
        <div class="d-flex flex-wrap mb-4">
        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
            <form id="publish_selected_tasks" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0 mb-sm-2 mb-md-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="d-flex fs-6 text-muted text-decoration-none p-1" onclick="$('#publish_selected_tasks').submit();" >
                    <i class="fa-check fa-regular me-2"></i> <span>Publish Selected Tasks</span>
                </a>
                <input type="hidden" name="publish_selected_tasks" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="unpublish_selected_tasks" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0 mb-sm-2 mb-md-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class=" d-flex text-muted fs-6 p-1 text-decoration-none" onclick="$('#unpublish_selected_tasks').submit();" >
                    <i class="fa-solid fa-xmark me-2" ></i> <span>Unpublish Selected Tasks</span>
                </a>
                <input type="hidden" name="unpublish_selected_tasks" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        {/if}

        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
            <form id="tasks_as_paid" class="bg-light-subtle d-flex flex-column  justify-content-center form_action me-2  mb-4 mb-lg-0 mb-sm-2 mb-md-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="d-flex text-muted p-1 fs-6 text-decoration-none" onclick="$('#tasks_as_paid').submit();" >
                    <i class="fa fa-usd me-2"  aria-hidden="true"></i><span>Mark Selected Tasks as Paid</span>
                </a>
                <input type="hidden" name="tasks_as_paid" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="tasks_as_unpaid" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="d-flex text-muted p-1 fs-6 text-decoration-none" onclick="$('#tasks_as_unpaid').submit();" >
                    <i class="fa fa-strikethrough me-2" aria-hidden="true"></i> <span>Mark Selected Tasks as Unpaid</spam>
                </a>
                <input type="hidden" name="all_as_paid1" value="1" />
                <input type="hidden" name="tasks_as_unpaid" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

      
      {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
  </form>
        {/if}
        </div>

        <div class="d-flex mb-4 flex-wrap">
        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)}
            <form id="status_as_unclaimed" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="d-flex text-muted p-1 fs-6 text-decoration-none" onclick="$('#status_as_unclaimed').submit();" >
                    <i class="fa fa-unlock me-2"  aria-hidden="true"></i> <span>Set Status of Selected to Unclaimed</span>
                </a>
                <input type="hidden" name="status_as_unclaimed" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="status_as_waiting" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="d-flex p-1 text-muted fs-6 text-decoration-none" onclick="$('#status_as_waiting').submit();">
                    <i class="fa fa-pause me-2"  aria-hidden="true"></i> <span>Set Status of Selected to Waiting </span>
                </a>
                <input type="hidden" name="status_as_waiting" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="complete_selected_tasks" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="d-flex text-muted p-1 fs-6 text-decoration-none" onclick="$('#complete_selected_tasks').submit();">
                <i class="fa-solid fa-check me-2 "></i> <span>Set Shell Tasks Status&nbsp;&nbsp;Complete</span>
                </a>
                <input type="hidden" name="complete_selected_tasks" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>

            <form id="uncomplete_selected_tasks" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                <a class="d-flex p-1 text-muted fs-6 text-decoration-none" onclick="$('#uncomplete_selected_tasks').submit();">
                <i class="fa-solid fa-pause me-2 "></i><span>Set Shell Tasks Status In Progress</span>
                </a>
                <input type="hidden" name="uncomplete_selected_tasks" value="" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
            <br />
             
            <div class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0">
            <a class=" d-flex p-1 text-muted fs-6 text-decoration-none open-cancel-modal p-1" data-bs-toggle="modal" data-id="1" href="#cancelmodal" role="button" data-cancelled="1">
                <i class="fa fa-ban me-2"  aria-hidden="true"></i> <span>Set Selected Tasks to Cancelled</span>
            </a>
            </div>

            <form id="cancel" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" style="margin-bottom: 2px;">
            <a class=" d-flex p-1 text-muted fs-6 text-decoration-none p-1" onclick="$('#cancel').submit();"   data-id="0" role="button" data-cancelled="0">
                <i class="fa fa-check-square me-2"  aria-hidden="true"></i> <span>Set Selected Tasks to Uncancelled</span>
            </a>
                <input type="hidden" name="cancel" value="" />
                <input type="hidden" name="cancelled" value="0" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
             </form>
        {/if}
        </div>
        <div class="d-flex mt-4 flex-wrap">
        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
            <div class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0">
            <a class="d-flex p-1 text-muted fs-6 text-decoration-none open-ponum-modal"  data-bs-toggle="modal" href="#ponummodal" role="button">
            <i class="fa fa-credit-card me-2"  aria-hidden="true"></i> <span> Set Purchase Order # </span>
            </a>
            </div>

            <form id="ready_payment" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" style="margin-bottom: 2px;">
            <a class="d-flex p-1 text-muted fs-6 text-decoration-none" onclick="$('#ready_payment').submit();" style="color:#000000;" role="button">
                <i class="fa fa-money me-2" aria-hidden="true"></i> <span>Set tasks to Ready for Payment</span>
            </a>
                <input type="hidden" name="ready_payment" value="" />
                <input type="hidden" name="ready_payment_status" value="Ready for payment" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
            <form id="pending_documentation" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" style="margin-bottom: 2px;">
            <a class="d-flex p-2 text-muted fs-6 text-decoration-none" onclick="$('#pending_documentation').submit();" style="color:#000000;" role="button">
                <i class="fa fa-book me-2"  aria-hidden="true"></i> <span> Set tasks to Pending Documentation </span>
            </a>
                <input type="hidden" name="pending_documentation" value="" />
                <input type="hidden" name="ready_payment_status" value="Pending documentation" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
            <form id="tasks_settled" class="bg-light-subtle d-flex flex-column justify-content-center form_action me-2  mb-4 mb-lg-0" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" >
            <a class="d-flex  p-1 text-muted fs-6 text-decoration-none" onclick="$('#tasks_settled').submit();" style="color:#000000;" role="button">
                <i class="fa fa-check-square me-2"  aria-hidden="true"></i><span> Set tasks to Settled </span>
            </a>
                <input type="hidden" name="tasks_settled" value="" />
                <input type="hidden" name="ready_payment_status" value="Settled" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        {/if}
        </div>
        </div>
</div>
<br />
<br />

<hr />
  
    </div>

    {if isset($flash['taskSuccess'])}
        <div class="alert alert-success alert-dismissible fade show mt-2">
            <span>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['taskSuccess'])}</span>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    {else if isset($flash['taskError'])}
        <div class="alert alert-warning alert-dismissible fade show mt-2">
            <span>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['taskError'])}</span>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    {/if}      

        <div class="mt-4">
        <div>
            {if isset($projectTasks) && count($projectTasks) > 0}
                {foreach from=$taskLanguageMap key=languageCountry item=tasks}
                  <div class="mt-4">
                    <div class="d-flex align-items-center flex-wrap">
                    <span class="me-4 fw-bold">
                        {TemplateHelper::getLanguageAndCountryFromCode($languageCountry)}
                    </span>
                    <span class="me-2">
                        <select name="language_options[]" id="language_options" class="form-select me-1 text-muted" id="language_options" data-select-name="{$languageCountry|replace:',':'_'}">
                            <option value="">-- Choose --</option>
                            <option value="all_tasks_{$languageCountry|replace:',':'_'}">Select all Tasks</option>
                            <option value="all_translation_tasks_{$languageCountry|replace:',':'_'}">Select all Translation Tasks</option>
                            <option value="all_revision_tasks_{$languageCountry|replace:',':'_'}">Select all Revision Tasks</option>
                            <option value="all_revtrans_tasks_{$languageCountry|replace:',':'_'}">Select all Translation and Revision</option>
                            <option value="all_approval_tasks_{$languageCountry|replace:',':'_'}" class="all_approval_tasks_lang">Select all Approval Tasks</option>
                            <option value="delesect_all_{$languageCountry|replace:',':'_'}">Deselect all</option>
                        </select>
                    </span>

                    {* //Modal *}
                      <div>
                        <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                          <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                          <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Tasks Restrictions </h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body py-4">
                                <div class="d-flex">
                        <form id="get_translators_count_availability" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                  <button class="btn btn-primary text-white mt-2 mt-md-0 restrictions" data-bs-target="#exampleModalToggle" data-bs-toggle="modal">Restrict Task</button>

                      </div>
                    {* endofModal *}
                    </div>

                    <div class="bg-body p-2 border-secondary mt-4">
                    <div class="table-responsive mt-4 ">
                    <table class="table " >
                        <thead class="fs-5">
                            <tr>
                                 <th><input type="checkbox" name="select_all_tasks" data-lang="{$languageCountry|replace:',':'_'}" /></th>
                                 <th class="text-center">{Localisation::getTranslation('common_title')}</th>
                                 <th class="text-center">{Localisation::getTranslation('common_status')}</th>
                                 <th class="text-center">{Localisation::getTranslation('common_type')}</th>
                                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
                                 <th class="text-center">Paid?</th>
                                {/if}
                                 <th class="text-center">Cancelled?</th>
                                 <th class="text-center">{Localisation::getTranslation('common_task_deadline')}</th>
                                 <th class="text-center">Published?</th>
                                 <th class="text-center">{Localisation::getTranslation('common_tracking')}</th>
                                 <th class="text-center">{Localisation::getTranslation('common_edit')}</th>
                                 <th class="text-center">{Localisation::getTranslation('project_view_archive_delete')}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-4 bg-primary">

                            {foreach from=$tasks item=task}
                                {assign var="task_id" value=$task->getId()}
                                <tr class="align-middle">
                                <td class="text-center"> <input type="checkbox"  name="select_task" value="{$task->getId()}" data-task-type="{$task->getTaskType()}" data-lang="{$languageCountry|replace:',':'_'}" data-paid="{$get_paid_for_project[$task_id]}" data-payment-status="{$get_payment_status_for_project[$task_id]['payment_status']}" /> </td>
                                    <td class="text-center">
                                        <a class="custom-link" href="{urlFor name="task-view" options="task_id.$task_id"}">
                                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
                                        </a>
                                        <br />
                                    </td>
                                    <td id={$task->getId()} class="text-center">
                                        {assign var="status_id" value=$task->getTaskStatus()}
                                        {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                            <span>{Localisation::getTranslation('common_waiting')}</span><br />
                                            <div class="text-secondary-sublte fs-6 fw-bold ">
                                              {if $get_payment_status_for_project[$task_id]['native_matching'] == 0}
                                              <span> </span>
                                              {elseif $get_payment_status_for_project[$task_id]['native_matching'] == 1}
                                              <span data-bs-toggle="tooltip" data-bs-placement="top" class="mt-2"
                                              data-bs-custom-class="custom-tooltip"
                                              data-bs-title="Matching Native Language"><img src="{urlFor name='home'}ui/img/Native lm.svg" alt="Matching Native Language icon" width="20%" height="20%" /> </span> 
                                              {elseif $get_payment_status_for_project[$task_id]['native_matching'] == 2}
                                              <span data-bs-toggle="tooltip" data-bs-placement="top" class="mt-2"
                                              data-bs-custom-class="custom-tooltip"
                                              data-bs-title="Matching Native Language and Variant"> <img src="{urlFor name='home'}ui/img/Native lcm.svg" alt="Matching Native Language and Variant icon" width="20%" height="20%" /> </span> 
                                              {/if}
                                            </div>

                                        {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                            <span>{Localisation::getTranslation('common_unclaimed')}</span>
                                            <div class="text-secondary-sublte fs-6 fw-bold">
                                              {if $get_payment_status_for_project[$task_id]['native_matching'] == 0}
                                                <span> </span>
                                                {elseif $get_payment_status_for_project[$task_id]['native_matching'] == 1}
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="mt-2"
                                                data-bs-custom-class="custom-tooltip"
                                                data-bs-title="Matching Native Language"> <img src="{urlFor name='home'}ui/img/Native lm.svg" alt="Matching Native Language icon" width="20%" height="20%"/> </span>
                                                {elseif $get_payment_status_for_project[$task_id]['native_matching'] == 2}
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="mt-2"
                                                data-bs-custom-class="custom-tooltip"
                                                data-bs-title="Matching Native Language and Variant"><img src="{urlFor name='home'}ui/img/Native lcm.svg" alt="Matching Native Language and Variant icon" width="20%" height="20%"/> </span>
                                              {/if}
                                            </div>

                                        {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                          {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                                            <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">
                                                {Localisation::getTranslation('common_in_progress')}
                                            </a><br />
                                          {else}
                                                {Localisation::getTranslation('common_in_progress')}<br />
                                          {/if}
                                            {$user_id = $users_who_claimed[$task_id]['user_id']}
                                            <i class=" fa-solid fa-user "></i> <a  href="{urlFor name="user-public-profile" options="user_id.$user_id"}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Task claimed by {$users_who_claimed[$task_id]['display_name']}">{TemplateHelper::uiCleanseHTML($users_who_claimed[$task_id]['display_name'])}</a>
                                                {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)) && TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']}
                                                    <form id="complete_form_{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                                        <input type="hidden" name="task_id" value="{$task_id}" />
                                                        <input type="hidden" name="complete_task" value="1" />
                                                        <a class="  btn btn-sm btn-dark-subtle border border-dark-subtle " onclick="$('#complete_form_{$task_id}').submit();" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="Set Status Complete">
                                                        <img src="{urlFor name='home'}ui/img/check.svg" alt="check" >
                                                        </a>
                                                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                                    </form>
                                                {/if}
                                        {elseif $status_id == TaskStatusEnum::CLAIMED}
                                          {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                                            <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}" class="custom-link">
                                                Claimed
                                            </a><br />
                                          {else}
                                                Claimed<br />
                                          {/if}
                                            {if !empty($users_who_claimed[$task_id])}
                                                {$user_id = $users_who_claimed[$task_id]['user_id']}
                                             <i class=" fa-solid fa-user "></i>   <a style="color:#000000;" href="{urlFor name="user-public-profile" options="user_id.$user_id"}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Task claimed by {$users_who_claimed[$task_id]['display_name']}">{TemplateHelper::uiCleanseHTML($users_who_claimed[$task_id]['display_name'])}</a>
                                                {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)) && TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']}
                                                    <form id="complete_form_{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                                        <input type="hidden" name="task_id" value="{$task_id}" />
                                                        <input type="hidden" name="complete_task" value="1" />
                                                        <a class="  btn btn-sm btn-dark-subtle border border-dark-subtle" onclick="$('#complete_form_{$task_id}').submit();" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Set Status Complete">
                                                        <i class=" fa-solid fa-check "></i> 
                                                        </a>
                                                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                                    </form>
                                                {/if}
                                            {/if}
                                        {elseif $status_id == TaskStatusEnum::COMPLETE}
                                            {assign var="org_id" value=$project->getOrganisationId()}
                                            <a href="{urlFor name="org-task-complete" options="task_id.$task_id|org_id.$org_id"}">
                                                {Localisation::getTranslation('common_complete')}
                                            </a>
                                            {if !TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']}
                                            <br />
                                            <a class="btn btn-primary text-white" target="_blank" href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download Output File">
                                            <i class=" fa-solid fa-download "></i> 
                                            </a>
                                            {/if}
                                            <br />
                                            {$user_id = $users_who_claimed[$task_id]['user_id']}
                                            <i class=" fa-solid fa-user "></i>    <a  href="{urlFor name="user-public-profile" options="user_id.$user_id"}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Task claimed by {$users_who_claimed[$task_id]['display_name']}">{TemplateHelper::uiCleanseHTML($users_who_claimed[$task_id]['display_name'])}</a>
                                        {/if}
                                    </td>
                                    <td class="text-center">
                                        <strong>
                                            <small>                                  
                                                {assign var="type_id" value=$task->getTaskType()}
                                                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                                    {if $type_id == $task_type}
                                                        <span style="color: {$ui['colour']}">{$ui['type_text']}</span>{if $ui['shell_task']}<br />{$ui['type_category_text']}{/if}
                                                    {/if}
                                                {/foreach}
                                            </small>
                                        </strong>
                                        {if $get_payment_status_for_project[$task_id]['total_words']}<br />{round($get_payment_status_for_project[$task_id]['total_words'], 2)} {$get_payment_status_for_project[$task_id]['pricing_and_recognition_unit_text_hours']}{/if}
                                    </td>
                                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
                                    <td class="text-center">
                                     {if $get_paid_for_project[$task_id] == 1}
                                         {if $get_payment_status_for_project[$task_id]['payment_status'] == 'Unsettled'}
                                          <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Unsettled">PO#: {$get_payment_status_for_project[$task_id]['purchase_order']} <i class="fa fa-solid fa-x" ></i> </span>
                                         {elseif $get_payment_status_for_project[$task_id]['payment_status'] == 'Ready for payment'}
                                          <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Ready for payment">PO#: {$get_payment_status_for_project[$task_id]['purchase_order']} <i class="fa fa-money" style="font-size: 15px !important;padding:0 !important;width:12px !important;margin-left:-2px;"></i> </span>
                                         {elseif $get_payment_status_for_project[$task_id]['payment_status'] == 'Pending documentation'}
                                          <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Pending documentation">PO#: {$get_payment_status_for_project[$task_id]['purchase_order']} <i class="fa fa-book" style="font-size: 15px !important;padding:0 !important;width:12px !important;margin-left:-2px;" ></i> </span>
                                         {elseif $get_payment_status_for_project[$task_id]['payment_status'] == 'Settled'}
                                          <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Settled">PO#: {$get_payment_status_for_project[$task_id]['purchase_order']} <i class="fa fa-check-circle" style="font-size: 15px !important;padding:0 !important;width:12px !important;margin-left:-2px;" ></i> </span> </span>
                                          {else}
                                          PO#: {$get_payment_status_for_project[$task_id]['purchase_order']}<br />{$get_payment_status_for_project[$task_id]['payment_status']} 
                                          {/if}
                                         <br />${round($get_payment_status_for_project[$task_id]['total_expected_cost'], 2)}
                                     {else}
                                         <span>-</span>
                                     {/if}
                                    </td>
                                    {/if}

                                    <td class="text-center">
                                     {if $task->get_cancelled()} 
                                         <form id="cancelyes" class="cancel" method="post" onclick="$('#cancelyes').submit();" action="{urlFor name="project-view" options="project_id.$project_id"}" >
                                           <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Uncancel" >
                                            <a class=" btn-grayish cancel"   data-id="0" id="uncancel"  role="button" data-cancelled="0" data-task-id="{$task->getId()}">
                                            Yes
                                            </a>
                                            <input type="hidden" name="cancel" value="" />
                                            <input type="hidden" name="cancelled" value="0" />
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                           </span>
                                         </form>
                                     {else}
                                        <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Cancel" >
                                            <a class="btngray cancel" data-bs-toggle="modal"  data-bs-target="#cancelmodal" id="cancel"  href="#cancelmodal" role="button" data-task-id="{$task->getId()}" data-cancelled="1">
                                               No
                                            </a>
                                        </span>
                                     {/if}
                                    </td>

                                    <td class="text-center">
                                        <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$task->getDeadline()}</div>
                                    </td>

                                    <td class="text-center">
                                    <form id="publishedForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}" style="text-align: center">
                                    <input type="hidden" name="task_id" value="{$task_id}" />
                                    {if $task->getPublished() == 1}
                                        <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{Localisation::getTranslation('common_unpublish')}">
                                        <a class=" btn-grayish" onclick="$('#publishedForm{$task_id}').submit();" >
                                        <i class="fa-regular fa-circle-check fa-lg"></i>
                                        </a>
                                        </span>
                                        <input type="hidden" name="publishedTask" value="0" />
                                    {else}
                                        <input type="hidden" name="publishedTask" value="1" />
                                        <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{Localisation::getTranslation('common_publish')}">
                                        <a class="btngray" onclick="$('#publishedForm{$task_id}').submit();"  >
                                        <i class="fa-regular fa-circle-xmark fa-lg"></i>
                                        </a>
                                        </span>
                                    {/if}
                                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                    </form>
                                    </td>

                                    <td class="text-center">
                                        <form id="trackedForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $taskMetaData[$task_id]['tracking']}
                                                <input type="hidden" name="trackTask" value="0" />
                                                <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="{Localisation::getTranslation('common_untrack_task')}" >
                                                <a class="btn-grayish" onclick="$('#trackedForm{$task_id}').submit();" >
                                                      <i class="fa-solid fa-envelope fa-lg"></i>
                                                </a>
                                                </span>
                                            {else}
                                                <input type="hidden" name="trackTask" value="1" />
                                                 <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="{Localisation::getTranslation('common_track_task')}" >
                                                <a class="btngray" onclick="$('#trackedForm{$task_id}').submit();" >
                                                     <i class="fa-regular fa-circle-xmark fa-lg"></i>
                                                </a>
                                                </span>
                                            {/if}
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    </td>    
                                    <td class="text-center">
                                        <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="{Localisation::getTranslation('project_view_edit_task')}">
                                        <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class="btngray" >
                                            <i class="fa-solid fa-pen fa-lg"></i>
                                        </a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form id="archiveDeleteForm{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
                                            <input type="hidden" name="task_id" value="{$task_id}" />
                                            {if $status_id < TaskStatusEnum::IN_PROGRESS}
                                                <input type="hidden" name="deleteTask" value="Delete" />
                                                <a class="btn-grayish" 
                                                    onclick="if (confirm('{Localisation::getTranslation('project_view_1')}')) 
                                                        $('#archiveDeleteForm{$task_id}').submit();" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="{Localisation::getTranslation('common_delete')}" >
                                                     <i class="fa-solid fa-trash fa-lg"></i>
                                                </a> 
                                            {elseif $status_id == TaskStatusEnum::IN_PROGRESS || $status_id == TaskStatusEnum::CLAIMED}
                                                <div class="tooltip-wrapper" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="{Localisation::getTranslation('project_view_2')}">  <button style="pointer-events: none;" class="btn-grayish" disabled >
                                                     <i class="fa-regular fa-circle-check fa-lg"></i>
                                                 </button> 
                                                </div>
                                            {else}
                                                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
                                                <input type="hidden" name="archiveTask" value="Delete" />
                                               
                                                <a class="btn-grayish"
                                                    onclick="if (confirm('{Localisation::getTranslation('project_view_3')}'))
                                                        $('#archiveDeleteForm{$task_id}').submit();" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="{Localisation::getTranslation('common_archive')}"> <img src="{urlFor name='home'}ui/img/project-trash.svg" alt="retrieve" > </a>
                                                {/if}
                                            {/if}
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    </td>
                                </tr>                        
                            {/foreach}
                        </tbody>
                    </table>
                    </div>
                    </div>
                  </div>
                {/foreach}
            {else}
                <div class="alert alert-warning">
                    <strong>{Localisation::getTranslation('common_what_happens_now')}?</strong> {Localisation::getTranslation('project_view_4')}
                    {Localisation::getTranslation('project_view_5')}
                </div>
            {/if}
        </div>
    </div>  

    {else}

        {if isset($projectTasks)}
        <p class="alert alert-info">
            {Localisation::getTranslation('project_view_6')}
        </p>
        {/if}
    {/if}

    {if !empty($volunteerTaskLanguageMap)}
    <hr />
    <h3 >
        {Localisation::getTranslation('project_view_tasks')}
        <small>{Localisation::getTranslation('project_view_0')}</small>
    </h3>
                {foreach from=$volunteerTaskLanguageMap key=languageCountry item=tasks}
                    <div class="fs-5 fw-bold">
                        {TemplateHelper::getLanguageAndCountryFromCode($languageCountry)}
                    </div>
                    <hr />
                    <div class="bg-body p-2 border-secondary rounded-top-3 mt-4">
                    <div class="table-responsive mt-4  ">       
                    <table class="table table-borderless" >
                        <thead class="fs-5">
                            <tr>
                                <th>{Localisation::getTranslation('common_title')}</th>
                                <th>{Localisation::getTranslation('common_status')}</th>
                                <th>{Localisation::getTranslation('common_type')}</th>
                                <th>{Localisation::getTranslation('common_task_deadline')}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-4 ">

                            {foreach from=$tasks item=task}
                                {assign var="task_id" value=$task['task_id']}
                                <tr >
                                    <td width="24%">
                                        <a  class="custom-link" href="{urlFor name="task-view" options="task_id.$task_id"}?twb_page=project&twb_zone=task">
                                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task['title'])}
                                        </a>
                                        <br />
                                    </td>
                                    <td>
                                        {assign var="status_id" value=$task['status_id']}
                                        {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                            {Localisation::getTranslation('common_waiting')}
                                        {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                            {Localisation::getTranslation('common_unclaimed')}
                                        {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                            {Localisation::getTranslation('common_in_progress')}
                                        {elseif $status_id == TaskStatusEnum::CLAIMED}
                                            Claimed
                                        {elseif $status_id == TaskStatusEnum::COMPLETE}
                                            {Localisation::getTranslation('common_complete')}
                                        {/if}
                                    </td>
                                    <td>
                                        <strong>
                                            <small>
                                            {assign var="type_id" value=$task['type_id']}
                                                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                                    {if $type_id == $task_type}
                                                        <span style="color: {$ui['colour']}">{$ui['type_text']}</span>{if $ui['shell_task']}<br />{$ui['type_category_text']}{/if}
                                                    {/if}
                                                {/foreach}
                                            </small>
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$task['deadline']}</div>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                    </div>
                    </div>
                {/foreach}
    {/if}

    <!-- Cancel Modal -->
<div id="cancelmodal" class="modal fade"  tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
    <h3 class="modal-title fs-4 fw-bold me-4" id="myModalLabel">Cancel Task</h3>
    <strong id="taskmsg" class="btn btn-danger me-4">No task has been selected</strong>

  </div>
  <div class="modal-body">
  <form id="cancel"  method="post" action="{urlFor name="project-view" options="project_id.$project_id"}"> 
  <p>Note: when you cancel a task all tasks in the same language pair/file combination will also be cancelled (unless the "Cancel only the selected task" checkbox is checked). Additionally an email will be sent to any linguists working on the tasks.</p>
  <p>Reason to cancel selected task(s):</p>
  <select name="cancel_task" id="cancel_task" class="form-select text-wrap">
    <option value="">--Select--</option>
    <option value="Request withdrawn by Partner without cause">Request withdrawn by Partner without cause</option>
    <option value="Request withdrawn by Partner with cause (timeline issues, quality issues, etc.)">Request withdrawn by Partner with cause (timeline issues, quality issues, etc.)</option>
    <option value="Request cancelled by TWB due to content eligibility concerns">Request cancelled by TWB due to content eligibility concerns</option>
    <option value="Request cancelled by TWB due to lack of capacity">Request cancelled by TWB due to lack of capacity</option>
    <option value="other">Other</option>    
  </select>
  <br />
  <p name="reason_text">Further details:</p>
  <br />
  <textarea rows="4" cols="40" name="reason" id="reason" style="width:auto;"></textarea>
  <br />
  <input type="checkbox" name="cancel_selected_only" value="1" /> Cancel only the selected task on TWB only (not on Phrase)
  <input type="hidden" name="cancel" value="" />
  <input type="hidden" name="cancelled" value="" />
   {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
  </div>
  <div class="modal-footer">
  <button type="button" class="btn btn-light"  data-bs-dismiss="modal" aria-label="Close"> Close</button>
    <button class="btn btn-danger" id="cancelbtn" onclick="$('#cancel').submit();">Confirm</button>
  </div>

  </form>
  </div>
  </div>
</div>

<!-- PO# Modal -->
<div id="ponummodal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
    <h3 class="modal-title fs-4 fw-bold me-4" id="myModalLabel">Set Purchase Order #</h3>
  </div>

  <div class="modal-body">
  <form id="ponumform"  method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
    PO #: <input type="text" name="po" value="" />
    <input type="hidden" name="ponum" value="" />
    <input type="hidden" name="ready_payment_status" value="Unsettled" />
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    <div class="modal-footer">
      <button type="button" class=" btn btn-light" data-bs-dismiss="modal" aria-label="Close">Close</button>
      <button class="btn btn-success" id="ponumbtn" onclick="$('#ponumform').submit();">Confirm</button>
    </div>
  </form>
  </div>
</div>
</div>
</div>


    </div>
</section>

</div>

<script>
   $("[data-bs-toggle='tooltip']").tooltip(); // Initialize Tooltip
</script>
{include file="footer2.tpl"}
