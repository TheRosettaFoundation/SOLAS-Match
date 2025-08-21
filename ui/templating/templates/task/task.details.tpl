<!-- Editor Hint: ¿áéíóú -->

{assign var="status_id" value=$task->getTaskStatus()}
{if !empty($details_claimant)}
    {assign var="user_id" value=$details_claimant->getId()}
{/if}

<div class="bg-body p-2 border-secondary rounded-3 mt-4 ">
<div class="table-responsive ">
<table class="table ">
    <thead >
       <tr class="fs-5 align-middle position-relative  ">
                <th>Project</th>
                <th>Source Language</th>
                <th> Target Language</th>
                <th>Created</th>
                <th> Task Deadline</th>
                <th>Linguist {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text']}</th>
                {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER))}<th>{Localisation::getTranslation('common_status')}</th>{/if}
       </tr>
    </thead>
    <tbody class="fs-4">
        <tr>
            <td>
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view"  options="project_id.$projectId"}" class="custom-link">
                    {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}
                    </a>
                {/if}
            </td>
            
            <td>
                {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                {TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}
                {/if}
            </td>

            <td>
                {TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}
            </td>

            <td>
                <div class="convert_utc_to_local" style="visibility: hidden">{$task->getCreatedTime()}</div>
            </td>

            <td>
                <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$task->getDeadline()}</div>

                {if $max_translation_deadline}
                    {if strpos($max_translation_deadline, 'Completed')}
                        <div>{$max_translation_deadline}</div>
                    {else}
                        {assign var="pos_colon" value=strpos($max_translation_deadline, ':')}
                        <div>
                            {substr($max_translation_deadline, 0, $pos_colon + 2)}
                            <span class="convert_utc_to_local_deadline_no_timezone" style="visibility: hidden">{substr($max_translation_deadline, $pos_colon + 2)}</span>
                        </div>
                    {/if}
                {/if}
            </td>
            <td>
               <span class="d-none">
                    <div id="siteLocationURL">{Settings::get("site.location")}</div>
                    <div id="project_id_for_updated_wordcount">{$task->getProjectId()}</div>
                </span>
                <div id="put_updated_wordcount_here">{if $task->getWordCount() != '' && $task->getWordCount() > 1}{$task->getWordCount()}{if $task->get_word_count_original() > 0 && $task->getWordCount() != $task->get_word_count_original()} ({$task->get_word_count_original()}){/if}{else}-{/if}</div>
            </td>

            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                <td>
                    {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                        {Localisation::getTranslation('common_waiting')}
                    {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                        {Localisation::getTranslation('common_unclaimed')}
                    {elseif $status_id == TaskStatusEnum::CLAIMED}
                        Claimed
                    {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                        {Localisation::getTranslation('common_in_progress')}
                    {elseif $status_id == TaskStatusEnum::COMPLETE}
                        {Localisation::getTranslation('common_complete')}
                        {if !empty($complete_date)}<br />{substr($complete_date, 0, 10)}{/if}
                    {/if}
                </td>
            {/if}
        </tr>
    </tbody>
</table>
</div>
</div>

<div class="bg-body p-2 border-secondary rounded-3 mt-2 mb-2">
<div class="table-responsive">
    <table class="table table-borderless">
       <tr class="">
            <thead class="fs-5">
            <th class="w-100" >{Localisation::getTranslation('common_project_description')}</th>
            </thead>
       </tr>

        <tbody class="fs-4">
            <tr>
                <td class="w-100">
                        {if $project->getDescription() != ''}
                            <div class="ql-editor">{TemplateHelper::clean_project_description($project->getDescription())}</div>
                        {else}
                            {Localisation::getTranslation('common_no_description_has_been_listed')}
                        {/if}
                </td>
            </tr>
            <tr>
                {if !empty(TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_1']) && empty(TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_2'])}
                <td class="w-100 d-flex">
                    <div class="pb-0      bg-dark rounded-2"><a href="{TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_1']}" class="btngray-lg" target="_blank">Click here for "{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']}" instructions</a></div>
                </td>
                {/if}

                {if !empty(TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_1']) && !empty(TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_2'])}
                <td class="w-100 d-flex flex-column">
                    <div class="pb-0 mb-2 bg-dark rounded-2"><a href="{TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_1']}" class="btngray-lg" target="_blank">Click here for "{TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_words_1']}" instructions</a></div>
                    <div class="pb-0      bg-dark rounded-2"><a href="{TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_2']}" class="btngray-lg" target="_blank">Click here for "{TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_words_2']}" instructions</a></div>
                </td>
                {/if}
            </tr>
        </tbody>
    </table>
   </div>

{if $task->getComment() != ''}
<div class="table-responsive">
    <table class="table table-borderless">
       <tr class="">
            <thead class="fs-5">
            <th class="w-100">{Localisation::getTranslation('common_task_comment')}</th>
            </thead>
       </tr>

        <tbody class="fs-4">
            <tr>
                <td class="w-100">
                    {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getComment())}
                </td>
            </tr>
        </tbody>
    </table>
   </div>  
{/if}

