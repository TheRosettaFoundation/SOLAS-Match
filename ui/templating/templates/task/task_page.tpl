{include file="new_header.tpl"}
<div class="d-none">
  <div id="siteLocationURL">{Settings::get('site.location')}</div>
  <div id="task_id">{$task->getId()}</div>
  <div id="status_id">{$task->getTaskStatus()}</div>
  <div id="user_id">{$current_user_id}</div>

  {assign var="task_id" value=$task->getId()}
  {assign var="type_id" value=$task->getTaskType()}
  {assign var="status_id" value=$task->getTaskStatus()}
  {assign var="project_id" value=$task->getProjectId()}

  {if !empty($details_claimant) && $details_claimant->getId() == $current_user_id}
    {assign var="is_claimer" 1}
  {else}
    {assign var="is_claimer" 0}
  {/if}
  <div id="is_claimer">{$is_claimer}</div>

  {if TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
    <div id="number_of_review_panels">3</div>
  {else}
    <div id="number_of_review_panels">4</div>
  {/if}

  <div id="matecat_url">{$matecat_url}</div>
  <div id="sesskey">{$sesskey}</div>
</div>

  <div class="container-fluid app-shell py-4"> <!-- was main -->

    {if isset($flash['success'])}
      <p class="alert alert-success">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
      </p>
    {/if}
    {if isset($flash['error'])}
      <p class="alert alert-error">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
      </p>
    {/if}

    <!-- Header card -->
    <div class="card mb-4">
      <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
          <div class="d-flex align-items-start gap-3">
            <div class="rounded-3 bg-light border d-grid place-items-center">
                <a href="{$siteLocation}org/{$org_id}/profile">
                  {if !empty($org_image)}
                  <img src="data:image/jpeg;base64,{$org_image}" alt="{TemplateHelper::uiCleanseHTML($org_name)} logo" title="{TemplateHelper::uiCleanseHTML($org_name)}" class="rounded-circle" width="60" height="60" />
                  {else}
                  <img src="https://placehold.co/60x60/ED1C24/ffffff?text=TWB" alt="{TemplateHelper::uiCleanseHTML($org_name)} logo" title="{TemplateHelper::uiCleanseHTML($org_name)}" class="rounded-circle" width="60" height="60" />
                  {/if}
                </a>
            </div>
            <div>
              <div><a href="{$siteLocation}org/{$org_id}/profile" class="task_tooltip" style="text-decoration: none;"><span class="soft-muted small">{TemplateHelper::uiCleanseHTML($org_name)}</span><span class="task_tooltiptext">Organization that this task is for</span></a></div>
              <div><a href="{urlFor name="project-view" options="project_id.$project_id"}" class="task_tooltip" style="text-decoration: none;"><span class="h4 mb-1 project_colour">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}</span><span class="task_tooltiptext">Project that this task belongs to</span></a></div>
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="soft-muted small">{TemplateHelper::uiCleanseHTML($task->getTitle())}</span>
                <span class="badge rounded-pill text-uppercase fs-7 fw-bold" style="background-color: {TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']} task</span>
                {if isset($chunks[$task_id])} - <span> [Part {$chunks[$task_id]['low_level'] }</span><span>/{$chunks[$task_id]['number_of_chunks'] }]</span>{/if}
              </div>
            </div>
          </div>

          <div class="ms-lg-auto">
            {if $status_id == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
              {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $LINGUIST + $NGO_LINGUIST)) && $user_within_limitations}
              <button class="btn btn-orange" id="claim_button">
                Claim the {strtolower(TaskTypeEnum::$enum_to_UI[$type_id]['type_text'])} task
              </button>
              {/if}
            {/if}

            <div id="head_show-revision-btn">
              {if $is_claimer}
              <div class="d-none remove_style">
               <button class="btn btn-orange" id="show-revision-btn">
                 Review the instructions
               </button>
              </div>
              {/if}
            </div>

            {if !empty($matecat_url) && (($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)) || (in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER) && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']))}
              <a href="{$matecat_url}" target="_blank" class="btn btn-orange mt-2">Work URL</a>
            {/if}

            {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER))}
              <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='btn btn-orange mt-2'><img src="{urlFor name='home'}ui/img/edit.svg" alt="edit-icon" class="me-2">Edit task details</a>
            {/if}
          </div>
        </div>

        <hr class="my-4" />

        <!-- Stages -->
        <div class="row g-3">
          {if !$is_claimer && !($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) &&
            ($status_id > TaskStatusEnum::PENDING_CLAIM || $is_denied_for_task || !$user_within_limitations || TaskTypeEnum::$enum_to_UI[$type_id]['shell_task'])}

            <div class="col-12">
              <div class="stage-pill">
                <div class="d-flex align-items-start justify-content-between">
                  <div>
                    <div class="mb-2">
                      {if $is_denied_for_task && $type_id != TaskTypeEnum::TRANSLATION}
                        You cannot claim this task, because you have previously claimed the matching translation task.
                      {elseif $is_denied_for_task}
                        You cannot claim this task, because you have previously claimed the matching revision or proofreading task.
                      {else}
                        This task is no longer available.
                      {/if}
                      Feel free to check if there are other open tasks, explore our Learning Center to keep building your skills, or visit our Community Library for practical guidelines, tips, and best practices.
                    </div>
                    <div class="d-flex justify-content-center">
                      <a class="btn btn-orange" href="{urlFor name="home"}">Available tasks</a>
                      <a class="btn btn-orange mx-2" href="https://elearn.translatorswb.org/" target="_blank">Learning Center</a>
                      <a class="btn btn-orange" href="https://communitylibrary.translatorswb.org/login" target="_blank">Community Library</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          {else}

          {foreach $steps as $step}
          <div class="col-12 col-md-6 col-lg-3">
            <div class="stage-pill{if $step['this']} active light_blue_background{/if}">
              <div class="d-flex align-items-start justify-content-between">
                <div>
                  <div class="stage-title"><i class="bi {if $step['type'] == 3}bi-pencil-square{elseif $step['type'] == 4}bi-spellcheck{else}bi-translate{/if} me-1"></i> {TaskTypeEnum::$enum_to_UI[$step['type']]['type_text_short']}</div>
                  <div class="stage-meta">{if $step['status'] == TaskStatusEnum::IN_PROGRESS && $step['translations_not_all_complete']}{$taskStatusTexts[10]}{else}{$taskStatusTexts[$step['status']]}{/if}</div>
                  {if $step['delayed']}<span class="badge rounded-pill badge-task" style="color: #8A5A3A; background: #F3E9D8">Delayed</span>{/if}
                </div>
              </div>
              <div class="stage-meta mt-2">
                  {if $step['this'] && $step['translations_not_all_complete']}Wait for Previous step ({/if}<i class="bi bi-clock me-1"></i>{if $step['this'] && $step['translations_not_all_complete']}This step: {/if}<span class="convert_utc_to_local_deadline_natural{if $step['this']}_this{/if}" style="visibility: hidden">{$step['deadline']}</span>{if $step['this'] && $step['translations_not_all_complete']}){/if}
              </div>
              {if $step['this'] && ($is_claimer || ($step['status'] > 2 && ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER))))}
              <div class="mt-3">
                <div class="list-group list-group-flush small">
                  {if $step['status'] == 10 || $step['status'] == 3}
                  <div id="head_center">
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 blue_background">
                      <i class="bi bi-circle me-2"></i> Review the instructions
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary light_blue_background">
                      <i class="bi bi-circle me-2"></i> Work on the task
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary light_blue_background">
                      <i class="bi bi-circle me-2"></i> Provide feedback
                    </div>
                  </div>
                  {else}
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 light_blue_background">
                      <i class="bi bi-check-circle me-2"></i> Review the instructions
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary light_blue_background">
                      <i class="bi bi-check-circle me-2"></i> Work on the task
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary blue_background">
                      <i class="bi {if $review_done}bi-check-circle{else}bi-circle{/if} me-2"></i> Provide feedback
                    </div>
                  {/if}
                </div>
              </div>
              {/if}
            </div>
          </div>
          {/foreach}

          <div class="col-12 col-md-6 col-lg-3">
            <div class="stage-pill">
              <div class="d-flex align-items-start justify-content-between">
                <div>
                  <div class="stage-title"><i class="bi bi-truck me-1"></i> Delivery</div>
                  <div class="stage-meta">Pending</div>
                </div>
              </div>
            </div>
          </div>

          {/if}
        </div>

      </div>
    </div>

    <!-- Key info row -->
    <div class="keyrow mb-4">
      <div class="k">
        <div class="ico"><i class="bi bi-globe2"></i></div>
        <div>
          <div class="label">{if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}Language pair{else}Language{/if}</div>
          <div class="value">{if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}{TemplateHelper::getLanguageAndCountry($task->getSourceLocale())} → {/if}{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</div>
        </div>
      </div>
      <div class="k">
        <div class="ico"><i class="bi bi-clock"></i></div>
        <div>
          <div class="label">Task deadline</div>
          <div class="value"><div class="convert_utc_to_local_deadline_natural" style="visibility: hidden">{$task->getDeadline()}</div></div>
        </div>
      </div>
      <div class="k">
        <div class="ico"><i class="bi bi-file-text"></i></div>
        <div>
          <span class="d-none">
            <div id="siteLocationURL">{Settings::get("site.location")}</div>
            <div id="project_id_for_updated_wordcount">{$project_id}</div>
          </span>
          <div class="label">{TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text']}</div>
          <div class="value"><span id="put_updated_wordcount_here">{if $task->getWordCount() != '' && $task->getWordCount() > 1}{$task->getWordCount()}{if $task->get_word_count_original() > 0 && $task->getWordCount() != $task->get_word_count_original()} ({$task->get_word_count_original()}){/if}{else}-{/if}</span> {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text_short']}</div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Left column -->
      <div class="col-12 col-lg-8">

        {if $project->getImpact() != ''}
        <!-- Description -->
        <div class="card mb-4">
          <div class="card-body p-4">
            <div class="section-title">Description</div>
            <p class="mb-0">
              {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getImpact())}
            </p>
          </div>
        </div>
        {/if}

        <!-- Project-specific instructions -->
        <div class="card mb-4 highlight_1">
          <div class="card-body p-0">
            <div class="accordion accordion-flush" id="acc1">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1" aria-expanded="true">
                    Project-specific instructions
                  </button>
                </h2>
                <div id="c1" class="accordion-collapse collapse show" data-bs-parent="#acc1">
                  <div class="accordion-body p-4">
                    <div class="ql-editor">{TemplateHelper::clean_project_description($project->getDescription())}</div>

                    <div class="mt-2">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getComment())}</div>

                      {if $mt_used == 1}
                      <p class="mt-2 mb-3">
                        This project has been pre-translated using <strong>machine translation (MT)</strong> and your task is to fully post-edit it.
                        Learn more about MT post-editing (MTPE) in our Community Library.
                      </p>

                      <a href="https://communitylibrary.translatorswb.org/books/07-translation-and-editing-instructions/page/post-editing-machine-translation-tips-and-best-practice" target="_blank" class="btn btn-dark btn-sm">
                        <i class="bi bi-book me-1"></i> MTPE instructions
                      </a>
                      {/if}

                    <div class="mt-4">
                      <div id="head_confirm_read_project_instructions">
                        {if $is_claimer}
                        <button class="confirm-bar" type="button" id="confirm_read_project_instructions">
                          <i class="bi bi-check2-circle me-2"></i> I confirm I have read the project-specific instructions.
                        </button>
                        {/if}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- References and style guides -->
        <div class="card mb-4 highlight_2">
          <div class="card-body p-0">
            <div class="accordion accordion-flush" id="acc2">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c2" aria-expanded="true">
                    References and style guides
                  </button>
                </h2>
                <div id="c2" class="accordion-collapse collapse show" data-bs-parent="#acc2">
                  <div class="accordion-body p-4">

                    <div>
                      {if empty($language_style)}
                        No specific style guides
                      {else}
                        {foreach $language_style as $style}
                          <div>{$style['body']}</div>
                        {/foreach}
                      {/if}
                    </div>

                    <div class="mt-4">
                      <div id="head_confirm_read_reference_instructions">
                        {if $is_claimer}
                        <button class="confirm-bar" type="button" id="confirm_read_reference_instructions">
                          <i class="bi bi-check2-circle me-2"></i> I confirm I have reviewed the references and style guides.
                        </button>
                        {/if}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

