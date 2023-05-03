<!-- Editor Hint: ¿áéíóú -->
<table class="table table-striped">
    <thead>
        <th style="text-align: left"><strong>{Localisation::getTranslation('common_project')}</strong></th>
        <th>{Localisation::getTranslation('common_source_language')}</th>
        <th>{Localisation::getTranslation('common_target_language')}</th>
        <th>{Localisation::getTranslation('common_created')}</th>
        <th>{Localisation::getTranslation('common_task_deadline')}</th>
        <th>{TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text']}</th>
        {if isset($isMember)}<th>{Localisation::getTranslation('common_status')}</th>{/if}
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left; word-break:break-all; width: 150px">
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view" options="project_id.$projectId"}">
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
            </td>
            <td>
                <span class="hidden">
                    <div id="siteLocationURL">{Settings::get("site.location")}</div>
                    <div id="project_id_for_updated_wordcount">{$task->getProjectId()}</div>
                </span>
                <div id="put_updated_wordcount_here">{if $task->getWordCount() != '' && $task->getWordCount() > 1}{$task->getWordCount()}{if $task->get_word_count_original() > 0 && $task->getWordCount() != $task->get_word_count_original()} ({$task->get_word_count_original()}){/if}{else}-{/if}</div>
            </td>
            {if isset($isMember)}
                <td>
                    {assign var="status_id" value=$task->getTaskStatus()}
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
                    {/if}
                </td>
            {/if}
        </tr>
    </tbody>
</table>

<div class="well">
    <table width="100%" style="overflow-wrap: break-word; table-layout: fixed;">
        <thead>
            <th width="48%" align="left">{Localisation::getTranslation('common_task_comment')}<hr/></th>
            <th></th>
            <th width="48%" align="left">{Localisation::getTranslation('common_project_description')}<hr/></th>
        </thead>
        <tbody>
            <tr style="overflow-wrap: break-word;" valign="top">
                <td>
                    <i>
                        {if $task->getComment() != ''}
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getComment())}
                        {else}
                            {Localisation::getTranslation('common_no_comment_has_been_listed')}
                        {/if}
                    </i>
                </td>
                <td></td>
                <td>
                    <i>
                        {if $project->getDescription() != ''}
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getDescription())}
                        {else}
                            {Localisation::getTranslation('common_no_description_has_been_listed')}
                        {/if}
                    </i>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 40px"/>
            </tr>
            <tr>
                <td>
                    <strong>{Localisation::getTranslation('task_details_project_impact')}</strong><hr/>
                </td>
                <td></td>
                <td>
                    <strong>{Localisation::getTranslation('task_details_project_tags')}</strong><hr/>
                </td>
            </tr>
            <tr valign="top">                
                <td>
                    <i>
                    {if $project->getImpact() != ''}
                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getImpact())}
                    {else}
                        {Localisation::getTranslation('No impact has been listed')}
                    {/if}  
                    </i> 
                </td>    
                <td></td>
                <td>
                    {foreach from=$project->getTag() item=tag}
                        <a class="tag label" href="{urlFor name="tag-details" options="id.{$tag->getId()}"}">{TemplateHelper::uiCleanseHTML($tag->getLabel())}</a>
                    {/foreach}
                </td>                    
            </tr>
            {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && isset($discourse_slug)}
            <tr>
                <td colspan="3" style="padding-bottom: 40px"/>
            </tr>
            <tr>
                <td>
                    {if !preg_match('/^Test.{4}$/', $task->getTitle())}<strong>{Localisation::getTranslation('common_discuss_on_community')}:</strong><hr/>{/if}
                </td>
                <td></td>
                <td>
                    {if isset($isSiteAdmin) && isset($matecat_url) && $isSiteAdmin && $matecat_url != ''}<strong>{if !empty($memsource_task)}Phrase TMS{else}Kató TM{/if} URL for Task:</strong><hr/>{/if}
                </td>
            </tr>
            <tr valign="top">
                <td>
                    {if !preg_match('/^Test.{4}$/', $task->getTitle())}<a href="https://community.translatorswb.org/t/{$discourse_slug}" target="_blank">https://community.translatorswb.org/t/{$discourse_slug}</a>{/if}
                </td>
                <td></td>
                <td>
                    {if isset($isSiteAdmin) && isset($matecat_url) && $isSiteAdmin && $matecat_url != ''}<a href="{$matecat_url}" target="_blank">{$matecat_url}</a>{/if}
                </td>
            </tr>
            {/if}
            {if !empty($required_qualification_for_details)}
            <tr>
                <td colspan="3" style="padding-bottom: 40px"/>
            </tr>
            <tr>
                <td>
                    <strong>{Localisation::getTranslation('required_qualification_level')}:</strong><hr/>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr valign="top">
                <td><i>
                    {if $required_qualification_for_details == 1}{Localisation::getTranslation('user_qualification_level_1')}{/if}
                    {if $required_qualification_for_details == 2}{Localisation::getTranslation('user_qualification_level_2')}{/if}
                    {if $required_qualification_for_details == 3}{Localisation::getTranslation('user_qualification_level_3')}{/if}
                </i></td>
                <td></td>
                <td></td>
            </tr>
            {/if}
        </tbody>
    </table>
</div>