{assign var="tags" value=$project->getTag()}
{if ($project->getImpact() != '' && $project->getImpact() != '-') || !empty($tags)}
    <div class="table-responsive">
    <table class="table table-borderless ">
        <thead class="fs-5">        
            <tr>
                <th class="w-50">
                    <strong>{Localisation::getTranslation('task_details_project_impact')}</strong>
                </th>

                <th class="w-50">
                    <strong>{Localisation::getTranslation('task_details_project_tags')}</strong>
                </th>
            </tr>
        </thead>
        <tbody class="fs-4">
            <tr>                
                <td class="w-50">
                    <i>
                    {if $project->getImpact() != ''}
                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getImpact())}
                    {else}
                        {Localisation::getTranslation('No impact has been listed')}
                    {/if}  
                    </i> 
                </td>    

                <td class="w-50">
                    {foreach from=$tags item=tag}
                        <a class="tag label" href="{urlFor name="tag-details" options="id.{$tag->getId()}"}">{TemplateHelper::uiCleanseHTML($tag->getLabel())}</a>
                    {/foreach}
                </td>                    
            </tr>
        </tbody>
       </table>
       </div> 
{/if}

            {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && isset($discourse_slug)}

           <div class="table-responsive">
            <table class="table table-borderless">
            <thead class="fs-5">
             <tr class="align-middle">
                <th class="w-50">
                    {if !preg_match('/^Test.{4}$/', $task->getTitle())}<strong>{Localisation::getTranslation('common_discuss_on_community')}</strong>{/if}
                </th>
             
                <th class="w-50" >
                    {if !empty($matecat_url)}
                        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                            <strong>Ready to start working?</strong>
                        {elseif in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER) && TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                            <strong>Ready to start working?</strong>
                        (elseif !empty($user_id) && $user_id == $current_user_id && $status_id == TaskStatusEnum::IN_PROGRESS}
                            <strong>Ready to start working?</strong>
                        {/if}
                    {/if}
                </th>
              </tr>  
            </thead>
            <tbody class="fs-4">
            <tr>
                <td class="w-50 d-flex">
                   <div class="pb-0 bg-dark rounded-2">{if !preg_match('/^Test.{4}$/', $task->getTitle())}<a href="https://community.translatorswb.org/t/{$discourse_slug}" class="btngray-lg" target="_blank">Ask in the Forum</a>{/if}</div>
                </td>

                <td class="w-50 ">
                  <div class="d-flex">
                    <div class="pb-0 bg-dark rounded-2">
                      {if !empty($matecat_url)}
                        {if $type_id == 2}
                            {assign var="translate_text" value="Translate using Phrase TMS"}
                        {elseif $type_id == 3}
                            {assign var="translate_text" value="Revise using Phrase TMS"}
                        {elseif $type_id == 6}
                            {assign var="translate_text" value="Proofread using Phrase TMS"}
                        {else}
                            {assign var="translate_text" value="Work using this URL"}
                        {/if}

                        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                            <a href="{$matecat_url}" class="btngray-lg" target="_blank">
                                {$translate_text} <img src="{urlFor name='home'}ui/img/url.svg" alt="url" />
                            </a>
                        {elseif in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER) && TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                            <a href="{$matecat_url}" class="btngray-lg" target="_blank">
                                {$translate_text} <img src="{urlFor name='home'}ui/img/url.svg" alt="url" />
                            </a>
                        (elseif !empty($user_id) && $user_id == $current_user_id && $status_id == TaskStatusEnum::IN_PROGRESS}
                            <a href="{$matecat_url}" class="btngray-lg" target="_blank">
                                {$translate_text} <img src="{urlFor name='home'}ui/img/url.svg" alt="url" />
                            </a>
                        {/if}
                      {/if}
                    </div>
                 <div>
                 </td>
            </tr>
            </tbody>
          </table>
          </div>

            {/if}

            {if !empty($required_qualification_for_details)}

            <div class="table-responsive mt-4">
            <table class="table table-borderless">
              <thead class="fs-5">
                <tr>
                    <th>{Localisation::getTranslation('required_qualification_level')}: </th>
                </tr>              
              </thead>
              <tbody class="fs-4">
               <tr>
               <td>
                    <i>
                    {if $required_qualification_for_details == 1}{Localisation::getTranslation('user_qualification_level_1')}{/if}
                    {if $required_qualification_for_details == 2}{Localisation::getTranslation('user_qualification_level_2')}{/if}
                    {if $required_qualification_for_details == 3}{Localisation::getTranslation('user_qualification_level_3')}{/if}
                    </i>
               </td>

               </tr>
                 </tbody>
            </table>
         </div>  

            {/if}
        </div>