<!-- Admin -->
{if isset($show_actions)}

<div class="d-flex align-items-center mt-4 mb-4">
<div class="flex-fill border-top border-1 border-body-subtle " ></div>
<div class=" text-center mx-4 text-muted fw-bold">Admin</div>
<div class=" flex-fill border-top border-1 border-body-subtle" ></div>
</div>
<div class="bg-body p-2 border-secondary rounded-3 mt-4">
  <div class=" table table-responsive mt-4">
    <table class="table">
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
                <form id="complete_form_{$task_id}" method="post" action="{urlFor name="project-view" options="project_id.$project_id"}">
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
                <a href="{urlFor name="user-public-profile" options="user_id.{$details_claimant->getId()}"}">{TemplateHelper::uiCleanseHTML($details_claimant->getDisplayName())}</a>
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
                <hr />

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
                <hr />

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

        <!-- Admin Assign -->
        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() < TaskStatusEnum::IN_PROGRESS}
        <div class="bg-body p-2 border-secondary rounded-3 mt-2">
          <div class="d-none d-md-flex justify-content-around p-2">
            <div class="fs-5 fw-bold w-75"> {Localisation::getTranslation('task_view_assign_label')}</div>
            <div class="fs-5 fw-bold w-75"> Remove a user from deny list for this task:</div>
          </div>

          <hr class="d-none d-md-block" />

          <div class=" d-block d-md-flex p-2 fs-6 mt-2">
            <div class="w-50" >
              <div class="fs-5 fw-bold w-75 mb-4 d-block d-md-none"> {Localisation::getTranslation('task_view_assign_label')}</div>

              <form id="assignTaskToUserForm" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" onsubmit="return confirm('{Localisation::getTranslation("task_view_assign_confirmation")}');">

                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                  <input id="input" class="fs-6" type="email" name="userIdOrEmail" placeholder="{Localisation::getTranslation('task_view_assign_placeholder')}"><br />
                {/if}

                {if !empty($list_qualified_translators)}
                <select  name="assignUserSelect" id="assignUserSelect" class="select mt-2">
                  <option value="">...</option>
                  {foreach $list_qualified_translators as $list_qualified_translator}
                  <option value="{$list_qualified_translator['user_id']}">{TemplateHelper::uiCleanseHTML($list_qualified_translator['name'])}</option>
                  {/foreach}
                </select>
                <br />
                {/if}

                <br />
                <a class="btngray-sm mt-2" onclick="$('#assignTaskToUserForm').submit();" href="#">
                  <img src="{urlFor name='home'}ui/img/add-user.svg" alt="Add user" class="mx-1" /> &nbsp;{Localisation::getTranslation('task_view_assign_button')}
                </a>
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
              </form>

            </div>

            <div class="w-50">
              <div class="fs-5 fw-bold w-75 mb-4 mt-4  d-block d-md-none"> Remove a user from deny list for this task:</div>
              <form id="removeUserFromDenyListForm" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" onsubmit="return confirm('{Localisation::getTranslation("task_view_assign_confirmation")}');" >

                <input type="text" class="fs-6 mb-4" id='input' name="userIdOrEmailDenyList" placeholder="{Localisation::getTranslation('task_view_assign_placeholder')}" /><br />
                <a class="btngray-sm mt-2" href="#" onclick="$('#removeUserFromDenyListForm').submit();">
                  <img src="{urlFor name='home'}ui/img/remove-user.svg" alt="remove user" class="mx-1" /> Remove User from deny list
                </a>
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
              </form>
            </div>
          </div>

          <a href="{urlFor name="task-search_translators" options="task_id.$task_id"}" class="btngray-sm mt-4 mb-2">
            <img src="{urlFor name='home'}ui/img/search-user.svg" alt="arrow" class="mx-1" ></i>&nbsp;Search for Translators
          </a>
        </div>
        {/if}

        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
        <div class="mb-2 mt-3">
          <strong>{Localisation::getTranslation('task_org_feedback_user_feedback')}</strong><hr />

          <form id="taskUserFeedback" enctype="application/x-www-form-urlencoded" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" accept-charset="utf-8">
            <textarea wrap="soft" style="width: 99%" maxlength="4096" rows="10" name="feedback" placeholder="{Localisation::getTranslation('task_org_feedback_1')}"></textarea>

            <div class="d-flex justify-content-between mt-2 flex-wrap">
              <span>
                <button type="submit" value="1" name="revokeTask" class="btngray-sm">
                  {Localisation::getTranslation('task_org_feedback_2')}
                </button>

                <label class="checkbox clear_brand">
                  <input type="checkbox" name="deny_user" value="1" /> Add user to deny list
                </label>
              </span>

              <span class="">
                <button type="submit" value="Submit" name="submit" class="btngray-sm me-2">
                  {Localisation::getTranslation('common_submit_feedback')}
                </button>
                <button type="reset" value="Reset" name="reset" class="btngray-sm">
                  {Localisation::getTranslation('common_reset')}
                </button>
              </span>
            </div>

            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
          </form>
        </div>
        {/if}

        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() == TaskStatusEnum::COMPLETE && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
          {if !empty($memsource_task)}
          <p class="mt-4">{Localisation::getTranslation('org_task_review_0')}</p>
          <p>
            <a class="btngray-sm" href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">
              {Localisation::getTranslation('org_task_review_download_output_file')}
            </a>
          </p>
          {/if}

          <h2 class="page-header mt-5">
            {Localisation::getTranslation('org_task_review_review_this_file')}
            <span class=" fs-4 text-muted ">{Localisation::getTranslation('org_task_review_1')}</span>
          </h2>

          <p>{Localisation::getTranslation('org_task_complete_provide_or_view_review')}</p>
          <p>
            <a class="btngray-sm me-2" href="{urlFor name="org-task-review" options="org_id.$org_id|task_id.$task_id"}">
              {Localisation::getTranslation('org_task_complete_provide_a_review')}
            </a>
            <a class="btngray-sm" href="{urlFor name="org-task-reviews" options="org_id.$org_id|task_id.$task_id"}">
              {Localisation::getTranslation('org_task_complete_view_reviews')}
            </a>
          </p>
        {/if}
        <!-- End Admin Assign -->