{assign var="task_id" value=$task->getId()}
{if isset($isOrgMember)}
    <table width="100%" class="table table-striped">
        <thead>
            <th>{Localisation::getTranslation('common_publish_task')}</th>
            <th>Cancelled?</th>
            <th>{Localisation::getTranslation('common_tracking')}</th>
            {if !empty($isSiteAdmin) && isset($paid_status)}<th>Paid?</th>{/if}
            {if !empty($details_claimant)}
            <th>{Localisation::getTranslation('common_claimed_date')}</th>
            <th>{Localisation::getTranslation('common_claimed_by')}</th>
            {/if}
        </thead>
        <tr align="center">
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $task->getPublished() == 1}
                        <input type="hidden" name="published" value="0" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-remove-circle icon-white"></i> {Localisation::getTranslation('common_unpublish')}
                        </a>
                    {else}
                        <input type="hidden" name="published" value="1" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-check icon-black"></i> {Localisation::getTranslation('common_publish')}
                        </a>
                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            <td>
                {if $task->get_cancelled()}
                    <a href="#" class="btn btn-small btn-inverse" disabled>
                        <i class="icon-check icon-white"></i> Yes
                    </a>
                {else}
                    <a href="#" class="btn btn-small" disabled>
                        <i class="icon-remove-circle icon-black"></i> No
                    </a>
                {/if}
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if $taskMetaData[$task_id]['tracking']}
                        <input type="hidden" name="track" value="Ignore" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-inbox icon-white"></i> {Localisation::getTranslation('common_untrack_task')}
                        </a>
                    {else}
                        <input type="hidden" name="track" value="Track" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-envelope icon-black"></i> {Localisation::getTranslation('common_track_task')}
                        </a>
                    {/if}
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            {if !empty($isSiteAdmin) && isset($paid_status)}
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type="hidden" name="task_id" value="{$task_id}" />
                    {if empty($paid_status)}
                        <input type="hidden" name="paid_status" value="2" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small">
                            <i class="icon-check icon-black"></i> Make Paid
                        </a>
                    {else}
                        <input type="hidden" name="paid_status" value="1" />
                        <a href="#" onclick="this.parentNode.submit()" class="btn btn-small btn-inverse">
                            <i class="icon-remove-circle icon-white"></i> Make Unpaid
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
                {assign var="user_id" value=$details_claimant->getId()}
                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{TemplateHelper::uiCleanseHTML($details_claimant->getDisplayName())}</a>
            </td>
            {/if}
        </tr>
    </table>
{/if}

{if !empty($isSiteAdmin) && !empty($paid_status)}
    <table width="100%" class="table table-striped">
        <thead>
            <th>Purchase Order</th>
            <th>Payment Status</th>
            <th>Unit Rate for {TaskTypeEnum::$enum_to_UI[$type_id]['pricing_and_recognition_unit_text']}</th>
            <th>Default Unit Rate for {TaskTypeEnum::$enum_to_UI[$type_id]['pricing_and_recognition_unit_text']}</th>
            <th>Total Expected Cost</th>
            <th>Source Units in {TaskTypeEnum::$enum_to_UI[$type_id]['source_unit_for_later_stats']}</th>
        </thead>
        <tr align="center">
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type='text' value="{$paid_status['purchase_order']}" name="purchase_order" id="purchase_order" />
                    <input type="submit" class="btn btn-primary" name="purchase_order_submit" value="Submit" />
                    <input type="hidden" name="mark_purchase_order" value="1" />
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            <td>
                {$paid_status['payment_status']}
                {if $paid_status['payment_status'] == 'Pending documentation' || $paid_status['payment_status'] == 'Ready for payment'}
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                        {if $paid_status['payment_status'] == 'Pending documentation'}
                            <input type="submit" class="btn btn-primary" name="payment_status_submit" value="Change to Ready for payment" />
                            <input type="hidden" name="mark_payment_status" value="Ready for payment" />
                        {/if}
                        {if $paid_status['payment_status'] == 'Ready for payment'}
                            <input type="submit" class="btn btn-primary" name="payment_status_submit" value="Change to Pending documentation" />
                            <input type="hidden" name="mark_payment_status" value="Pending documentation" />
                        {/if}
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}
                {if $paid_status['payment_status'] == 'Ready for payment'}
                    <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                            <input type="submit" class="btn btn-primary" name="payment_status_submit" value="Change to Settled" />
                            <input type="hidden" name="mark_payment_status" value="Settled" />
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                {/if}
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type='text' value="{$paid_status['unit_rate']}" name="unit_rate" id="unit_rate" />
                    <input type="submit" class="btn btn-primary" name="unit_rate_submit" value="Submit" />
                    <input type="hidden" name="mark_unit_rate" value="1" />
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
            <td>
                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                    {if $type_id == $task_type}
                        {$ui['unit_rate']}
                    {/if}
                {/foreach}
            </td>
            <td>
                ${round($total_expected_cost, 2)}
            </td>
            <td>
                <form method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                    <input type='text' value="{$paid_status['source_quantity']}" name="source_quantity" id="source_quantity" />
                    <input type="submit" class="btn btn-primary" name="source_quantity_submit" value="Submit" />
                    <input type="hidden" name="mark_source_quantity" value="1" />
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </td>
        </tr>
    </table>
{/if}