{assign var="task_id" value=$task->getId()} 
{if isset($show_actions)}

<div class="d-flex align-items-center mt-4 mb-4">
<div class="flex-fill border-top border-1 border-body-subtle " ></div>
<div class=" text-center mx-4 text-muted fw-bold">Admin</div>
<div class=" flex-fill border-top border-1 border-body-subtle" ></div> 
</div>
<div class="bg-body p-2 border-secondary rounded-3 mt-4">
  <div class=" table table-responsive mt-4">
    <table class="table  ">
        <thead>
         <tr class="fs-5 align-middle">
            <th>{Localisation::getTranslation('common_publish_task')}</th>
            {if $status_id == TaskStatusEnum::IN_PROGRESS && ($roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)) && TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
            <th>Mark Shell Task Complete</th>
            {/if}
            <th>Cancelled?</th>
            <th>{Localisation::getTranslation('common_tracking')}</th>
            {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER)) && isset($paid_status)}<th>Paid?</th>{/if}
            {if !empty($details_claimant)}
            <th>{Localisation::getTranslation('common_claimed_date')}</th>
            <th>{Localisation::getTranslation('common_claimed_by')}</th>
            {/if}
         </tr>   
        </thead>
        <tbody class="fs-4">
        <tr class="py-2">
            <td>
              <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $task->getPublished() == 1}
                        <input type="hidden" name="published" value="0" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn-grayish">
                            <img src="{urlFor name='home'}ui/img/unpublish.svg" alt="unpublish" >
                             {Localisation::getTranslation('common_unpublish')}
                        </a>

                    {else}
                        <input type="hidden" name="published" value="1" />
                        <a href="#" onclick="this.parentNode.submit()" class="btngray">
                        
                             {Localisation::getTranslation('common_publish')}
                        </a>
                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>

            </td>
            {if $status_id == TaskStatusEnum::IN_PROGRESS && ($roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)) && TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
            <td>
                <form id="complete_form_{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$projectId"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    <input type="hidden" name="complete_task" value="1" />

                    <a class="btn-grayish " onclick="$('#complete_form_{$task_id}').submit();" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Set Status Complete">
                         <img src="{urlFor name='home'}ui/img/check.svg" alt="check" >
                    </a>
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            {/if}
            <td>
                {if $task->get_cancelled()}

                    <a href="#" class="btn-grayish opacity-50" disabled>
                       Yes
                    </a>

                {else}

                        <a href="#" class="btngray opacity-50" disabled>
                             No
                        </a>

                {/if}
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $taskMetaData[$task_id]['tracking']}
                        <input type="hidden" name="track" value="Ignore" />

                        <a href="#" onclick="this.parentNode.submit()" class="btn-grayish">
                        <i class="fa-regular fa-circle-xmark fa-lg"></i> {Localisation::getTranslation('common_untrack_task')}
                        </a>

                    {else}
                        <input type="hidden" name="track" value="Track" />

                        <a href="#" onclick="this.parentNode.submit()" class="btngray">
                        <i class="fa-solid fa-envelope fa-lg"></i> {Localisation::getTranslation('common_track_task')}
                        </a>

                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER)) && isset($paid_status)}
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if empty($paid_status)}
                    
                        <input type="hidden" name="paid_status" value="2" />
                        <a href="#" onclick="this.parentNode.submit()" class="btngray">
                        <i class="fa-regular fa-circle-check fa-lg"></i> Make Paid
                        </a>

                    {else}

                        <input type="hidden" name="paid_status" value="1" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn-grayish">
                        <i class="fa-regular fa-circle-xmark fa-lg"></i> Make Unpaid
                        </a>

                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            {/if}
            {if !empty($details_claimant)}
            <td>
                <div class="convert_utc_to_local_deadline" style="visibility: hidden">{$details_claimed_date}</div>
            </td>
            <td>
                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{TemplateHelper::uiCleanseHTML($details_claimant->getDisplayName())}</a>
            </td>
            {/if}
        </tr>
       </tbody>
    </table>
    </div>