<!-- End Admin -->

        {if !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
        <!-- Source file -->
        <div class="card highlight_3">
          <div class="card-body p-0">
            <div class="accordion accordion-flush" id="acc3">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c3" aria-expanded="true">
                    Source file
                  </button>
                </h2>
                <div id="c3" class="accordion-collapse collapse show" data-bs-parent="#acc3">
                  <div class="accordion-body p-4">
                    <a href="{urlFor name="download-task" options="task_id.$task_id"}" class="btn btn-dark btn-sm mb-3">
                      <i class="bi bi-download me-1"></i> Download source file
                    </a>

                    {if !empty($file_preview_path)}

                      <div class="py-4 d-flex  justify-content-between align-items-center flex-wrap">
                        <div class="d-flex ">
                        <img src="{urlFor name='home'}ui/img/print.svg" alt="print" id="print" class="mx-4 d-none" />
                        <a class="d-none" href="https://docs.google.com/viewer?url={$file_preview_path}&embedded=true"  download="{$file_preview_path}"  id="download-file"> <img src="{urlFor name='home'}ui/img/download.svg" id="downing" alt="download" /> </a>
                        </div>
                      </div>
                      <div style="padding-bottom:56.25%; position:relative; display:block; width: 100%">
                        <iframe width="100%" height="100%" id="iframe" src="https://docs.google.com/viewer?url={$file_preview_path}&embedded=true" frameborder="0" allowfullscreen="" style="position:absolute; top:0; left: 0">
                        </iframe>
                      </div>

                    {/if}

                    <div id="head_confirm_read_source_instructions">
                      {if $is_claimer}
                      <button class="confirm-bar" type="button" id="confirm_read_source_instructions">
                        <i class="bi bi-check2-circle me-2"></i> I confirm I have reviewed the source file.
                      </button>
                      {/if}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {/if}

      </div>

      <!-- Right column -->
      <div class="col-12 col-lg-4">
        <div class="card mb-4 highlight_0">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="section-title mb-0">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text_short']} instructions</div>
              <a href="{TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_1']}" target="_blank" class="btn btn-dark btn-sm">
                <i class="bi bi-layout-text-window-reverse me-1"></i> Full instructions
              </a>
            </div>

            <p class="small mb-3">
              {TaskTypeEnum::$enum_to_UI[$type_id]['type_description']}
            </p>

            <div id="head_confirm_read_instructions">
              {if $is_claimer}
              <button class="btn btn-orange w-100" id="confirm_read_instructions">
                <i class="bi bi-check2-circle me-2"></i> I confirm I have read the task instructions.
              </button>
              {/if}
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body p-4">
            <div class="section-title">Do you have any questions or comments?</div>
            <a href="https://community.translatorswb.org/t/{$discourse_slug}" target="_blank" class="btn btn-dark w-100">
              <i class="bi bi-chat-left-text me-2"></i> Project forum
            </a>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- was main -->

<!-- Confirmation Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST" action="{urlFor name="task-view" options="task_id.$task_id"}">
        <div class="modal-header">
          <h5 class="modal-title">I confirm that:</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="task_id" value="{$task_id}" />
          <input type="hidden" name="mark_claim_task" value="1" />
          <input type="hidden" name="sesskey" value="{$sesskey}" />

          <div class="form-check mb-3">
            <input type="checkbox" name="confirm_capable" class="form-check-input confirm-check" value="1" required>
            <label class="form-check-label">
              I am capable to <strong>{TaskTypeEnum::$enum_to_UI[$type_id]['type_text_verb']}</strong> this file in <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>.
            </label>
          </div>

          <div class="form-check mb-3">
            <input type="checkbox" name="confirm_deadline" class="form-check-input confirm-check" value="1" required>
            <label class="form-check-label">
              I have the time to {TaskTypeEnum::$enum_to_UI[$type_id]['type_text_verb']} this file ({$task->getWordCount()} {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text_short']}) and I will complete it by <strong><span class="convert_utc_to_local_deadline_natural" style="visibility: hidden">{$task->getDeadline()}</span></strong>.
            </label>
          </div>

          {foreach $steps as $step}
            {if $step['this'] && $step['translations_not_all_complete']}
            <p class="text-muted small">
              You can start working on the task once the previous step has been completed.
            </p>
            {/if}
          {/foreach}
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Bring me back
          </button>

          <button type="submit" class="btn btn-primary" id="confirmBtn" disabled>
            I confirm I will {TaskTypeEnum::$enum_to_UI[$type_id]['type_text_verb']} this file.
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

{include file="footer2.tpl"}
