{include file="new_header.tpl"}

  {assign var="task_id" value=$task->getId()}
  {assign var="type_id" value=$task->getTaskType()}
  {assign var="project_id" value=$task->getProjectId()}

  <div class="container-fluid app-shell py-4"> <!-- was main -->
   <div id="add_instructions"></div>
   <div id="page-content">
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
              <div><a href="{$siteLocation}org/{$org_id}/profile" style="text-decoration: none;"><span class="soft-muted small">{TemplateHelper::uiCleanseHTML($org_name)}</span></a></div>
              <div><a href="{urlFor name="project-view" options="project_id.$project_id"}" style="text-decoration: none;"><span class="h4 mb-1" style="color: black;">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}</span></a></div>
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="soft-muted small">{TemplateHelper::uiCleanseHTML($task->getTitle())}</span>
                <span class="badge rounded-pill badge-task" style="color: {TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']} task</span>
              </div>
            </div>
          </div>

          <div class="ms-lg-auto">
            <button class="btn btn-orange" id="show-revision-btn">
              Review the instructions
            </button>
          </div>
        </div>

        <hr class="my-4">

        <!-- Stages -->
        <div class="row g-3">
          <div class="col-12 col-md-6 col-lg-3">
            <div class="stage-pill">
              <div class="d-flex align-items-start justify-content-between">
                <div>
                  <div class="stage-title"><i class="bi bi-translate me-1"></i> Translation</div>
                  <div class="stage-meta">In progress</div>
                </div>
                <i class="bi bi-dot text-warning fs-3"></i>
              </div>
              <div class="stage-meta mt-2">
                <i class="bi bi-clock me-1"></i> Due on January 20<br>
                10:00 (Europe/Madrid)
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3">
            <div class="stage-pill active">
              <div class="d-flex align-items-start justify-content-between">
                <div>
                  <div class="stage-title"><i class="bi bi-pencil-square me-1"></i> Revision</div>
                  <div class="stage-meta">Claimed</div>
                </div>
                <i class="bi bi-check-circle-fill text-primary fs-5"></i>
              </div>

              <div class="stage-meta mt-2">
                <i class="bi bi-clock me-1"></i> Due on January 26<br>
                10:00 (Europe/Madrid)
              </div>

              <div class="mt-3">
                <div class="list-group list-group-flush small">
                  <button class="list-group-item list-group-item-action px-0 py-1 border-0">
                    <i class="bi bi-info-circle me-2"></i> Review the instructions
                  </button>
                  <button class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary" disabled>
                    <i class="bi bi-wrench-adjustable-circle me-2"></i> Work on the task
                  </button>
                  <button class="list-group-item list-group-item-action px-0 py-1 border-0 text-secondary" disabled>
                    <i class="bi bi-chat-left-dots me-2"></i> Provide feedback
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3">
            <div class="stage-pill">
              <div class="d-flex align-items-start justify-content-between">
                <div>
                  <div class="stage-title"><i class="bi bi-spellcheck me-1"></i> Proofreading</div>
                  <div class="stage-meta">Pending</div>
                </div>
                <i class="bi bi-dash-circle text-secondary fs-5"></i>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 col-lg-3">
            <div class="stage-pill">
              <div class="d-flex align-items-start justify-content-between">
                <div>
                  <div class="stage-title"><i class="bi bi-truck me-1"></i> Delivery</div>
                  <div class="stage-meta">—</div>
                </div>
                <i class="bi bi-dash-circle text-secondary fs-5"></i>
              </div>
            </div>
          </div>
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
        <!-- Description -->
        <div class="card mb-4">
          <div class="card-body p-4">
            <div class="section-title">Description</div>
            <p class="mb-0">
              This project is a part of a series of polls/brochures for UNICEF. Our target audience are
              “U-Reporters” – 16-24 years old.
            </p>
          </div>
        </div>

        <!-- Project-specific instructions -->
        <div class="card mb-4">
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
                    <p class="mb-3">
                      Please make sure to use colloquial, gender-appropriate, and user-friendly language.
                      Remember to adjust your style to the target audience: youth/mothers on U-report social media pages.
                    </p>
                    <div class="small">
                      <div class="mb-2"><strong>French:</strong> Please ALWAYS use the informal “tu” and NEVER use “vous”!</div>
                      <div class="mb-3"><strong>Arabic:</strong> Please use a colloquial language, but always stick to MSA (Modern Standard Arabic).</div>
                      <div class="divider-soft my-3"></div>
                      <p class="mb-3">
                        This project has been pre-translated using <strong>machine translation (MT)</strong> and your task is to fully post-edit it.
                        Learn more about MT post-editing (MTPE) in our Community Library.
                      </p>

                      <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-book me-1"></i> MTPE instructions
                      </button>
                    </div>

                    <div class="mt-4">
                      <button class="confirm-bar" type="button">
                        <i class="bi bi-check2-circle me-2"></i> I confirm I have read the project-specific instructions.
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- References and style guides -->
        <div class="card mb-4">
          <div class="card-body p-0">
            <div class="accordion accordion-flush" id="acc2">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2">
                    References and style guides
                  </button>
                </h2>
                <div id="c2" class="accordion-collapse collapse" data-bs-parent="#acc2">
                  <div class="accordion-body p-4">
                    <div class="small mb-3">
                      <div class="soft-muted">Link:</div>
                      <a href="#" class="text-decoration-none">https://www.example.com/</a>
                    </div>

                    <button class="btn btn-outline-secondary btn-sm">
                      <i class="bi bi-file-earmark-text me-1"></i> Arabic style guide
                    </button>

                    <div class="mt-4">
                      <button class="confirm-bar" type="button">
                        <i class="bi bi-check2-circle me-2"></i> I confirm I have reviewed the references and style guides.
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Source file -->
        <div class="card">
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
                    <button class="btn btn-dark btn-sm mb-3">
                      <i class="bi bi-download me-1"></i> Download source file
                    </button>

                    <div class="preview-shell mb-3">
                      <div class="preview-btn">
                        <button class="btn btn-outline-dark">
                          <i class="bi bi-eye me-1"></i> Preview source file
                        </button>
                      </div>
                    </div>

                    <button class="confirm-bar" type="button">
                      <i class="bi bi-check2-circle me-2"></i> I confirm I have reviewed the source file.
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Right column -->
      <div class="col-12 col-lg-4">
        <div id=position_revision_instructions_card></div>
        <div class="card mb-4 revision-instructions-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="section-title mb-0">Revision instructions</div>
              <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-layout-text-window-reverse me-1"></i> Full instructions
              </button>
            </div>

            <p class="small mb-3">
              Review the translation against the source text and project requirements. Ensure accuracy, fluency,
              terminology, style, formatting, and consistency with provided resources. Apply necessary corrections,
              follow project-specific instructions, and preserve tags and layout. Run a QA check, resolve all issues,
              perform a final read, and seek clarification if needed before completion.
            </p>

            <button class="btn btn-orange w-100" id="confirm_read_instructions">
              <i class="bi bi-check2-circle me-2"></i> I confirm I have read the task instructions.
            </button>
          </div>
        </div>

        <div class="card">
          <div class="card-body p-4">
            <div class="section-title">Do you have any questions or comments?</div>
            <button class="btn btn-dark w-100">
              <i class="bi bi-chat-left-text me-2"></i> Project forum
            </button>
          </div>
        </div>
      </div>
    </div>
   </div>
  </div> <!-- was main -->

{include file="footer2.tpl"}