{/if}

{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}

    <div class="table-responsive mt-4">
    <table class="table  ">
        <thead>
          <tr class="fs-5 align-middle">
          {if !empty($paid_status)}
            <th>Purchase Order</th>
            <th>Payment Status</th>
            <th>Linguist Unit Rate for {TaskTypeEnum::$enum_to_UI[$type_id]['pricing_and_recognition_unit_text_hours']}</th>
            <th>Partner Unit Price for {TaskTypeEnum::$enum_to_UI[$type_id]['pricing_and_recognition_unit_text_hours']}</th>
            <th>Source Units in {TaskTypeEnum::$enum_to_UI[$type_id]['source_unit_for_later_stats']}</th>
          {else}
            <th>Partner weighted Pricing Units in {TaskTypeEnum::$enum_to_UI[$type_id]['pricing_and_recognition_unit_text']}</th>
            <th>Source Units in {TaskTypeEnum::$enum_to_UI[$type_id]['source_unit_for_later_stats']}</th>
          {/if}
        </tr>
        </thead>
        <tbody class="fs-4">
        <tr >
{if !empty($paid_status)}
            <td>
                {$paid_status['purchase_order']}
            </td>
            <td>
                {$paid_status['payment_status']}
                {if $paid_status['payment_status'] == 'Unsettled'}
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                        <input type="submit" class="btngray-sm mt-2" name="payment_status_submit" value="Change to In-kind" />
                        <input type="hidden" name="mark_payment_status" value="In-kind" />
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                        <input type="submit" class="btngray-sm mt-2" name="payment_status_submit" value="Change to In-house" />
                        <input type="hidden" name="mark_payment_status" value="In-house" />
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                        <input type="submit" class="btngray-sm mt-2" name="payment_status_submit" value="Change to Waived" />
                        <input type="hidden" name="mark_payment_status" value="Waived" />
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}

                {if $paid_status['payment_status'] == 'In-kind' || $paid_status['payment_status'] == 'In-house' || $paid_status['payment_status'] == 'Waived'}
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                        <input type="submit" class="btngray-sm mt-2" name="payment_status_submit" value="Change to Unsettled" />
                        <input type="hidden" name="mark_payment_status" value="Unsettled" />
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}

                {if $paid_status['payment_status'] == 'Company'}
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                        <input type="submit" class="btngray-sm mt-2" name="payment_status_submit" value="Change to In-kind" />
                        <input type="hidden" name="mark_payment_status" value="In-kind" />
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}
                {if $paid_status['payment_status'] == 'In-kind'}
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                        <input type="submit" class="btngray-sm mt-2" name="payment_status_submit" value="Change to Company" />
                        <input type="hidden" name="mark_payment_status" value="Company" />
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input style="width:60px;" type='text' value="{$paid_status['unit_rate']}" name="unit_rate" id="unit_rate" />
                    <input type="submit" class="btngray-sm mt-2" name="unit_rate_submit" value="Submit" />
                    <input type="hidden" name="mark_unit_rate" value="1" />
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                 <hr />

                <div class="mt-4  fs-5">  <span class="fw-bold" >Default: </span>  ${TaskTypeEnum::$enum_to_UI[$type_id]['unit_rate']}</div>
                <hr/>

                <div class="mt-4 fw-bold fs-5">Total Expected Cost</div>
                <hr />
                <div> 
            ${if $paid_status['payment_status'] == 'In-kind' || $paid_status['payment_status'] == 'In-house' || $paid_status['payment_status'] == 'Waived'}<del>{round($total_expected_cost, 2)}</del>{else}{round($total_expected_cost, 2)}{/if} for {if $task->getWordCount() != '' && $task->getWordCount() > 1}{$task->getWordCount()}{else}-{/if} {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text_short']}

                </div>
                </form>
            </td>
            <td>
            <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
            <input style="width:60px;" type='text' value="{$paid_status['unit_rate_pricing']}" name="unit_rate_pricing" id="unit_rate_pricing" />
            <input type="submit" class="btngray-sm mt-2" name="unit_rate_pricing_submit" value="Submit" />
            <input type="hidden" name="mark_unit_rate_pricing" value="1" />
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                <hr />

                <div class="fs-5 mt-4"> <span class="fw-bold">Default: </span> ${TaskTypeEnum::$enum_to_UI[$type_id]['unit_rate_pricing_default']}   </div>
                <hr/>

                <div class="fs-5 fw-bold mt-4"> Total Expected Price</div>
                <hr />
                <div>
                   ${round($total_expected_price, 2)} for {$task->get_word_count_partner_weighted()} {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text_short']}
                </div>
            </form> 

             </td>   
             <td>

             <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
             <input style="width:60px;" type='text' value="{$task->get_source_quantity()}" name="source_quantity" id="source_quantity" />
             <input type="submit" class="btngray-sm fs-5 mt-2 md:mt-0" name="source_quantity_submit" value="Submit" />
             <input type="hidden" name="mark_source_quantity" value="1" />
             {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
         </form>
             </td>

          {else}
             <td>
             {if $task->getWordCount() != '' && $task->getWordCount() > 1}{$task->getWordCount()}{else}-{/if}
        </td>
        <td>
             <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
             <input style="width:60px;" type='text' value="{$task->get_source_quantity()}" name="source_quantity" id="source_quantity" />
             <input type="submit" class="btngray-sm fs-5 mt-2 md:mt-0" name="source_quantity_submit" value="Submit" />
             <input type="hidden" name="mark_source_quantity" value="1" />
             {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
         </form>
         </td>   
          {/if}

        </tr>
       </tbody>
    </table>
    </div>
{/if}
{if isset($show_actions)}
</div>
{/if}
