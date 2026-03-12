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
              <div><a href="{urlFor name="project-view" options="project_id.$project_id"}" class="task_tooltip" style="text-decoration: none;"><span class="h4 mb-1" style="color: black;">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}</span><span class="task_tooltiptext">Project that this task belongs to</span></a></div>
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="soft-muted small">{TemplateHelper::uiCleanseHTML($task->getTitle())}</span>
                <span class="badge rounded-pill badge-task" style="color: {TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']} task</span>
                {if isset($chunks[$task_id])} - <span> [Part {$chunks[$task_id]['low_level'] }</span><span>/{$chunks[$task_id]['number_of_chunks'] }]</span>{/if}
              </div>
            </div>
          </div>

          <div class="ms-lg-auto">
            {if $status_id == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
              {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $LINGUIST + $NGO_LINGUIST)) && $user_within_limitations}
              <button class="btn btn-orange" id="claim_button">
                Claim the task
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
              <a href="{$matecat_url}" target="_blank" class="btn btn-orange">Work URL</a>
            {/if}
          </div>
        </div>

        <hr class="my-4">

        <!-- Stages -->
        <div class="row g-3">
          {if !$is_claimer && !($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) &&
            ($status_id >= TaskStatusEnum::PENDING_CLAIM || $is_denied_for_task || !$user_within_limitations || TaskTypeEnum::$enum_to_UI[$type_id]['shell_task'])}

            <div class="col-12 col-md-6 col-lg-6">
              <div class="stage-pill" style="background: WhiteSmoke">
                <div class="d-flex align-items-start justify-content-between">
                  <div>
                    <div>
                      This task is no longer available. Feel free to check if there are other open tasks, explore our Learning Center to keep building your skills, or visit our Community Library for practical guidelines, tips, and best practices.
                    </div>
                    <div>
                      <a class="btn btn-orange" href="{urlFor name="home"}">Available tasks</a>
                      <a class="btn btn-orange" href="https://elearn.translatorswb.org/" target="_blank"">Learning Center</a>
                      <a class="btn btn-orange" href="https://communitylibrary.translatorswb.org/login" target="_blank"">Community Library</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          {else}

          {foreach $steps as $step}
          <div class="col-12 col-md-6 col-lg-3">
            <div class="stage-pill{if $step['this']} active{/if}" {if $step['this']}style="background: #DFEEFD"{/if}>
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
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0" style="background: #C8DFF6">
                      <i class="bi bi-circle me-2"></i> Review the instructions
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary" style="background: #DFEEFD">
                      <i class="bi bi-circle me-2"></i> Work on the task
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary" style="background: #DFEEFD">
                      <i class="bi bi-circle me-2"></i> Provide feedback
                    </div>
                  </div>
                  {else}
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0" style="background: #DFEEFD">
                      <i class="bi bi-check-circle me-2"></i> Review the instructions
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary" style="background: #DFEEFD">
                      <i class="bi bi-check-circle me-2"></i> Work on the task
                    </div>
                    <div class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary" style="background: #C8DFF6">
                      <i class="bi bi-circle me-2"></i> Provide feedback
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

                    <div>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getComment())}</div>

                      {if $mt_used == 1}
                      <p class="mb-3">
                        This project has been pre-translated using <strong>machine translation (MT)</strong> and your task is to fully post-edit it.
                        Learn more about MT post-editing (MTPE) in our Community Library.
                      </p>

                      <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-book me-1"></i> MTPE instructions
                      </button>
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
                        {$language_style[0]['body']}
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
              <a href="{TaskTypeEnum::$enum_to_UI[$type_id]['bookstack_url_1']}" target="_blank" class="btn btn-outline-secondary btn-sm">
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
