<?php

namespace SolasMatch\UI\RouteHandlers;

use \SolasMatch\UI\DAO as DAO;
use \SolasMatch\UI\Lib as Lib;
use \SolasMatch\Common as Common;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__."/../../Common/Enums/TaskTypeEnum.class.php";
require_once __DIR__."/../../Common/Enums/TaskStatusEnum.class.php";
require_once __DIR__."/../../Common/lib/SolasMatchException.php";

class ProjectRouteHandler
{
    public function init()
    {
        global $app;

        $app->map(['GET', 'POST'],
            '/project/{project_id}/view[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:projectView')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('project-view');

        $app->map(['GET', 'POST'],
            '/project/{project_id}/alter[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:projectAlter')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgProject')
            ->setName('project-alter');

        $app->map(['GET', 'POST'],
            '/project/{org_id}/create[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:projectCreate')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg')
            ->setName('project-create');

        $app->map(['GET', 'POST'],
            '/project/{org_id}/create_empty[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:project_create_empty')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrg')
            ->setName('project-create-empty');

        $app->map(['GET', 'POST'],
            '/project/{project_id}/add_shell_tasks[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:project_add_shell_tasks')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgProject')
            ->setName('project-add-shell-tasks');

        $app->get(
            '/project/id/{project_id}/created[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:projectCreated')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgProject')
            ->setName('project-created');

        $app->get(
            '/project/id/{project_id}/mark-archived/{sesskey}[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:archiveProject')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForOrgProject')
            ->setName('archive-project');

        $app->get(
            '/project/{project_id}/file[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:downloadProjectFile')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserIsLoggedIn')
            ->setName('download-project-file');

        $app->get(
            '/project/{project_id}/image[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:downloadProjectImageFile')
            ->add('\SolasMatch\UI\Lib\Middleware:authUserForProjectImage')
            ->setName('download-project-image');

        $app->get(
            '/project_cron_1_minute',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:project_cron_1_minute')
            ->setName('project_cron_1_minute');

        $app->get(
            '/task_cron_1_minute',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:task_cron_1_minute')
            ->setName('task_cron_1_minute');

        $app->get(
            '/project/{project_id}/getwordcount[/]',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:project_get_wordcount')
            ->setName('project_get_wordcount');

        $app->map(['GET', 'POST'],
            '/memsource_hook',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:memsourceHook')
            ->setName('memsource_hook');

        $app->map(['GET', 'POST'],
            '/api_hook',
            '\SolasMatch\UI\RouteHandlers\ProjectRouteHandler:api_hook')
            ->setName('api_hook');
    }

    public function api_hook(Request $request)
    {
        global $app;
        if ($request->getHeaderLine('X-API-TOKEN') !== Common\Lib\Settings::get('api_hook.X-API-TOKEN')) {
            error_log('X-API-TOKEN does not match!');
            die;
        }
        $body = (string)$request->getBody();
        $hook = json_decode($body, true);
        if (empty($hook)) {
            error_log("api_hook not decoded: $body");
        }
        error_log('api_hook: ' . print_r(json_decode($body, true), true));
        die;
    }

    public function memsourceHook(Request $request)
    {
        global $app;
        if ($request->getHeaderLine('X-Memsource-Token') !== Common\Lib\Settings::get('memsource.X-Memsource-Token')) {
            error_log('X-Memsource-Token does not match!');
            die;
        }
        $body = (string)$request->getBody();
        $hook = json_decode($body, true);
        if (empty($hook)) {
            error_log("Hook not decoded: $body");
        }
        error_log($hook['event'] . ' ' . print_r(json_decode($body, true), true));

        switch ($hook['event']) {
            case 'PROJECT_CREATED':
                $this->create_project($hook);
                break;
            case 'PROJECT_DUE_DATE_CHANGED':
                $this->update_project_due_date($hook);
                break;
            case 'PROJECT_METADATA_UPDATED':
                $this->update_project_client($hook);
                break;
            case 'JOB_CREATED':
                $this->create_task($hook);
                break;
            case 'JOB_STATUS_CHANGED':
                $this->update_task_status($hook);
                break;
            case 'JOB_ASSIGNED':
                $this->job_assigned($hook);
                break;
            case 'JOB_DUE_DATE_CHANGED':
                $this->update_task_due_date($hook);
                break;
            case 'ANALYSIS_CREATED':
                $this->save_analysis($hook);
                break;
        }
        die;
    }

    private function create_project($hook)
    {
        $hook = $hook['project'];
        $project = new Common\Protobufs\Models\Project();
        $projectDao = new DAO\ProjectDao();
        if ($projectDao->get_memsource_project_by_memsource_id($hook['id']) || $projectDao->get_memsource_self_service_project($hook['id'])) {
            error_log("Self Service PROJECT_CREATED ignored: {$hook['id']}");
            return;
        }

        $project->setTitle(mb_substr($hook['name'], 0, 128));
        if (!empty($hook['note'])) $project->setDescription($this->truncate_note($hook['note']));
        else                       $project->setDescription('-');
        $project->setImpact('-');
        if (!empty($hook['dateDue'])) $project->setDeadline(substr($hook['dateDue'], 0, 10) . ' ' . substr($hook['dateDue'], 11, 8));
        else                          $project->setDeadline(gmdate('Y-m-d H:i:s', strtotime('25 days')));
        $project->setWordCount(1);
        list($trommons_source_language_code, $trommons_source_country_code) = $projectDao->convert_memsource_to_language_country($hook['sourceLang']);
        $source_language_pair = "{$trommons_source_language_code}-{$trommons_source_country_code}";
        $sourceLocale = new Common\Protobufs\Models\Locale();
        $sourceLocale->setCountryCode($trommons_source_country_code);
        $sourceLocale->setLanguageCode($trommons_source_language_code);
        $project->setSourceLocale($sourceLocale);

        if (empty($hook['client']['id'])) {
            error_log("No client id in new project: {$hook['name']}");
            $hook['client']['id'] = 0;
        }
        if ($hook['client']['id'] == 305070) {
            error_log('Testing Client (No KP link)');
            return;
        }
        $memsource_client = $projectDao->get_memsource_client_by_memsource_id($hook['client']['id']);
        if (empty($memsource_client)) {
            error_log("No MemsourceOrganisations record for new project: {$hook['name']}, client id: {$hook['client']['id']}");
            $memsource_client = ['org_id' => 790]; // SUPPORT TWB
        }
        $project->setOrganisationId($memsource_client['org_id']);

        if (!empty($hook['dateCreated'])) $project->setCreatedTime(substr($hook['dateCreated'], 0, 10) . ' ' . substr($hook['dateCreated'], 11, 8));
        else                              $project->setCreatedTime(gmdate('Y-m-d H:i:s'));

        $project = $projectDao->createProjectDirectly($project);
        error_log("Created Project (PROJECT_CREATED): {$hook['name']}");
        if (empty($project)) {
            error_log("Failed to create Project: {$hook['name']}");
            return;
        }

        $project_id = $project->getId();
        $destination = Common\Lib\Settings::get("files.upload_path") . "proj-$project_id/";
        mkdir($destination, 0755);

        $workflowLevels = ['', '', '', '', '', '', '', '', '', '', '', '']; // Will contain e.g. 'Translation' or 'Revision' for workflowLevel 1 possibly up to 12
        if (!empty($hook['workflowSteps'])) {
            foreach ($hook['workflowSteps'] as $step) {
                foreach ($workflowLevels as $i => $w) {
                    if ($step['workflowLevel'] == $i + 1) $workflowLevels[$i] = $step['name'];
                }
            }
        }

        $projectDao->set_memsource_project($project_id, $hook['id'], $hook['uid'],
            empty($hook['createdBy']['uid']) ? '' : $hook['createdBy']['uid'],
            empty($hook['owner']['uid']) ? '' : $hook['owner']['uid'],
            $workflowLevels);

        $target_languages = '';
        if (!empty($hook['targetLangs'])) {
            foreach ($hook['targetLangs'] as $index => $value) {
                list($trommons_source_language_code, $trommons_source_country_code) = $projectDao->convert_memsource_to_language_country($value);
                $hook['targetLangs'][$index] = "{$trommons_source_language_code}-{$trommons_source_country_code}";
            }
            $target_languages = implode(',', $hook['targetLangs']);
            $projectDao->record_memsource_project_languages($project_id, $source_language_pair, $target_languages);
        }

         $taskDao = new DAO\TaskDao();
         if ($taskDao->organisationHasQualifiedBadge($memsource_client['org_id'])) $taskDao->insert_project_restrictions($project_id, true, true);

        $org_id = $memsource_client['org_id'];
        if ($org_id != 790 && ($old_project_id = $projectDao->get_project_id_for_latest_org_image($org_id))) {
            $image_files = glob(Common\Lib\Settings::get('files.upload_path') . "proj-$old_project_id/image/image.*");
            if (!empty($image_files)) {
                $image_file = $image_files[0];
                $project_id = $project->getId();
                $destination = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/image";
                mkdir($destination, 0755);
                $ext = pathinfo($image_file, PATHINFO_EXTENSION);
                copy($image_file, "$destination/image.$ext");
                $projectDao->set_uploaded_approved($project_id);
            }
        }

        // Create a topic in the Community forum (Discourse)
        error_log("projectCreate create_discourse_topic($project_id, $target_languages)");
        try {
            $this->create_discourse_topic($project_id, $target_languages, ['owner_uid' => empty($hook['owner']['uid']) ? '' : $hook['owner']['uid']]);
        } catch (\Exception $e) {
            error_log('projectCreate create_discourse_topic Exception: ' . $e->getMessage());
        }
    }

    private function truncate_note($note)
    {
        if (mb_strlen($note) <= 4096) return $note;
        return mb_substr($note, 0, 4096 - 37) . ' THIS TEXT HAS BEEN EDITED FOR LENGTH';
    }

    private function update_project_due_date($hook)
    {
        $hook = $hook['project'];
        $projectDao = new DAO\ProjectDao();

        $memsource_project = $projectDao->get_memsource_project_by_memsource_id($hook['id']);
        if (empty($memsource_project)) {
            error_log("Can't find memsource_project for {$hook['id']} in event PROJECT_DUE_DATE_CHANGED");
            return;
        }
        if (!empty($hook['dateDue'])) $projectDao->update_project_due_date($memsource_project['project_id'], substr($hook['dateDue'], 0, 10) . ' ' . substr($hook['dateDue'], 11, 8));
        $projectDao->queue_asana_project($memsource_project['project_id']);
    }

    private function update_project_client($hook)
    {
        $hook = $hook['project'];
        $projectDao = new DAO\ProjectDao();

        $memsource_project = $projectDao->get_memsource_project_by_memsource_id($hook['id']);
        if (empty($memsource_project)) {
            error_log("Can't find memsource_project for {$hook['id']} in event PROJECT_METADATA_UPDATED");
            return;
        }

        if (!empty($hook['note'])) $projectDao->update_project_description($memsource_project['project_id'], $this->truncate_note($hook['note']));

        if (!empty($hook['workflowSteps'])) {
            $workflowLevels = ['', '', '', '', '', '', '', '', '', '', '', '']; // Will contain e.g. 'Translation' or 'Revision' for workflowLevel 1 possibly up to 12
            $found_something = 0;
            foreach ($hook['workflowSteps'] as $step) {
                foreach ($workflowLevels as $i => $w) {
                    if ($step['workflowLevel'] == $i + 1) {
                        $workflowLevels[$i] = $step['name'];
                        if (!empty($step['name'])) $found_something = 1;
                    }
                }
            }
            if ($found_something) $projectDao->update_memsource_project($memsource_project['project_id'], $workflowLevels);
        }

        if (!empty($hook['owner']['uid'])) {
            $projectDao->update_memsource_project_owner($memsource_project['project_id'], $hook['owner']['uid']);
            $projectDao->queue_asana_project($memsource_project['project_id']);
        }

        if (empty($hook['client']['id'])) return;
        $memsource_client = $projectDao->get_memsource_client_by_memsource_id($hook['client']['id']);
        if (empty($memsource_client)) {
            error_log("No MemsourceOrganisations record for project: {$hook['name']}, client id: {$hook['client']['id']} in event PROJECT_METADATA_UPDATED");
            return;
        }
        $projectDao->update_project_organisation($memsource_project['project_id'], $memsource_client['org_id']);
    }

    private function create_task($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();
        $memsource_project_sync = 0;
        $parent_tasks_filter = [];
        $split_uids_filter = [];
        foreach ($hook as $part) {
            $task = new Common\Protobufs\Models\Task();

            if (empty($part['fileName'])) {
                error_log("No fileName in new jobPart {$part['uid']}");
                continue;
            }
            if (empty($part['project']['id'])) {
                error_log("No project id in new jobPart {$part['uid']} for: {$part['fileName']}");
                continue;
            }
            $memsource_project = $projectDao->get_memsource_project_by_memsource_id($part['project']['id']);
            if (empty($memsource_project)) {
                error_log("Can't find memsource_project for {$part['project']['id']} in new jobPart {$part['uid']} for: {$part['fileName']}");
                continue;
            }
            $project_id = $memsource_project['project_id'];
            $task->setProjectId($project_id);
            $task->setTitle(mb_substr((empty($part['internalId']) ? '' : $part['internalId'] . ' ') . $part['fileName'], 0, 128));

            $project = $projectDao->getProject($project_id);

            if ($projectDao->is_job_uid_already_processed($part['uid'])) {
                error_log("Job uid is a duplicate for {$part['project']['id']} in new jobPart {$part['uid']} for: {$part['fileName']}");
            //    if (!empty($part['wordsCount'])) $taskDao->updateWordCountForProject($memsource_project['project_id'], $part['wordsCount']);

            //    $projectDao->update_memsource_task($memsource_task['task_id'], !empty($part['id']) ? $part['id'] : 0, $part['task'],
            //        empty($part['internalId'])    ? 0 : $part['internalId'],
            //        empty($part['beginIndex'])    ? 0 : $part['beginIndex'],
            //        empty($part['endIndex'])      ? 0 : $part['endIndex']);
                continue;
            }

            $projectSourceLocale = $project->getSourceLocale();
            $taskSourceLocale = new Common\Protobufs\Models\Locale();
            $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
            $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
            $task->setSourceLocale($taskSourceLocale);
            $task->setTaskStatus(Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);

            $taskTargetLocale = new Common\Protobufs\Models\Locale();
            list($target_language, $target_country) = $projectDao->convert_memsource_to_language_country($part['targetLang']);
            $taskTargetLocale->setLanguageCode($target_language);
            $taskTargetLocale->setCountryCode($target_country);
            $task->setTargetLocale($taskTargetLocale);

            if (empty($part['workflowLevel'])) {
                error_log("Can't find workflowLevel in new jobPart {$part['uid']} for: {$part['fileName']}, assuming Translation");
                $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
            } elseif ($part['workflowLevel'] > 12) {
                error_log("Don't support workflowLevel > 12: {$part['workflowLevel']} in new jobPart {$part['uid']} for: {$part['fileName']}");
                continue;
            } else {
                $workflow_levels = [$memsource_project['workflow_level_1'], $memsource_project['workflow_level_2'], $memsource_project['workflow_level_3'], $memsource_project['workflow_level_4'], $memsource_project['workflow_level_5'], $memsource_project['workflow_level_6'], $memsource_project['workflow_level_7'], $memsource_project['workflow_level_8'], $memsource_project['workflow_level_9'], $memsource_project['workflow_level_10'], $memsource_project['workflow_level_11'], $memsource_project['workflow_level_12']];
                $taskType = $workflow_levels[$part['workflowLevel'] - 1];
                error_log("taskType: $taskType, workflowLevel: {$part['workflowLevel']}");
                if (!empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$taskType])) $taskType = Common\Enums\TaskTypeEnum::$task_type_to_enum[$taskType];
                elseif ($taskType == '' && $part['workflowLevel'] == 1) {
                    $taskType = Common\Enums\TaskTypeEnum::TRANSLATION;
                    $workflow_levels = ['Translation'];
                } else {
                    error_log("Can't find expected taskType ($taskType) in new jobPart {$part['uid']} for: {$part['fileName']}");
                    continue;
                }
            }
            $task->setTaskType($taskType);

            if (!empty($part['wordsCount'])) {
                $task->setWordCount($part['wordsCount']);
                $task->set_word_count_partner_weighted($part['wordsCount']);
                $task->set_word_count_original($part['wordsCount']);
                $task->set_source_quantity($part['wordsCount']);
                $projectDao->queue_asana_project($project_id);
                if ($taskType == Common\Enums\TaskTypeEnum::TRANSLATION || $part['workflowLevel'] == 1) {
                    if (empty($part['internalId']) || (strpos($part['internalId'], '.') === false)) { // Only allow top level
                        $project_languages = $projectDao->get_memsource_project_languages($project_id);
error_log("Translation {$target_language}-{$target_country} vs first get_memsource_project_languages($project_id): {$project_languages['kp_target_language_pairs']} + {$part['wordsCount']}");//(**)
                        if (!empty($project_languages['kp_target_language_pairs'])) {
                            $project_languages = explode(',', $project_languages['kp_target_language_pairs']);
                            if ("{$target_language}-{$target_country}" === $project_languages[0]) {
error_log("Updating project_wordcount with {$part['wordsCount']}");//(**)
                                $projectDao->add_to_project_word_count($project_id, $part['wordsCount']);
                            }
                        }
                    }
                }
            } else {
                $task->setWordCount(1);
            }

            $self_service_project = $projectDao->get_memsource_self_service_project($part['project']['id']);
            if ($self_service_project) {
                $deadline = strtotime($project->getDeadline());
                $deadline_less_7_days = $deadline - 7*24*60*60; // 7-4 days Translation
                $deadline_less_4_days = $deadline - 4*24*60*60; // 4-1 days Revising
                $deadline_less_1_days = $deadline - 1*24*60*60; // 1 day for pm
                $now = time();
                $total = $deadline - $now;
                if ($total < 0) $total = 0;
                if ($deadline_less_7_days < $now) { // We are squashed for time
                    $deadline_less_4_days = $deadline - $total*4/7;
                    $deadline_less_1_days = $deadline - $total*1/7;
                }
                if ($self_service_project['split']) {
                    $deadline_less_4_days = $deadline - $total*45/100;
                    $deadline_less_1_days = $deadline - $total*5/100;
                }

                // If there is only 1 workflow, give all the time to that
                if (!empty($part['project']['lastWorkflowLevel']) && $part['project']['lastWorkflowLevel'] == 1) $deadline_less_4_days = $deadline_less_1_days;

                if ($taskType == Common\Enums\TaskTypeEnum::TRANSLATION) $task->setDeadline(gmdate('Y-m-d H:i:s', $deadline_less_4_days));
                else                                                     $task->setDeadline(gmdate('Y-m-d H:i:s', $deadline_less_1_days));
                $projectDao->set_dateDue_in_memsource_when_new($memsource_project['memsource_project_uid'], $part['uid'], gmdate('Y-m-d H:i:s', $taskType == Common\Enums\TaskTypeEnum::TRANSLATION ? $deadline_less_4_days : $deadline_less_1_days));

                $task->setPublished(1);
            } else {
                if (!empty($part['dateDue'])) $task->setDeadline(substr($part['dateDue'], 0, 10) . ' ' . substr($part['dateDue'], 11, 8));
                else                          $task->setDeadline($project->getDeadline());

                $task->setPublished(0);
            }

            $task_id = $taskDao->createTaskDirectly($task);
            if (!$task_id) {
                error_log("Failed to add task for new jobPart {$part['uid']} for: {$part['fileName']}");
                continue;
            }
            error_log("Added Task: $task_id for new jobPart {$part['uid']} for: {$part['fileName']}");

            $success = $projectDao->set_memsource_task($task_id, !empty($part['id']) ? $part['id'] : 0, $part['uid'], $part['task'], // note 'task' is for Language pair (independent of workflow step)
                empty($part['internalId'])    ? 0 : $part['internalId'],
                empty($part['workflowLevel']) ? 0 : $part['workflowLevel'],
                empty($part['beginIndex'])    ? 0 : $part['beginIndex'], // Begin Segment number
                empty($part['endIndex'])      ? 0 : $part['endIndex'],
                0);
error_log("set_memsource_task($task_id... {$part['uid']}...), success: $success");//(**)
            if (!$success) { // May be because of double hook?
                $projectDao->delete_task_directly($task_id);
                error_log("delete_task_directly($task_id) because of set_memsource_task fail");
                continue;
            }
            $projectDao->set_task_resource_info_trigger($task_id);

            $forward_order = [];
            $reverse_order = [];
            foreach ($workflow_levels as $i => $workflow_level) {
                if (!empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level])) {
                    $forward_order[Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level]] = ($i == 11 || empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i + 1]])) ? 0 : Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i + 1]];
                    $reverse_order[Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level]] = ($i ==  0 || empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i - 1]])) ? 0 : Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i - 1]];
                }
            }
            $top_level = $projectDao->get_top_level($part['internalId']);
            $project_tasks = $projectDao->get_tasks_for_project($project_id);
            foreach ($project_tasks as $project_task) {
                if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                    //(**) Matches on same file & same language, for QA or Proofreading may need to be wider
                    if ($forward_order[$taskType]) {
                         if ($forward_order[$taskType] == $project_task['task-type_id'])
                             $projectDao->set_taskclaims_required_to_make_claimable($task_id, $project_task['task_id'], $project_id);
                    }
                    if ($reverse_order[$taskType]) {
                         if ($reverse_order[$taskType] == $project_task['task-type_id'])
                             $projectDao->set_taskclaims_required_to_make_claimable($project_task['task_id'], $task_id, $project_id);
                    }
                }
            }

            if($projectDao->is_task_claimable($task_id)) $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::PENDING_CLAIM);

            $project_restrictions = $taskDao->get_project_restrictions($project_id);
            if ($project_restrictions && (
                    ($task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION  && $project_restrictions['restrict_translate_tasks'])
                        ||
                    ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING && $project_restrictions['restrict_revise_tasks']))) {
                $taskDao->setRestrictedTask($task_id);
            }

            if ($self_service_project) {
                $creator = $taskDao->get_self_creator_from_project_file($project_id);
                error_log("Tracking for Self Service Creator: {$creator['id']}");
                $taskDao->trackTaskDirectly($creator['id'], $task_id);
            }

            $uploadFolder = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/task-$task_id/v-0";
            mkdir($uploadFolder, 0755, true);
            $filesFolder = Common\Lib\Settings::get('files.upload_path') . "files/proj-$project_id/task-$task_id/v-0";
            mkdir($filesFolder, 0755, true);

            $filename = str_replace('/', '_', $part['fileName']);
            file_put_contents("$filesFolder/$filename", ''); // Placeholder
            file_put_contents("$uploadFolder/$filename", "files/proj-$project_id/task-$task_id/v-0/$filename"); // Point to it

            if (mb_strlen($filename) <= 255) $projectDao->queue_copy_task_original_file($project_id, $task_id, $part['uid'], $filename); // cron will copy file from memsource

            if ($self_service_project && $self_service_project['split'] && $task->getWordCount() > 2000 && $task->getTaskType() == Common\Enums\TaskTypeEnum::TRANSLATION) {
                error_log("Splitting project_id: $project_id, task_id: $task_id");
                $uid = $part['uid'];
                $memsource_project_uid = $memsource_project['memsource_project_uid'];
                $ch = curl_init("https://cloud.memsource.com/web/api2/v1/projects/$memsource_project_uid/jobs/$uid/split");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = [
                    'partCount' => ceil($task->getWordCount()/2000.),
                ];
                $payload = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $authorization = 'Authorization: Bearer ' . Common\Lib\Settings::get('memsource.memsource_api_token');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
                $result_exec = curl_exec($ch);
                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                error_log("responseCode: $responseCode");
                if ($responseCode == 200) {
                    $result = json_decode($result_exec, true);
error_log(print_r($result, true));//(**)
                    $memsource_project_sync = $memsource_project;
                    // Add filters to manipulate only one language in Sync to stop possible race
                    $parent_tasks_filter[] = $task_id;
                    foreach ($result['jobs'] as $job) {
                        $split_uids_filter[] = $job['uid'];
                    }
                    $words_default = round($task->getWordCount()/$data['partCount']);
                }
            }
        }
        if ($memsource_project_sync) $projectDao->sync_split_jobs($memsource_project_sync, $split_uids_filter, $parent_tasks_filter, $words_default, 1);
    }

    private function update_task_status($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();
        foreach ($hook as $part) {
            $memsource_task = $projectDao->get_memsource_task_by_memsource_uid($part['uid']);
            if (empty($memsource_task)) {
                error_log("Can't find memsource_task for {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: {$part['status']}");
                continue;
            }
            $task_id = $memsource_task['task_id'];
            $projectDao->set_task_resource_info_trigger($task_id);

            $taskDao->set_memsource_status($task_id, $part['uid'], $part['status']);
error_log("task_id: $task_id, memsource_task for {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: {$part['status']}");//(**)

            if ($part['status'] == 'ASSIGNED') {
                if (!empty($part['assignedTo'][0]['linguist']['uid']) && count($part['assignedTo']) == 1) {
                    $user_id = $projectDao->get_user_id_from_memsource_user($part['assignedTo'][0]['linguist']['uid']);
                    if (!$user_id) {
                        error_log("Can't find user_id for {$part['assignedTo'][0]['linguist']['uid']} in event JOB_STATUS_CHANGED, jobPart status: ASSIGNED");
                        $user_id = 62927; // translators@translatorswithoutborders.org
//(**)dev server                        $user_id = 3297;
                    }

                    if (!$taskDao->taskIsClaimed($task_id)) {
                        $taskDao->claimTaskAndDeny($task_id, $user_id, $memsource_task);
                        error_log("JOB_STATUS_CHANGED ASSIGNED in memsource task_id: $task_id, user_id: $user_id, memsource job: {$part['uid']}, user: {$part['assignedTo'][0]['linguist']['uid']}");
                    } else { // Probably being set by admin in Memsource from COMPLETED_BY_LINGUIST back to ASSIGNED
                      if ($taskDao->getTaskStatus($task_id) == Common\Enums\TaskStatusEnum::COMPLETE) {
                        $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::IN_PROGRESS);
                        error_log("ASSIGNED task_id: $task_id, memsource: {$part['uid']}, reverting from COMPLETED_BY_LINGUIST");
                      }
                    }
                }
            }
            if ($part['status'] == 'COMPLETED_BY_LINGUIST' || $part['status'] == 'COMPLETED') {
                if (!$taskDao->taskIsClaimed($task_id)) $taskDao->claimTask($task_id, 62927); // translators@translatorswithoutborders.org
//(**)dev server                if (!$taskDao->taskIsClaimed($task_id)) $taskDao->claimTask($task_id, 3297);

              if ($taskDao->getTaskStatus($task_id) != Common\Enums\TaskStatusEnum::COMPLETE) {
                $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::COMPLETE);
                $taskDao->sendTaskUploadNotifications($task_id, 1);
                $taskDao->set_task_complete_date($task_id);
                error_log("COMPLETED_BY_LINGUIST task_id: $task_id, memsource: {$part['uid']}");
              }
            }
            if ($part['status'] == 'DECLINED_BY_LINGUIST' || $part['status'] == 'NEW') {
                if ($taskDao->taskIsClaimed($task_id)) {
                    if (empty($part['project']['id'])) {
                        error_log("No project id in {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: DECLINED_BY_LINGUIST");
                        continue;
                    }
                    $memsource_project = $projectDao->get_memsource_project_by_memsource_id($part['project']['id']);
                    if (empty($memsource_project)) {
                        error_log("Can't find memsource_project for {$part['project']['id']} in {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: DECLINED_BY_LINGUIST");
                        continue;
                    }
                    $user_id = $projectDao->getUserClaimedTask($task_id);
                    if ($user_id) {
                        $taskDao->unclaimTask($task_id, $user_id);
                        $taskDao->sendOrgFeedbackDeclined($task_id, $user_id, $memsource_project);
                    }
                    error_log("JOB_STATUS_CHANGED DECLINED_BY_LINGUIST in memsource task_id: $task_id, user_id: $user_id, memsource job: {$part['uid']}");
                }
            }
            if ($part['status'] == 'CANCELLED') {
                if (empty($part['project']['id'])) {
                    error_log("No project id in {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: CANCELLED");
                    continue;
                }
                $memsource_project = $projectDao->get_memsource_project_by_memsource_id($part['project']['id']);
                if (empty($memsource_project)) {
                    error_log("Can't find memsource_project for {$part['project']['id']} in {$part['uid']} in event JOB_STATUS_CHANGED, jobPart status: CANCELLED");
                    continue;
                }
                $userDao = new DAO\UserDao();
                $userDao->propagate_cancelled(1, $memsource_project, $task_id, 'Hook from Phrase', 1, 1);
                error_log("JOB_STATUS_CHANGED CANCELLED in memsource task_id: $task_id, memsource job: {$part['uid']}");
            }
        }
    }

    private function job_assigned($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();
        foreach ($hook as $part) {
            if (!empty($part['assignedTo'][0]['linguist']['uid']) && count($part['assignedTo']) == 1) {
                $memsource_task = $projectDao->get_memsource_task_by_memsource_uid($part['uid']);
                if (empty($memsource_task)) {
                    error_log("Can't find memsource_task for {$part['uid']} in event JOB_ASSIGNED jobPart");
                    continue;
                }
                $task_id = $memsource_task['task_id'];
                $projectDao->set_task_resource_info_trigger($task_id);

                $user_id = $projectDao->get_user_id_from_memsource_user($part['assignedTo'][0]['linguist']['uid']);
                if (!$user_id) {
                    error_log("Can't find user_id for {$part['assignedTo'][0]['linguist']['uid']} in event JOB_ASSIGNED jobPart");
                    $user_id = 62927; // translators@translatorswithoutborders.org
//(**)dev server                    $user_id = 3297;
                }

                if (!empty($part['status']) && in_array($part['status'], ['NEW', 'EMAILED', 'DECLINED_BY_LINGUIST'])) continue;

                if (!$taskDao->taskIsClaimed($task_id)) {
                    $taskDao->claimTaskAndDeny($task_id, $user_id, $memsource_task);
                    error_log("JOB_ASSIGNED in memsource task_id: $task_id, user_id: $user_id, memsource job: {$part['uid']}, user: {$part['assignedTo'][0]['linguist']['uid']}");
                }
            }
        }
    }

    private function update_task_due_date($hook)
    {
        $hook = $hook['jobParts'];
        $projectDao = new DAO\ProjectDao();
        foreach ($hook as $part) {
            $memsource_task = $projectDao->get_memsource_task_by_memsource_uid($part['uid']);
            if (empty($memsource_task)) {
                error_log("Can't find memsource_task for {$part['uid']} in event JOB_DUE_DATE_CHANGED");
                continue;
            }
            if (!empty($part['dateDue'])) $projectDao->update_task_due_date($memsource_task['task_id'], substr($part['dateDue'], 0, 10) . ' ' . substr($part['dateDue'], 11, 8));
        }
    }

    private function save_analysis($hook)
    {
        $hook = $hook['analyse'];
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        if (!empty($hook['analyseLanguageParts'][0]['data']['repetitions']) && !empty($hook['analyseLanguageParts'][0]['jobs'][0]['uid'])) {
            $memsource_task = $projectDao->get_memsource_task_by_memsource_uid($hook['analyseLanguageParts'][0]['jobs'][0]['uid']);
            if ($memsource_task) {
                $task_id = $memsource_task['task_id'];
                $claimant_id = $projectDao->getUserClaimedTask($task_id);
                $analyse_uid = $hook['uid'];
                $analyse_id  = $hook['id'];
                if (!$projectDao->get_requested_analysis($analyse_id)) {
                    error_log("Not Requested analyse_id: $analyse_id ($analyse_uid)");
                    return;
                }
                $memsource_project_uid = $hook['project']['uid'];
                $task = $taskDao->getTask($task_id);
                list($translation_level, $revision_level) = $this->get_workflow_levels($task->getProjectId());
                $this_level = $memsource_task['workflowLevel'];
                $compare_workflow_level = $translation_level;
                if ($translation_level == $this_level) $compare_workflow_level = $revision_level;
                if ($hook['type'] == 'PostAnalyse') $projectDao->insert_post_analysis($task_id, $claimant_id, $analyse_uid, $memsource_project_uid, $this_level, $hook['analyseLanguageParts'][0]['data']);
                if ($hook['type'] == 'Compare')  $projectDao->insert_compare_analysis($task_id, $claimant_id, $analyse_uid, $memsource_project_uid, $this_level, $compare_workflow_level, $hook['analyseLanguageParts'][0]['data']);
            } else error_log('ANALYSIS_CREATED Bad job uid');
        } else error_log('ANALYSIS_CREATED Bad hook');
    }

    public function get_workflow_levels($project_id) {
        $projectDao = new DAO\ProjectDao();
        $memsource_project = $projectDao->get_memsource_project($project_id);
        $workflow_levels = [$memsource_project['workflow_level_1'], $memsource_project['workflow_level_2'], $memsource_project['workflow_level_3'], $memsource_project['workflow_level_4'], $memsource_project['workflow_level_5'], $memsource_project['workflow_level_6'], $memsource_project['workflow_level_7'], $memsource_project['workflow_level_8'], $memsource_project['workflow_level_9'], $memsource_project['workflow_level_10'], $memsource_project['workflow_level_11'], $memsource_project['workflow_level_12']];
        $translation_level = 0;
        $revision_level = 0;
        foreach ($workflow_levels as $level => $name) {
            if (!$translation_level && $name == 'Translation') $translation_level = $level + 1;
            if (!$revision_level    && $name == 'Revision'   ) $revision_level    = $level + 1;
        }
        return [$translation_level, $revision_level];
    }

    public function projectView(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $project_id = $args['project_id'];
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $adminDao = new DAO\AdminDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();
        $orgDao = new DAO\OrganisationDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        $project = $projectDao->getProject($project_id);
        if (empty($project)) {
            UserRouteHandler::flash('error', 'That project does not exist!');
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $org = $orgDao->getOrganisation($project->getOrganisationId());
        $project_tags = $projectDao->getProjectTags($project_id);
        $roles = $adminDao->get_roles($user_id, $project->getOrganisationId());

        if ($taskDao->isUserRestrictedFromProject($project_id, $user_id)) {
            UserRouteHandler::flash('error', 'You cannot access this project!');
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $memsource_project = $projectDao->get_memsource_project($project_id);

        $reload_for_wordcount = 0;
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'projectView')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) {
                if (isset($post['translators_count'])) {
                    $response->getBody()->write(json_encode($taskDao->count_users_who_can_claim($post['translators_count'])));
                    return $response->withHeader('Content-Type', 'application/json');
                }

                if (isset($post['matching'])) {
                    error_log('updateRequiredTaskNativeMatching(' . $post['task_id'] . ', ' . $post['matching'] . "): $user_id");
                    $response->getBody()->write(json_encode($taskDao->updateRequiredTaskNativeMatching($post['task_id'], $post['matching'])));
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }

            $task = null;
            if (isset($post['task_id'])) {
                $task = $taskDao->getTask($post['task_id']);
            } elseif (isset($post['revokeTaskId'])) {
                $task = $taskDao->getTask($post['revokeTaskId']);
            }

            if (($roles & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) && isset($post['publishedTask']) && isset($post['task_id'])) {
                if ($post['publishedTask']) {
                    $task->setPublished(true);
                    UserRouteHandler::flashNow("success", " Task published  ");

                } else {
                    $task->setPublished(false);
                    UserRouteHandler::flashNow("success", "Task unpublished");

                }
                error_log('projectView setPublished(' . $post['publishedTask'] . ') ' . $post['task_id'] . " by $user_id");
                $taskDao->updateTask($task);
            }

            if (isset($post['trackProject'])) {
                if ($post['trackProject']) {
                    $userTrackProject = $userDao->trackProject($user_id, $project->getId());                    
                    $this->assign_user_to_task($project->getId(), $user_id);
                    $this->follow_discource_project($project->getId(), $user_id);
                    if ($userTrackProject) {
                        UserRouteHandler::flashNow("success", Lib\Localisation::getTranslation('project_view_7'));
                    } else {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('project_view_8'));
                    }
                } else {
                    $userUntrackProject = $userDao->untrackProject($user_id, $project->getId());
                    $this->remove_user_from_task($project->getId(), $user_id);
                    $this->unfollow_discource_project($project->getId(), $user_id);
                    if ($userUntrackProject) {
                        UserRouteHandler::flashNow("success", Lib\Localisation::getTranslation('project_view_9'));
                    } else {
                        UserRouteHandler::flashNow("error", Lib\Localisation::getTranslation('project_view_10'));
                    }
                }
            } elseif (isset($post['trackTask'])) {
                if ($task && $task->getTitle() != "") {
                    $task_title = $task->getTitle();
                } else {
                    $task_title = "task {$task->getId()}";
                }

                if (!$post['trackTask']) {
                    $response_dao = $userDao->untrackTask($user_id, $task->getId());
                    if ($response_dao) {
                        UserRouteHandler::flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('project_view_11'), $task_title)
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('project_view_12'), $task_title)
                        );
                    }
                } else {
                    $response_dao = $userDao->trackTask($user_id, $post['task_id']);
                    if ($response_dao) {
                        UserRouteHandler::flashNow(
                            "success",
                            sprintf(Lib\Localisation::getTranslation('project_view_13'), $task_title)
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            sprintf(Lib\Localisation::getTranslation('project_view_14'), $task_title)
                        );
                    }
                }
            }

            if ($roles & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) {
            if (isset($post['deleteTask'])) {
                $taskDao->deleteTask($post['task_id']);
                UserRouteHandler::flashNow(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('project_view_15'), $task->getTitle())
                );
                error_log('deleteTask(): ' . $post['task_id'] . " by $user_id");
            }
            if (isset($post['archiveTask'])) {
                $taskDao->archiveTask($post['task_id'], $user_id);
                UserRouteHandler::flashNow(
                    "success",
                    sprintf(Lib\Localisation::getTranslation('project_view_16'), $task->getTitle())
                );
                error_log('archiveTask(): ' . $post['task_id'] . " by $user_id");
            }
            }

            if (isset($post['imageApprove'])) {
                if (!$post['imageApprove']) {
                    $project->setImageApproved(1);
                    $result = $projectDao->setProjectImageStatus($project_id, 1);
                    if ($result)
                    {
                        UserRouteHandler::flashNow(
                            "success",
                            Lib\Localisation::getTranslation('project_view_image_approve_success')
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            Lib\Localisation::getTranslation('project_view_image_approve_failed')
                        );
                    }
                } else {
                    $project->setImageApproved(0);
                    $result = $projectDao->setProjectImageStatus($project_id, 0);
                    if ($result)
                    {
                        UserRouteHandler::flashNow(
                            "success",
                            Lib\Localisation::getTranslation('project_view_image_disapprove_success')
                        );
                    } else {
                        UserRouteHandler::flashNow(
                            "error",
                            Lib\Localisation::getTranslation('project_view_image_approve_failed')
                        );
                    }
                }
            }

            if (!empty($post['copyChunks']) && !empty($memsource_project)) {
                $error = $projectDao->sync_split_jobs($memsource_project);
                if ($error) UserRouteHandler::flashNow('error', $error);
                $reload_for_wordcount = 1;
            }
            if ($roles & (SITE_ADMIN | PROJECT_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) {
                if (!empty($post['unpublish_selected_tasks']) || !empty($post['publish_selected_tasks'])) {
                    $tasks = [];
                    if (!empty($post['unpublish_selected_tasks'])) {
                        $task_ids = preg_split ("/\,/", $post['unpublish_selected_tasks']);
                        $published = false;
                        error_log("Tasks Marked Unpublished by $user_id, IDs: " . $post['unpublish_selected_tasks']);
                    }
                    if(!empty($post['publish_selected_tasks'])) {
                        $task_ids = preg_split ("/\,/", $post['publish_selected_tasks']);
                        $published = true;
                        error_log("Tasks Marked Published by $user_id, IDs: " . $post['publish_selected_tasks']);
                    }
                    
                    foreach ($task_ids as $id) {
                        $tasks[] = $taskDao->getTask($id);
                    }

                    // Unpublish selected tasks
                    if (!empty($tasks)) {
                        foreach ($tasks as $project_task) {
                            $project_task->setPublished($published);
                            $taskDao->updateTask($project_task);
                        }
                        UserRouteHandler::flashNow('success', count($tasks) . ' tasks now marked as published/unpublished.');
                    }
                }

                if (isset($post['cancelled'])) {
                    $comment = ($post['cancelled'] == 1) ? $post['cancel_task'] . ' - ' . $post['reason'] : 'Uncancelled';
                    $task_ids = preg_split ("/\,/", $post['cancel']);
                    $cancelled = $post['cancelled'] ? 1 : 0;
                    $number = 0;
                    foreach ($task_ids as $id) {
                        $number += $userDao->propagate_cancelled($cancelled, $memsource_project, $id, $comment, empty($post['cancel_selected_only']) ? 1 : 0);
                    }
                    error_log("$number Tasks Marked Cancelled($cancelled) by $user_id, IDs: " . $post['cancel']);
                    UserRouteHandler::flashNow('success', $cancelled ? "$number tasks cancelled." : "$number tasks uncancelled.");
                }
            }
            if ($roles & (SITE_ADMIN | PROJECT_OFFICER) || in_array($project->getOrganisationId(), ORG_EXCEPTIONS) && $roles & (NGO_ADMIN + NGO_PROJECT_OFFICER)) {
                $number = 0;
                if (!empty($post['complete_selected_tasks'])) {
                    foreach (preg_split('/\,/', $post['complete_selected_tasks']) as $task_id) {
                        if (Common\Enums\TaskTypeEnum::$enum_to_UI[$taskDao->getTask($task_id)->getTaskType()]['shell_task'] && $taskDao->taskIsClaimed($task_id) && $taskDao->getTaskStatus($task_id) != Common\Enums\TaskStatusEnum::COMPLETE) {
                            $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::COMPLETE);
                           //$taskDao->sendTaskUploadNotifications($task_id, 1);
                           $taskDao->set_task_complete_date($task_id);
                           error_log("Shell Task Marked Completed task_id: $task_id, by $user_id");
                           $number++;
                        }
                    }
                    UserRouteHandler::flashNow('success', "$number in progress shell tasks now marked as complete.");
                }
                if (!empty($post['uncomplete_selected_tasks'])) {
                    foreach (preg_split('/\,/', $post['uncomplete_selected_tasks']) as $task_id) {
                        if (Common\Enums\TaskTypeEnum::$enum_to_UI[$taskDao->getTask($task_id)->getTaskType()]['shell_task'] && $taskDao->getTaskStatus($task_id) == Common\Enums\TaskStatusEnum::COMPLETE) {
                            $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::IN_PROGRESS);
                            error_log("Shell Task Marked UnCompleted task_id: $task_id, by $user_id");
                            $number++;
                        }
                    }
                    UserRouteHandler::flashNow('success', "$number completed shell tasks now marked as in progress.");
                }
            }
            if ($roles & (SITE_ADMIN | PROJECT_OFFICER)) {
                if (!empty($post['tasks_as_paid'])) {
                    $task_ids = preg_split ("/\,/", $post['tasks_as_paid']);
                    foreach ($task_ids as $id) {
                        $taskDao->set_paid_status($id);
                    }
                    UserRouteHandler::flashNow('success', count($task_ids) . ' tasks now marked as paid.');
                }

                if (!empty($post['tasks_as_unpaid'])) {
                    $task_ids = preg_split ("/\,/", $post['tasks_as_unpaid']);
                    foreach ($task_ids as $id) {
                        $taskDao->clear_paid_status($id);
                    }
                    UserRouteHandler::flashNow('success', count($task_ids) . ' tasks now marked as unpaid.');
                }
            }

            if ($roles & (SITE_ADMIN | PROJECT_OFFICER) || in_array($project->getOrganisationId(), ORG_EXCEPTIONS) && $roles & (NGO_ADMIN + NGO_PROJECT_OFFICER)) {
                if (!empty($post['status_as_unclaimed'])) {
                    $task_ids = preg_split ("/\,/", $post['status_as_unclaimed']);
                    foreach ($task_ids as $id) {
                        $project_task = $taskDao->getTask($id);
                        if ($project_task->getTaskStatus() == Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES) {
                            $taskDao->setTaskStatus($id, Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                        }
                    }
                    error_log("Tasks potentially Marked PENDING_CLAIM by $user_id, IDs: " . $post['status_as_unclaimed']);
                }

                if (!empty($post['status_as_waiting'])) {
                    $has_unclaimed_prerequisites = $projectDao->tasks_with_unclaimed_prerequisites($project_id);
                    $task_ids = preg_split ("/\,/", $post['status_as_waiting']);
                    foreach ($task_ids as $id) {
                        if (!empty($has_unclaimed_prerequisites[$id])) {
                            $project_task = $taskDao->getTask($id);
                            if ($project_task->getTaskStatus() == Common\Enums\TaskStatusEnum::PENDING_CLAIM) {
                                $taskDao->setTaskStatus($id, Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);
                            }
                        }
                    }
                    error_log("Tasks potentially Marked WAITING_FOR_PREREQUISITES by $user_id, IDs: " . $post['status_as_waiting']);
                }
            }
            if ($roles & (SITE_ADMIN | PROJECT_OFFICER)) {
                $updated = 0;
                if (!empty($post['ready_payment'])) {
                    $task_ids = preg_split ("/\,/", $post['ready_payment']);
                    foreach ($task_ids as $id) {                      
                        $paid_status = $taskDao->get_paid_status($id);
                        if ($paid_status && $paid_status['payment_status'] == 'Pending documentation') {
                            $updated++;
                            $paid_status['payment_status'] = 'Ready for payment';
                            $paid_status['status_changed'] = date('Y-m-d H:i:s');
                            $taskDao->update_paid_status($paid_status);
                        }
                    }
                    UserRouteHandler::flashNow('success', "$updated tasks now marked as ready for payment.");
                }

                if (!empty($post['pending_documentation'])) {
                    $task_ids = preg_split ("/\,/", $post['pending_documentation']);
                    foreach ($task_ids as $id) {
                        $paid_status = $taskDao->get_paid_status($id);
                        if ($paid_status && $paid_status['payment_status'] == 'Ready for payment') {
                            $updated++;
                            $paid_status['payment_status'] = 'Pending documentation';
                            $paid_status['status_changed'] = date('Y-m-d H:i:s');
                            $taskDao->update_paid_status($paid_status);
                        }
                    }
                    UserRouteHandler::flashNow('success', "$updated tasks now marked as pending documentation.");
                }

                if (!empty($post['tasks_settled'])) {
                    $task_ids = preg_split ("/\,/", $post['tasks_settled']);
                    foreach ($task_ids as $id) {
                        $paid_status = $taskDao->get_paid_status($id);
                        if ($paid_status && $paid_status['payment_status'] == 'Ready for payment') {
                            $updated++;
                            $paid_status['payment_status'] = 'Settled';
                            $paid_status['status_changed'] = date('Y-m-d H:i:s');
                            $taskDao->update_paid_status($paid_status);
                        }
                    }
                    UserRouteHandler::flashNow('success', "$updated tasks now marked as settled.");
                }

                if (!empty($post['ponum']) && !empty($post['po'])) {
                    if (is_numeric($post['po'])) {
                        $task_ids = preg_split ("/\,/", $post['ponum']);
                        foreach ($task_ids as $id) {
                            $paid_status = $taskDao->get_paid_status($id);
                            if ($paid_status && $paid_status['purchase_order'] != (int)$post['po']) {
                                $updated++;
                                $paid_status['payment_status'] = 'Unsettled';
                                $paid_status['status_changed'] = date('Y-m-d H:i:s');
                                $paid_status['purchase_order'] = (int)$post['po'];
                                $taskDao->update_paid_status($paid_status);
                            }
                        }
                        UserRouteHandler::flashNow('success', "$updated tasks purchase order number set.");
                    } else UserRouteHandler::flashNow('error', 'Purchase Order must be an integer.');
                }
            }
            if ($roles & (SITE_ADMIN | PROJECT_OFFICER) || in_array($project->getOrganisationId(), ORG_EXCEPTIONS) && $roles & (NGO_ADMIN + NGO_PROJECT_OFFICER)) {
                if (!empty($post['complete_task'])) {
                    $task_id = $post['task_id'];
                    $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::COMPLETE);
                    //$taskDao->sendTaskUploadNotifications($task_id, 1);
                    $taskDao->set_task_complete_date($task_id);
                    error_log("Shell Task Marked Completed task_id: $task_id, by $user_id");
                    UserRouteHandler::flashNow('success', 'In progress shell task now marked as complete.');
                }
            }
        }

        $total_expected_cost = 0;
        $total_expected_price = 0;
        $total_expected_cost_waived = 0;
        $one_paid = 0;
        $payment_status_for_project = [];

        if ($roles & (SITE_ADMIN | PROJECT_OFFICER | COMMUNITY_OFFICER | NGO_ADMIN | NGO_PROJECT_OFFICER)) {
            $userSubscribedToProject = $userDao->isSubscribedToProject($user_id, $project_id);
            $taskMetaData = array();
            $project_tasks = $projectDao->getProjectTasks($project_id);
            $translations_not_all_complete = $projectDao->identify_claimed_but_not_yet_in_progress($project_id);
            $taskLanguageMap = array();
            $get_payment_status_for_project = [];
            if ($project_tasks) {
                $get_payment_status_for_project = $taskDao->get_payment_status_for_project($project_id);
                foreach ($project_tasks as $task) {
                    $task_id = $task->getId();
                    if (!empty($translations_not_all_complete[$task_id])) $task->setTaskStatus(Common\Enums\TaskStatusEnum::CLAIMED);
                    $targetLocale = $task->getTargetLocale();
                    $taskTargetLanguage = $targetLocale->getLanguageCode();
                    $taskTargetCountry = $targetLocale->getCountryCode();
                    $taskLanguageMap["$taskTargetLanguage,$taskTargetCountry"][] = $task;
                    $metaData = array();
                    $response_dao = $userDao->isSubscribedToTask($user_id, $task_id);
                    if ($response_dao == 1) {
                        $metaData['tracking'] = true;
                        $userSubscribedToProject = 1; // For self service projects, $userSubscribedToProject will not have been set (other projects are not initially tracked for creator)
                    } else {
                        $metaData['tracking'] = false;
                    }
                    $taskMetaData[$task_id] = $metaData;

                    $get_payment_status_for_project[$task_id]['target_codes'] = "$taskTargetLanguage,$taskTargetCountry";
                }
            }

          
            $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}resources/bootstrap/js/bootstrap.min.js\"></script>";
            $extra_scripts .= "<script defer type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/taskRestrictions.js\"></script>";
            $extra_scripts .= "<script defer type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/projectDescClean.js\"></script>";
            $extra_scripts .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" type="text/javascript"></script>';
            $extra_scripts .= file_get_contents(__DIR__."/../js/project-view1.js");
            $extra_scripts .= file_get_contents(__DIR__."/../js/TaskView3.js");
            // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
            $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

            $template_data = array_merge($template_data, array(
                    "org" => $org,
                    "extra_scripts" => $extra_scripts,
                    "projectTasks" => $project_tasks,
                    "taskMetaData" => $taskMetaData,
                    "userSubscribedToProject" => $userSubscribedToProject,
                    "project_tags" => $project_tags,
                    "taskLanguageMap" => $taskLanguageMap
            ));

            $sort = [];
            foreach ($get_payment_status_for_project as $t_id => $payment_status) {
                $total_expected_cost += $payment_status['total_expected_cost'];
                $total_expected_price += $payment_status['total_expected_price'];
                if (in_array($payment_status['payment_status'], ['In-kind', 'In-house', 'Waived'])) $total_expected_cost_waived += $payment_status['total_expected_cost'];
                if ($payment_status['payment_status']) $one_paid = 1;
                $sort[$payment_status['target_codes'] . $payment_status['type_enum'] . $t_id] = $t_id;
            }
            ksort($sort);
            foreach ($sort as $t_id) $payment_status_for_project[$t_id] = $get_payment_status_for_project[$t_id];
        } else {
            $project_tasks = $taskDao->getVolunteerProjectTasks($project_id, $user_id);
            $translations_not_all_complete = $projectDao->identify_claimed_but_not_yet_in_progress($project_id);
            $volunteerTaskLanguageMap = array();
            foreach ($project_tasks as $task) {
                if (!empty($translations_not_all_complete[$task['task_id']])) $task['status_id'] = Common\Enums\TaskStatusEnum::CLAIMED;
                $volunteerTaskLanguageMap[$task['target_language_code'] . ',' . $task['target_country_code']][] = $task;
            }

            $extra_scripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}resources/bootstrap/js/bootstrap.min.js\"></script>";
            $extra_scripts .= file_get_contents(__DIR__."/../js/TaskView3.js");
            // Load Twitter JS asynch, see https://dev.twitter.com/web/javascript/loading
            $extra_scripts .= '<script>window.twttr = (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {}; if (d.getElementById(id)) return t; js = d.createElement(s); js.id = id; js.src = "https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); t._e = []; t.ready = function(f) { t._e.push(f); }; return t; }(document, "script", "twitter-wjs"));</script>';

            $template_data = array_merge($template_data, array(
                "extra_scripts" => $extra_scripts,
                "org" => $org,
                'volunteerTaskLanguageMap' => $volunteerTaskLanguageMap,
                "project_tags" => $project_tags
            ));
        }

        $preventImageCacheToken = time(); //see http://stackoverflow.com/questions/126772/how-to-force-a-web-browser-not-to-cache-images

        $creator = $taskDao->get_creator($project_id, $memsource_project);
        $pm = $creator['email'];
        if (strpos($pm, '@translatorswithoutborders.org') === false && strpos($pm, '@clearglobal.org') === false) $pm = 'projects@translatorswithoutborders.org';

        if ($reload_for_wordcount) $project = $projectDao->getProject($project_id);

        $template_data = array_merge($template_data, array(
                'sesskey'       => $sesskey,
                'roles'         => $roles,
                "imgCacheToken" => $preventImageCacheToken,
                'discourse_slug' => $projectDao->discourse_parameterize($project),
                'memsource_project'   => $memsource_project,
                'matecat_analyze_url' => ($memsource_project && !preg_match('/^\d*$/', $memsource_project['memsource_project_uid'])) ? $taskDao->get_matecat_analyze_url($project_id, $memsource_project) : '',
                'pm' => $pm,
                'project' => $project,
                'get_paid_for_project' => $taskDao->get_paid_for_project($project_id),
                'get_payment_status_for_project' => $payment_status_for_project,
                'users_who_claimed' => $projectDao->get_users_who_claimed($project_id),
                'project_complete_date'        => $taskDao->get_project_complete_date($project_id),
                'total_expected_cost'          => $total_expected_cost,
                'total_expected_price'         => $total_expected_price,
                'total_expected_cost_waived'   => $total_expected_cost_waived,
                'one_paid'                     => $one_paid,
        ));

        return UserRouteHandler::render("project/project.view.tpl", $response);
    }

    public function projectAlter(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $project_id = $args['project_id'];
        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $projectDao = new DAO\ProjectDao();
        $taskDao    = new DAO\TaskDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = $this->random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $project = $projectDao->getProject($project_id);
        if (empty($project)) {
            UserRouteHandler::flash('error', 'That project does not exist!');
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $memsource_project = $projectDao->get_memsource_project($project_id);

        if ($post = $request->getParsedBody()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey
                    || empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact'])
                    || empty($post['project_deadline'])
                    || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $post['project_deadline'])) {
                // Note the deadline date validation above is only partial (these checks have been done more rigorously on client size, if that is to be trusted)
                UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_to_create_project'), htmlspecialchars($post['project_title'], ENT_COMPAT, 'UTF-8')));
            } else {
                $sourceLocale = new Common\Protobufs\Models\Locale();

                $project->setTitle(mb_substr($post['project_title'], 0, 128));
                $project->setDescription($post['project_description']);
                $set_dateDue_in_memsource = $project->getDeadline() != $post['project_deadline'];
                $project->setDeadline($post['project_deadline']);
                $projectDao->queue_asana_project($project_id);
                $project->setImpact($post['project_impact']);
                $project->setReference($post['project_reference']);
                // Done by DAOupdateProjectWordCount(), which only saves it conditionally...
                // $project->setWordCount($post['wordCountInput']);

                $project->clearTag();
                if (!empty($post['tagList'])) {
                    $tagLabels = explode(' ', $post['tagList']);
                    foreach ($tagLabels as $tagLabel) {
                        $tagLabel = trim($tagLabel);
                        if (!empty($tagLabel)) {
                            $tag = new Common\Protobufs\Models\Tag();
                            $tag->setLabel($tagLabel);
                            $project->addTag($tag);
                        }
                    }
                }

                try {
                    error_log('projectAlter: ' . $project->getId());
                    $project = $projectDao->updateProject($project);
                } catch (\Exception $e) {
                    $project = null;
                }
                if (empty($project) || $project->getId() <= 0) {
                    UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('project_create_title_conflict'));
                } else {
                    if (false) { // Code copied from Project Create
                    } else {
                        if (false) { // Code copied from Project Create
                        } else {
                            $image_failed = false;
                            if (!empty($_FILES['projectImageFile']['name'])) {
                                $projectImageFileName = $_FILES['projectImageFile']['name'];
                                $extensionStartIndex = strrpos($projectImageFileName, '.');
                                // Check that file has an extension
                                if ($extensionStartIndex > 0) {
                                    $extension = substr($projectImageFileName, $extensionStartIndex + 1);
                                    $extension = strtolower($extension);
                                    $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . $extension;

                                    // Check that the file extension is valid for an image
                                    if (!in_array($extension, explode(",", Common\Lib\Settings::get('projectImages.supported_formats')))) {
                                        $image_failed = true;
                                    }
                                } else {
                                    // File has no extension
                                    $image_failed = true;
                                }

                                if ($image_failed || !empty($_FILES['projectImageFile']['error']) || empty($_FILES['projectImageFile']['tmp_name'])
                                        ||(($data = file_get_contents($_FILES['projectImageFile']['tmp_name'])) === false)) {
                                    $image_failed = true;
                                } else {
                                    $imageMaxWidth  = Common\Lib\Settings::get('projectImages.max_width');
                                    $imageMaxHeight = Common\Lib\Settings::get('projectImages.max_height');
                                    list($width, $height) = getimagesize($_FILES['projectImageFile']['tmp_name']);

                                    if (empty($width) || empty($height) || (($width <= $imageMaxWidth) && ($height <= $imageMaxHeight))) {
                                        try {
                                            $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                            $success = true;
                                        } catch (\Exception $e) {
                                            $success = false;
                                        }
                                    } else { // Resize the image
                                        $ratio = min($imageMaxWidth / $width, $imageMaxHeight / $height);
                                        $newWidth  = floor($width * $ratio);
                                        $newHeight = floor($height * $ratio);

                                        $img = '';
                                        if ($extension == 'gif') {
                                            $img = imagecreatefromgif($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } elseif ($extension == 'png') {
                                            $img = imagecreatefrompng($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } else {
                                            $img = imagecreatefromjpeg($_FILES['projectImageFile']['tmp_name']);
                                        }

                                        $tci = imagecreatetruecolor($newWidth, $newHeight);
                                        if (!empty($img) && $tci !== false) {
                                            if (imagecopyresampled($tci, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                                                imagejpeg($tci, $_FILES['projectImageFile']['tmp_name'], 100); // Overwrite
                                                // If we did not get this far, give up and use the un-resized image
                                            }
                                        }

                                        $data = file_get_contents($_FILES['projectImageFile']['tmp_name']);
                                        if ($data !== false) {
                                            try {
                                                $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                                $success = true;
                                            } catch (\Exception $e) {
                                                $success = false;
                                            }
                                        } else {
                                            $success = false;
                                        }
                                    }
                                    if (!$success) {
                                        $image_failed = true;
                                    }
                                }
                            }
                            if ($image_failed) {
                                UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_image'), htmlspecialchars($_FILES['projectImageFile']['name'], ENT_COMPAT, 'UTF-8')));
                            } else {
                                // Continue here whether there is, or is not, an image file uploaded as long as there was not an explicit failure
                                try {
                                     if ($set_dateDue_in_memsource) $projectDao->set_dateDue_in_memsource_for_project($memsource_project, $post['project_deadline']);

                                     if (!empty($post['project_hubspot']) && is_numeric($post['project_hubspot']) && ($post['project_hubspot'] = (int)$post['project_hubspot']) > 1) {
                                         if ($post['project_hubspot'] != $taskDao->get_project_complete_date($project_id)['deal_id']) $taskDao->update_project_deal_id($project_id, $post['project_hubspot']);
                                         if ($taskDao->update_hubspot_deals($post['project_hubspot']) != 1) UserRouteHandler::flash('error', 'Deal ID not found in HubSpot table');
                                     }
                                     if (!empty($post['project_allocated_budget']) && is_numeric($post['project_allocated_budget']) && ($post['project_allocated_budget'] = (int)$post['project_allocated_budget']) > 0) {
                                         if ($post['project_allocated_budget'] != $taskDao->get_project_complete_date($project_id)['allocated_budget']) $taskDao->update_project_allocated_budget($project_id, $post['project_allocated_budget']);
                                     }
                                     return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', array('project_id' => $project->getId())));
                                } catch (\Exception $e) { // redirect throws \Slim\Exception\Stop
                                }
                            }
                        }
                    }
                }
            }
        }

        $langDao = new DAO\LanguageDao();
        $languages = $langDao->getLanguages();
        $countryDao = new DAO\CountryDao();
        $countries = $countryDao->getCountries();

        $month_list = array(
            1 => Lib\Localisation::getTranslation('common_january'),
            2 => Lib\Localisation::getTranslation('common_february'),
            3 => Lib\Localisation::getTranslation('common_march'),
            4 => Lib\Localisation::getTranslation('common_april'),
            5 => Lib\Localisation::getTranslation('common_may'),
            6 => Lib\Localisation::getTranslation('common_june'),
            7 => Lib\Localisation::getTranslation('common_july'),
            8 => Lib\Localisation::getTranslation('common_august'),
            9 => Lib\Localisation::getTranslation('common_september'),
            10 => Lib\Localisation::getTranslation('common_october'),
            11 => Lib\Localisation::getTranslation('common_november'),
            12 => Lib\Localisation::getTranslation('common_december'),
        );
        $hour_list = array();
        for ($i = 0; $i < 24; $i++) {
            $hour_list[$i] = $i;
        }
        $minute_list = array();
        $minutei = (int)date('Y');
        for ($i = 0; $i < 60; $i++) {
            $minute_list[$i] = $i;
        }

        $project = $projectDao->getProject($project_id);
        $deadline = $project->getDeadline();
        $selected_year   = (int)substr($deadline,  0, 4);
        $year_list = [];
        $yeari = (int)date('Y');
        if ($selected_year < $yeari) $year_list[$selected_year] = $selected_year;
        for ($i = 0; $i < 10; $i++) {
            $year_list[$yeari] = $yeari;
            $yeari++;
        }
        $selected_month  = (int)substr($deadline,  5, 2);
        $selected_day    = (int)substr($deadline,  8, 2);
        $selected_hour   = (int)substr($deadline, 11, 2); // These are UTC, they will be recalculated to local time by JavaScript (we do not what the local time zone is)
        $selected_minute = (int)substr($deadline, 14, 2);
        $deadline_timestamp = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

        $sourceLocale = $project->getSourceLocale();
        $sourceCountrySelectCode  = $sourceLocale->getCountryCode();
        $sourceLanguageSelectCode = $sourceLocale->getLanguageCode();

        $project_tags_list = '';
        try {
            $project_tags = $projectDao->getProjectTags($project_id);
            if (!empty($project_tags)) {
                $separator = '';
                foreach ($project_tags as $project_tag) {
                    $project_tags_list .= $separator . $project_tag->getLabel();
                    $separator = ' ';
                }
            }
        } catch (\Exception $e) {
        }

        $adminDao = new DAO\AdminDao();
        $roles = $adminDao->get_roles($user_id, $project->getOrganisationId());

        $extraScripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extraScripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/ProjectAlter3.js\"></script>";
        $extraScripts .= '<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>';

        $template_data = array_merge($template_data, array(
            "siteLocation"          => Common\Lib\Settings::get('site.location'),
            "siteAPI"               => Common\Lib\Settings::get('site.api'),
            "maxFileSize"           => Lib\TemplateHelper::maxFileSizeBytes(),
            "imageMaxFileSize"      => Common\Lib\Settings::get('projectImages.max_image_size'),
            "supportedImageFormats" => Common\Lib\Settings::get('projectImages.supported_formats'),
            "project"        => $project,
            "project_tags"   => $project_tags_list,
            "project_id"     => $project_id,
            "org_id"         => $project->getOrganisationId(),
            "user_id"        => $user_id,
            "extra_scripts"  => $extraScripts,
            'deadline_timestamp' => $deadline_timestamp,
            'selected_day'   => $selected_day,
            'month_list'     => $month_list,
            'selected_month' => $selected_month,
            'year_list'      => $year_list,
            'selected_year'  => $selected_year,
            'hour_list'      => $hour_list,
            'selected_hour'  => $selected_hour,
            'minute_list'    => $minute_list,
            'selected_minute'=> $selected_minute,
            'languages'      => $languages,
            'countries'      => $countries,
            'sourceLanguageSelectCode' => $sourceLanguageSelectCode,
            'sourceCountrySelectCode'  => $sourceCountrySelectCode,
            'roles'             => $roles,
            'enter_analyse_url' => 0,
            'memsource_project' => $memsource_project,
            'project_complete_date' => $taskDao->get_project_complete_date($project_id),
            'sesskey'        => $sesskey,
        ));

        return UserRouteHandler::render("project/project.alter.tpl", $response);
    }

    public function projectCreate(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];

        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $adminDao = new DAO\AdminDao();
        $projectDao = new DAO\ProjectDao();
        $orgDao = new DAO\OrganisationDao();
        $subscriptionDao = new DAO\SubscriptionDao();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();

        if (empty($_SESSION['SESSION_CSRF_KEY'])) {
            $_SESSION['SESSION_CSRF_KEY'] = $this->random_string(10);
        }
        $sesskey = $_SESSION['SESSION_CSRF_KEY']; // This is a check against CSRF (Posts should come back with same sesskey)

        $create_memsource = 1; // This org uses memsource

        if ($post = $request->getParsedBody()) {
            if (empty($post['sesskey']) || $post['sesskey'] !== $sesskey
                    || empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact'])
                    || empty($post['sourceLanguageSelect']) || empty($post['project_deadline'])
                    || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $post['project_deadline'])
                    ) {
                // Note the deadline date validation above is only partial (these checks have been done more rigorously on client size, if that is to be trusted)
                UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_to_create_project'), htmlspecialchars($post['project_title'], ENT_COMPAT, 'UTF-8')));
            } else {
                $sourceLocale = new Common\Protobufs\Models\Locale();
                $project = new Common\Protobufs\Models\Project();

                $project->setTitle(mb_substr($post['project_title'], 0, 128));
                $project->setDescription($post['project_description']);
                $project->setDeadline($post['project_deadline']);
                $project->setImpact($post['project_impact']);
                $project->setReference($post['project_reference']);
                $project->setWordCount(1); // Code in taskInsertAndUpdate() does not support 0, so use 1 as placeholder

                list($trommons_source_language_code, $trommons_source_country_code) = $projectDao->convert_selection_to_language_country($post['sourceLanguageSelect']);
                $sourceLocale->setCountryCode($trommons_source_country_code);
                $sourceLocale->setLanguageCode($trommons_source_language_code);
                $project->setSourceLocale($sourceLocale);

                $project->setOrganisationId($org_id);
                $project->setCreatedTime(gmdate('Y-m-d H:i:s'));

                $project->clearTag();
                if (!empty($post['tagList'])) {
                    $tagLabels = explode(' ', $post['tagList']);
                    foreach ($tagLabels as $tagLabel) {
                        $tagLabel = trim($tagLabel);
                        if (!empty($tagLabel)) {
                            $tag = new Common\Protobufs\Models\Tag();
                            $tag->setLabel($tagLabel);
                            $project->addTag($tag);
                        }
                    }
                }
                if (!empty($post['earthquake'])) {
                    $tag = new Common\Protobufs\Models\Tag();
                    $tag->setLabel('2023-turkeysyria');
                    $project->addTag($tag);
                }

                try {
                    $project = $projectDao->createProjectDirectly($project);
                    error_log('Created Project: ' . $post['project_title']);
                } catch (\Exception $e) {
                    $project = null;
                }
                if (empty($project) || $project->getId() <= 0) {
                    UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('project_create_title_conflict'));
                } else {
                    if (empty($_FILES['projectFile']['name']) || !empty($_FILES['projectFile']['error']) || empty($_FILES['projectFile']['tmp_name'])
                            || (($data = file_get_contents($_FILES['projectFile']['tmp_name'])) === false)) {
                        UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), htmlspecialchars($_FILES['projectFile']['name'], ENT_COMPAT, 'UTF-8')));
                        error_log('Project Upload Error: ' . $post['project_title']);
                        try {
                            $projectDao->deleteProject($project->getId());
                        } catch (\Exception $e) {
                        }
                    } else {
                        $success = true;
                        $projectFileName = $_FILES['projectFile']['name'];
                        $extensionStartIndex = strrpos($projectFileName, '.');
                        // Check that file has an extension
                        if ($extensionStartIndex > 0) {
                             $extension = substr($projectFileName, $extensionStartIndex + 1);
                             $extension = strtolower($extension);
                             $projectFileName = substr($projectFileName, 0, $extensionStartIndex + 1) . $extension;
                            if (in_array($extension, ['pdf', 'jpg', 'png', 'gif'])) {
                                error_log("Project File wrong extension $projectFileName ($user_id): " . $post['project_title']);
                                $success = false;
                            }
                        }
                        if ($success) {
                        try {
                            $projectDao->saveProjectFile($project, $user_id, $projectFileName, $data);
                            error_log("Project File Saved($user_id): " . $post['project_title']);
                            if ($create_memsource) {
                                $memsource_project = $userDao->create_memsource_project($post, $project, $projectFileName, $data);
                                if (!$memsource_project) $success = false;
                            } else $memsource_project = 0;
                        } catch (\Exception $e) {
                            error_log("Project File Save Error($user_id): " . $post['project_title'] . ' ' . $e->getMessage());
                            $success = false;
                        }
                        }
                        if (!$success) {
                            UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('common_error_file_stopped_by_extension')));
                            try {
                                $projectDao->deleteProject($project->getId());
                            } catch (\Exception $e) {
                            }
                        } else {
                            $image_failed = false;
                            if (!empty($_FILES['projectImageFile']['name'])) {
                                $projectImageFileName = $_FILES['projectImageFile']['name'];
                                $extensionStartIndex = strrpos($projectImageFileName, '.');
                                // Check that file has an extension
                                if ($extensionStartIndex > 0) {
                                    $extension = substr($projectImageFileName, $extensionStartIndex + 1);
                                    $extension = strtolower($extension);
                                    $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . $extension;

                                    // Check that the file extension is valid for an image
                                    if (!in_array($extension, explode(",", Common\Lib\Settings::get('projectImages.supported_formats')))) {
                                        $image_failed = true;
                                    }
                                } else {
                                    // File has no extension
                                    $image_failed = true;
                                }

                                if ($image_failed || !empty($_FILES['projectImageFile']['error']) || empty($_FILES['projectImageFile']['tmp_name'])
                                        ||(($data = file_get_contents($_FILES['projectImageFile']['tmp_name'])) === false)) {
                                    $image_failed = true;
                                } else {
                                    $imageMaxWidth  = Common\Lib\Settings::get('projectImages.max_width');
                                    $imageMaxHeight = Common\Lib\Settings::get('projectImages.max_height');
                                    list($width, $height) = getimagesize($_FILES['projectImageFile']['tmp_name']);

                                    if (empty($width) || empty($height) || (($width <= $imageMaxWidth) && ($height <= $imageMaxHeight))) {
                                        try {
                                            $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                            $success = true;
                                        } catch (\Exception $e) {
                                            $success = false;
                                        }
                                    } else { // Resize the image
                                        $ratio = min($imageMaxWidth / $width, $imageMaxHeight / $height);
                                        $newWidth  = floor($width * $ratio);
                                        $newHeight = floor($height * $ratio);

                                        $img = '';
                                        if ($extension == 'gif') {
                                            $img = imagecreatefromgif($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } elseif ($extension == 'png') {
                                            $img = imagecreatefrompng($_FILES['projectImageFile']['tmp_name']);
                                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                        } else {
                                            $img = imagecreatefromjpeg($_FILES['projectImageFile']['tmp_name']);
                                        }

                                        $tci = imagecreatetruecolor($newWidth, $newHeight);
                                        if (!empty($img) && $tci !== false) {
                                            if (imagecopyresampled($tci, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                                                imagejpeg($tci, $_FILES['projectImageFile']['tmp_name'], 100); // Overwrite
                                                // If we did not get this far, give up and use the un-resized image
                                            }
                                        }

                                        $data = file_get_contents($_FILES['projectImageFile']['tmp_name']);
                                        if ($data !== false) {
                                            try {
                                                $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                                $success = true;
                                            } catch (\Exception $e) {
                                                $success = false;
                                            }
                                        } else {
                                            $success = false;
                                        }
                                    }
                                    if (!$success) {
                                        $image_failed = true;
                                    }
                                }
                            } else { // If no image uploaded, copy an old one
                                if ($old_project_id = $projectDao->get_project_id_for_latest_org_image($org_id)) {
                                    $image_files = glob(Common\Lib\Settings::get('files.upload_path') . "proj-$old_project_id/image/image.*");
                                    if (!empty($image_files)) {
                                        $image_file = $image_files[0];
                                        $project_id = $project->getId();
                                        $destination = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/image";
                                        mkdir($destination, 0755);
                                        $ext = pathinfo($image_file, PATHINFO_EXTENSION);
                                        copy($image_file, "$destination/image.$ext");
                                        $projectDao->set_uploaded_approved($project_id);
                                    }
                                }
                            }
                            if ($image_failed) {
                                UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_image'), htmlspecialchars($_FILES['projectImageFile']['name'], ENT_COMPAT, 'UTF-8')));
                                try {
                                    $projectDao->deleteProject($project->getId());
                                } catch (\Exception $e) {
                                }
                            } else {
                                // Continue here whether there is, or is not, an image file uploaded as long as there was not an explicit failure
                                    try {
                                        $target_languages = '';
                                        $targetCount = 0;
                                        if (!empty($post["target_language_$targetCount"])) {
                                            list($trommons_language_code, $trommons_country_code) = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
                                            $target_languages = $trommons_language_code . '-' . $trommons_country_code;
                                        }
                                        $targetCount++;
                                        while (!empty($post["target_language_$targetCount"])) {
                                            list($trommons_language_code, $trommons_country_code) = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
                                            $target_languages .= ',' . $trommons_language_code . '-' . $trommons_country_code;
                                            $targetCount++;
                                        }

                                        $restrict_translate_tasks = !empty($post['restrict_translate_tasks']);
                                        $restrict_revise_tasks    = !empty($post['restrict_revise_tasks']);
                                        if ($restrict_translate_tasks || $restrict_revise_tasks) $taskDao->insert_project_restrictions($project->getId(), $restrict_translate_tasks, $restrict_revise_tasks);

                                        // Create a topic in the Community forum (Discourse)
                                        error_log('projectCreate create_discourse_topic(' . $project->getId() . ", $target_languages)");
                                        try {
                                           $this->create_discourse_topic($project->getId(), $target_languages, 0, !empty($post['earthquake']));
                                        } catch (\Exception $e) {
                                            error_log('projectCreate create_discourse_topic Exception: ' . $e->getMessage());
                                        }
                                        try {
                                            UserRouteHandler::flash('success', 'It may take a while for all Tasks to appear below, please refresh the page until they all appear');
                                            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', array('project_id' => $project->getId())));
                                        } catch (\Exception $e) { // redirect throws \Slim\Exception\Stop
                                        }
                                    } catch (\Exception $e) {
                                        UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), htmlspecialchars($_FILES['projectFile']['name'], ENT_COMPAT, 'UTF-8')));
                                        try {
                                            error_log('projectCreate deleteProject(' . $project->getId() . ")");
                                            $projectDao->deleteProject($project->getId());
                                        } catch (\Exception $e) {
                                        }
                                    }
                            }
                        }
                    }
                }
            }
        }

        $month_list = array(
            1 => Lib\Localisation::getTranslation('common_january'),
            2 => Lib\Localisation::getTranslation('common_february'),
            3 => Lib\Localisation::getTranslation('common_march'),
            4 => Lib\Localisation::getTranslation('common_april'),
            5 => Lib\Localisation::getTranslation('common_may'),
            6 => Lib\Localisation::getTranslation('common_june'),
            7 => Lib\Localisation::getTranslation('common_july'),
            8 => Lib\Localisation::getTranslation('common_august'),
            9 => Lib\Localisation::getTranslation('common_september'),
            10 => Lib\Localisation::getTranslation('common_october'),
            11 => Lib\Localisation::getTranslation('common_november'),
            12 => Lib\Localisation::getTranslation('common_december'),
        );

        $year_list = array();
        $yeari = (int)date('Y');
        for ($i = 0; $i < 10; $i++) {
            $year_list[$yeari] = $yeari;
            $yeari++;
        }
        $hour_list = array();
        for ($i = 0; $i < 24; $i++) {
            $hour_list[$i] = $i;
        }
        $minute_list = array();
        $minutei = (int)date('Y');
        for ($i = 0; $i < 60; $i++) {
            $minute_list[$i] = $i;
        }
        $week = strtotime('+1 week');
        $selected_year   = (int)date('Y', $week);
        $selected_month  = (int)date('n', $week);
        $selected_day    = (int)date('j', $week);
        $selected_hour   = (int)date('G', $week); // These are UTC, they will be recalculated to local time by JavaScript (we do not know what the local time zone is)
        $selected_minute = 0;
        $deadline_timestamp = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

        $extraScripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extraScripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/ProjectCreate12.js\"></script>";

        $template_data = array_merge($template_data, array(
            "siteLocation"          => Common\Lib\Settings::get('site.location'),
            "siteAPI"               => Common\Lib\Settings::get('site.api'),
            "maxFileSize"           => Lib\TemplateHelper::maxFileSizeBytes(),
            "imageMaxFileSize"      => Common\Lib\Settings::get('projectImages.max_image_size'),
            "supportedImageFormats" => Common\Lib\Settings::get('projectImages.supported_formats'),
            "org_id"         => $org_id,
            "user_id"        => $user_id,
            'subscription_text' => null,
            "extra_scripts"  => $extraScripts,
            'deadline_timestamp' => $deadline_timestamp,
            'selected_day'   => $selected_day,
            'month_list'     => $month_list,
            'selected_month' => $selected_month,
            'year_list'      => $year_list,
            'selected_year'  => $selected_year,
            'hour_list'      => $hour_list,
            'selected_hour'  => $selected_hour,
            'minute_list'    => $minute_list,
            'selected_minute'=> $selected_minute,
            'create_memsource'=> $create_memsource,
            'languages'      => $projectDao->generate_language_selection($create_memsource),
            'showRestrictTask' => $taskDao->organisationHasQualifiedBadge($org_id),
            'roles'          => $adminDao->get_roles($user_id, $org_id),
            'sesskey'        => $sesskey,
            'template1'      => '{"source": "en-GB", "targets": ["zh-CN", "zh-TW", "th-TH", "vi-VN", "id-ID", "tl-PH", "ko-KR", "ja-JP", "ms-MY", "my-MM", "hi-IN", "bn-IN"]}',
            'template2'      => '{"source": "en-GB", "targets": ["ar-SA", "hi-IN", "swh-KE", "fr-FR", "es-49", "pt-BR"]}',
        ));
        return UserRouteHandler::render("project/project.create.tpl", $response);
    }

    public function project_create_empty(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $org_id = $args['org_id'];

        $user_id = Common\Lib\UserSession::getCurrentUserID();

        $adminDao = new DAO\AdminDao();
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $userDao = new DAO\UserDao();

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if ($post = $request->getParsedBody()) {
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'project_create_empty')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

            if (empty($post['project_title']) || empty($post['project_description']) || empty($post['project_impact']) || empty($post['sourceLanguageSelect']) || empty($post['project_deadline']) ||
                !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $post['project_deadline'])) {
                // Note the deadline date validation above is only partial (these checks have been done more rigorously on client size, if that is to be trusted)
                UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_to_create_project'), htmlspecialchars($post['project_title'], ENT_COMPAT, 'UTF-8')));
            } else {
                $sourceLocale = new Common\Protobufs\Models\Locale();
                $project = new Common\Protobufs\Models\Project();

                $project->setTitle(mb_substr($post['project_title'], 0, 128));
                $project->setDescription($post['project_description']);
                $project->setDeadline($post['project_deadline']);
                $project->setImpact($post['project_impact']);
                $project->setReference($post['project_reference']);
                $project->setWordCount(1); // Code in taskInsertAndUpdate() does not support 0, so use 1 as placeholder

                list($trommons_source_language_code, $trommons_source_country_code) = $projectDao->convert_selection_to_language_country($post['sourceLanguageSelect']);
                $sourceLocale->setCountryCode($trommons_source_country_code);
                $sourceLocale->setLanguageCode($trommons_source_language_code);
                $project->setSourceLocale($sourceLocale);

                $project->setOrganisationId($org_id);
                $project->setCreatedTime(gmdate('Y-m-d H:i:s'));

                $project->clearTag();
                if (!empty($post['tagList'])) {
                    $tagLabels = explode(' ', $post['tagList']);
                    foreach ($tagLabels as $tagLabel) {
                        $tagLabel = trim($tagLabel);
                        if (!empty($tagLabel)) {
                            $tag = new Common\Protobufs\Models\Tag();
                            $tag->setLabel($tagLabel);
                            $project->addTag($tag);
                        }
                    }
                }
                if (!empty($post['earthquake'])) {
                    $tag = new Common\Protobufs\Models\Tag();
                    $tag->setLabel('2023-turkeysyria');
                    $project->addTag($tag);
                }

                try {
                    $project = $projectDao->createProjectDirectly($project);
                    error_log('Created Project Empty: ' . $post['project_title']);
                } catch (\Exception $e) {
                    $project = null;
                }
                if (empty($project) || $project->getId() <= 0) {
                    UserRouteHandler::flashNow('error', Lib\Localisation::getTranslation('project_create_title_conflict'));
                } else {
                    $project_id = $project->getId();
                    mkdir(Common\Lib\Settings::get("files.upload_path") . "proj-$project_id/", 0755);

                    $projectDao->set_memsource_project($project_id, $project_id, $project_id, $user_id, $user_id, ['', '', '', '', '', '', '', '', '', '', '', '']);
                    $memsource_project = $projectDao->get_memsource_project($project_id);

                    $image_failed = false;
                    if (!empty($_FILES['projectImageFile']['name'])) {
                        $projectImageFileName = $_FILES['projectImageFile']['name'];
                        $extensionStartIndex = strrpos($projectImageFileName, '.');
                        // Check that file has an extension
                        if ($extensionStartIndex > 0) {
                            $extension = substr($projectImageFileName, $extensionStartIndex + 1);
                            $extension = strtolower($extension);
                            $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . $extension;

                            // Check that the file extension is valid for an image
                            if (!in_array($extension, explode(",", Common\Lib\Settings::get('projectImages.supported_formats')))) {
                                $image_failed = true;
                            }
                        } else {
                            // File has no extension
                            $image_failed = true;
                        }

                        if ($image_failed || !empty($_FILES['projectImageFile']['error']) || empty($_FILES['projectImageFile']['tmp_name'])
                                ||(($data = file_get_contents($_FILES['projectImageFile']['tmp_name'])) === false)) {
                            $image_failed = true;
                        } else {
                            $imageMaxWidth  = Common\Lib\Settings::get('projectImages.max_width');
                            $imageMaxHeight = Common\Lib\Settings::get('projectImages.max_height');
                            list($width, $height) = getimagesize($_FILES['projectImageFile']['tmp_name']);

                            if (empty($width) || empty($height) || (($width <= $imageMaxWidth) && ($height <= $imageMaxHeight))) {
                                try {
                                    $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                    $success = true;
                                } catch (\Exception $e) {
                                    $success = false;
                                }
                            } else { // Resize the image
                                $ratio = min($imageMaxWidth / $width, $imageMaxHeight / $height);
                                $newWidth  = floor($width * $ratio);
                                $newHeight = floor($height * $ratio);

                                $img = '';
                                if ($extension == 'gif') {
                                    $img = imagecreatefromgif($_FILES['projectImageFile']['tmp_name']);
                                    $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                } elseif ($extension == 'png') {
                                    $img = imagecreatefrompng($_FILES['projectImageFile']['tmp_name']);
                                    $projectImageFileName = substr($projectImageFileName, 0, $extensionStartIndex + 1) . 'jpg';
                                } else {
                                    $img = imagecreatefromjpeg($_FILES['projectImageFile']['tmp_name']);
                                }

                                $tci = imagecreatetruecolor($newWidth, $newHeight);
                                if (!empty($img) && $tci !== false) {
                                    if (imagecopyresampled($tci, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
                                        imagejpeg($tci, $_FILES['projectImageFile']['tmp_name'], 100); // Overwrite
                                        // If we did not get this far, give up and use the un-resized image
                                    }
                                }

                                $data = file_get_contents($_FILES['projectImageFile']['tmp_name']);
                                if ($data !== false) {
                                    try {
                                        $projectDao->saveProjectImageFile($project, $user_id, $projectImageFileName, $data);
                                        $success = true;
                                    } catch (\Exception $e) {
                                        $success = false;
                                    }
                                } else {
                                    $success = false;
                                }
                            }
                            if (!$success) {
                                $image_failed = true;
                            }
                        }
                    } else { // If no image uploaded, copy an old one
                        if ($old_project_id = $projectDao->get_project_id_for_latest_org_image($org_id)) {
                            $image_files = glob(Common\Lib\Settings::get('files.upload_path') . "proj-$old_project_id/image/image.*");
                            if (!empty($image_files)) {
                                $image_file = $image_files[0];
                                $destination = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/image";
                                mkdir($destination, 0755);
                                $ext = pathinfo($image_file, PATHINFO_EXTENSION);
                                copy($image_file, "$destination/image.$ext");
                                $projectDao->set_uploaded_approved($project_id);
                            }
                        }
                    }
                    if ($image_failed) {
                        UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_image'), htmlspecialchars($_FILES['projectImageFile']['name'], ENT_COMPAT, 'UTF-8')));
                        try {
                            $projectDao->deleteProject($project_id);
                        } catch (\Exception $e) {
                        }
                    } else {
                        // Continue here whether there is, or is not, an image file uploaded as long as there was not an explicit failure
                        try {
                            $restrict_translate_tasks = !empty($post['restrict_translate_tasks']);
                            $restrict_revise_tasks    = !empty($post['restrict_revise_tasks']);
                            if ($restrict_translate_tasks || $restrict_revise_tasks) $taskDao->insert_project_restrictions($project_id, $restrict_translate_tasks, $restrict_revise_tasks);

                            // Create a topic in the Community forum (Discourse)
                            error_log("projectCreate create_discourse_topic($project_id ...)");
                            try {
                               $this->create_discourse_topic($project_id, '', ['owner_uid' => $user_id], !empty($post['earthquake']));
                            } catch (\Exception $e) {
                                error_log('projectCreate create_discourse_topic Exception: ' . $e->getMessage());
                            }
                            try {
                                UserRouteHandler::flash('success', 'Project Created');
                                return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', array('project_id' => $project_id)));
                            } catch (\Exception $e) { // redirect throws \Slim\Exception\Stop
                            }
                        } catch (\Exception $e) {
                            UserRouteHandler::flashNow('error', sprintf(Lib\Localisation::getTranslation('project_create_failed_upload_file'), Lib\Localisation::getTranslation('common_project'), htmlspecialchars($_FILES['projectFile']['name'], ENT_COMPAT, 'UTF-8')));
                            try {
                                error_log('projectCreate deleteProject(' . $project_id . ")");
                                $projectDao->deleteProject($project_id);
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            }
        }

        $month_list = array(
            1 => Lib\Localisation::getTranslation('common_january'),
            2 => Lib\Localisation::getTranslation('common_february'),
            3 => Lib\Localisation::getTranslation('common_march'),
            4 => Lib\Localisation::getTranslation('common_april'),
            5 => Lib\Localisation::getTranslation('common_may'),
            6 => Lib\Localisation::getTranslation('common_june'),
            7 => Lib\Localisation::getTranslation('common_july'),
            8 => Lib\Localisation::getTranslation('common_august'),
            9 => Lib\Localisation::getTranslation('common_september'),
            10 => Lib\Localisation::getTranslation('common_october'),
            11 => Lib\Localisation::getTranslation('common_november'),
            12 => Lib\Localisation::getTranslation('common_december'),
        );

        $year_list = array();
        $yeari = (int)date('Y');
        for ($i = 0; $i < 10; $i++) {
            $year_list[$yeari] = $yeari;
            $yeari++;
        }
        $hour_list = array();
        for ($i = 0; $i < 24; $i++) {
            $hour_list[$i] = $i;
        }
        $minute_list = array();
        $minutei = (int)date('Y');
        for ($i = 0; $i < 60; $i++) {
            $minute_list[$i] = $i;
        }
        $week = strtotime('+1 week');
        $selected_year   = (int)date('Y', $week);
        $selected_month  = (int)date('n', $week);
        $selected_day    = (int)date('j', $week);
        $selected_hour   = (int)date('G', $week); // These are UTC, they will be recalculated to local time by JavaScript (we do not know what the local time zone is)
        $selected_minute = 0;
        $deadline_timestamp = gmmktime($selected_hour, $selected_minute, 0, $selected_month, $selected_day, $selected_year);

        $extraScripts  = "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/Parameters.js\"></script>";
        $extraScripts .= "<script type=\"text/javascript\" src=\"{$app->getRouteCollector()->getRouteParser()->urlFor("home")}ui/js/project_create_empty.js\"></script>";

        $template_data = array_merge($template_data, [
            "siteLocation"          => Common\Lib\Settings::get('site.location'),
            "siteAPI"               => Common\Lib\Settings::get('site.api'),
            "imageMaxFileSize"      => Common\Lib\Settings::get('projectImages.max_image_size'),
            "supportedImageFormats" => Common\Lib\Settings::get('projectImages.supported_formats'),
            "org_id"         => $org_id,
            "user_id"        => $user_id,
            "extra_scripts"  => $extraScripts,
            'deadline_timestamp' => $deadline_timestamp,
            'selected_day'   => $selected_day,
            'month_list'     => $month_list,
            'selected_month' => $selected_month,
            'year_list'      => $year_list,
            'selected_year'  => $selected_year,
            'hour_list'      => $hour_list,
            'selected_hour'  => $selected_hour,
            'minute_list'    => $minute_list,
            'selected_minute'=> $selected_minute,
            'languages'      => $projectDao->generate_language_selection(1),
            'showRestrictTask' => $taskDao->organisationHasQualifiedBadge($org_id),
            'roles'          => $adminDao->get_roles($user_id, $org_id),
            'sesskey'        => $sesskey,
        ]);
        return UserRouteHandler::render('project/project.create.empty.tpl', $response);
    }

    public function project_add_shell_tasks(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $project_id = $args['project_id'];

        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

        $project = $projectDao->getProject($project_id);
        $memsource_project = $projectDao->get_memsource_project($project_id);

        $sesskey = Common\Lib\UserSession::getCSRFKey();

        if ($post = $request->getParsedBody()) {
            if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($post, 'project_add_shell_tasks')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);
            $selections = $projectDao->generate_language_selection();
            $number = 0;
            for ($count = 0; $count < 20; $count++) {
                if (!empty($post["task_type_$count"])) {
                    $task_type = (int)$post["task_type_$count"];
                    $quantity = (int)$post["quantity_$count"];
                    $source_quantity = (int)$post["source_quantity_$count"];
                    $target_language = $post["target_language_$count"];
                    if (empty(Common\Enums\TaskTypeEnum::$enum_to_UI[$task_type]) || $quantity <= 1 || $source_quantity < 1 || empty($selections[$target_language])) continue;

                    list($target_language, $target_country) = $projectDao->convert_selection_to_language_country($target_language);

                    $task = new Common\Protobufs\Models\Task();
                    $task->setProjectId($project_id);
                    if (empty($post["title_$count"])) $task->setTitle($project->getTitle());
                    else                              $task->setTitle($post["title_$count"]);
                    $projectSourceLocale = $project->getSourceLocale();

                    $taskSourceLocale = new Common\Protobufs\Models\Locale();
                    $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
                    $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
                    $task->setSourceLocale($taskSourceLocale);

                    $task->setTaskStatus(Common\Enums\TaskStatusEnum::PENDING_CLAIM);

                    $taskTargetLocale = new Common\Protobufs\Models\Locale();
                    $taskTargetLocale->setLanguageCode($target_language);
                    $taskTargetLocale->setCountryCode($target_country);
                    $task->setTargetLocale($taskTargetLocale);

                    $task->setTaskType($task_type);
                    $task->setWordCount($quantity);
                    $task->set_word_count_partner_weighted($quantity);
                    $task->set_word_count_original($quantity);
                    $task->set_source_quantity($source_quantity);

                    $task->setDeadline($project->getDeadline());
                    $task->setPublished(0);

                    $projectDao->queue_asana_project($project_id);

                    $task_id = $taskDao->createTaskDirectly($task);
                    if (!$task_id) {
                        error_log('Failed to add Shell Task');
                        continue;
                    }
                    error_log("Added Shell Task: $task_id");

                    if (!empty($post["comment_$count"])) {
                        $url = $post["comment_$count"];
                        if (!preg_match('#^(http|https)://#i', $url)) $url = "https://$url";
                        $taskDao->insert_task_url($task_id, $url);
                    }
                    $projectDao->set_memsource_task($task_id, 0, $task_id, '', 0, 0, 0, 0, 0);
                    $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::PENDING_CLAIM);
                    $number++;
                }
            }
            UserRouteHandler::flash('success', "$number Shell Tasks Added");
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('project-view', ['project_id' => $project_id]));
        }
        $template_data = array_merge($template_data, [
            'siteLocation'   => Common\Lib\Settings::get('site.location'),
            'project_id'     => $project_id,
            'project'        => $project,
            'languages'      => $projectDao->generate_language_selection(),
            'sesskey'        => $sesskey,
        ]);
        return UserRouteHandler::render('project/project.add.shell.tasks.tpl', $response);
    }

    /**
     * Generate and return a random string of the specified length.
     *
     * @param int $length The length of the string to be created.
     * @return string
     */
    private function random_string($length=15) {
        $pool  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool .= 'abcdefghijklmnopqrstuvwxyz';
        $pool .= '0123456789';
        $poollen = strlen($pool);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($pool, (mt_rand()%($poollen)), 1);
        }
        return $string;
    }

    public function projectCreated(Request $request, Response $response, $args)
    {
        global $app, $template_data;
        $project_id = $args['project_id'];

        $projectDao = new DAO\ProjectDao();
        $project = $projectDao->getProject($project_id);
        $org_id = $project->getOrganisationId();

        $template_data = array_merge($template_data, array(
                "org_id" => $org_id,
                "project_id" => $project_id
        ));

        return UserRouteHandler::render("project/project.created.tpl", $response);
    }

    public function archiveProject(Request $request, Response $response, $args)
    {
        global $app;
        $project_id = $args['project_id'];
        $sesskey    = $args['sesskey'];

        $projectDao = new DAO\ProjectDao();

        if ($fail_CSRF = Common\Lib\UserSession::checkCSRFKey($sesskey, 'archiveProject')) return $response->withStatus(302)->withHeader('Location', $fail_CSRF);

        $project = $projectDao->getProject($project_id);
        $user_id = Common\Lib\UserSession::getCurrentUserID();
        $archivedProject = $projectDao->archiveProject($project_id, $user_id);

        if ($archivedProject) {
            UserRouteHandler::flash(
                "success",
                sprintf(Lib\Localisation::getTranslation('org_dashboard_9'), $project->getTitle())
            );
        } else {
            UserRouteHandler::flash(
                "error",
                sprintf(Lib\Localisation::getTranslation('org_dashboard_10'), $project->getTitle())
            );
        }

        return $response->withStatus(302)->withHeader('Location', $request->getHeaderLine('REFERER'));
    }

    public function downloadProjectFile(Request $request, Response $response, $args)
    {
        global $app;
        $projectId = $args['project_id'];

        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();

        if ($taskDao->isUserRestrictedFromProject($projectId, Common\Lib\UserSession::getCurrentUserID())) {
            UserRouteHandler::flash('error', 'You cannot access this project!');
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }

        $project_tasks = $projectDao->get_tasks_for_project($projectId); // Is a memsource project if any memsource jobs
        try {
            if ($project_tasks) {
                $task_id = 0;
                $unitary = 1;
                foreach ($project_tasks as $project_task) {
                    $title = substr($project_task['title'], strlen($project_task['internalId']) + 1);
                    if (!$task_id) { // First time through
                        $task_id = $project_task['id'];
                        $title_0 = $title;
                    }
                    if ($title_0 !== $title) $unitary = 0; // Not a unitary project with a single source file (1 => probably it is)
                }
                if (!$unitary) {
                    UserRouteHandler::flash('error', 'We could not find a matching file.');
                    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
                }
                $headArr = $taskDao->downloadTaskVersion($task_id, 0); // Download an original Task file as "Project" file
            } else {
            $headArr = $projectDao->downloadProjectFile($projectId);
            }
            //Convert header data to array and set headers appropriately
            if (!empty($headArr)) {
                $headArr = unserialize($headArr);
                foreach ($headArr as $key => $val) {
                    if (!empty($val)) $response = $response->withHeader($key, $val);
                }
            }
            return $response;
        } catch (Common\Exceptions\SolasMatchException $e) {
            UserRouteHandler::flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_original_project_file'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
    }

    public function downloadProjectImageFile(Request $request, Response $response, $args)
    {
        global $app;
        $projectId = $args['project_id'];

        $projectDao = new DAO\ProjectDao();

        try {
            $headArr = $projectDao->downloadProjectImageFile($projectId);
            //Convert header data to array and set headers appropriately
            if (!empty($headArr)) {
                $headArr = unserialize($headArr);
                foreach ($headArr as $key => $val) {
                    if (!empty($val)) $response = $response->withHeader($key, $val);
                }
            }
            return $response;
        } catch (Common\Exceptions\SolasMatchException $e) {
            UserRouteHandler::flash(
                "error",
                sprintf(
                    Lib\Localisation::getTranslation('common_error_file_not_found'),
                    Lib\Localisation::getTranslation('common_project_image_file'),
                    Common\Lib\Settings::get("site.system_email_address")
                )
            );
            return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
        }
    }


    public function  follow_discource_project($project_id, $userId){

        global $app;
        $projectDao = new DAO\ProjectDao();        
        $userDao = new DAO\UserDao();
        $user = $userDao->getUser($userId);
        $email = $user->email;

        $topicIdFromDB = $projectDao->get_discourse_id($project_id) ;

        // Discourse domain
        $discourseDomain = 'https://community.translatorswb.org';
        // Keys
        $apiKey = Common\Lib\Settings::get('discourse.api_key');
        $userName =  Common\Lib\Settings::get('discourse.api_username');
        
        //hardcdode topicId
        $topicId = 66964 ;

        // Create the API endpoint URL
        $apiUrl = "$discourseDomain/t/$topicId/notifications.json";
        
        error_log("url : $apiUrl");
        error_log("topicFromDB : $topicIdFromDB");
       
        $data = array(
            'notification_level' => "3"
        );
        
        $options = array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                'Api-Key: ' . $apiKey,
                'Api-Username: ' . $userName,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
        } else {
            error_log($response);
        }
        
        curl_close($ch);

    }

    public function  unfollow_discource_project($project_id, $userId){

        global $app;
        $projectDao = new DAO\ProjectDao();        
        $userDao = new DAO\UserDao();
        $user = $userDao->getUser($userId);
        $email = $user->email;
        $topicIdFromDB = $projectDao->get_discourse_id($project_id) ;

        // Discourse domain
        $discourseDomain = 'https://community.translatorswb.org';
        // Keys
        $apiKey = Common\Lib\Settings::get('discourse.api_key');
        $userName =  Common\Lib\Settings::get('discourse.api_username');
        
        //hardcdode topicId
        // $topicId = 66964 ;

        // Create the API endpoint URL
        $apiUrl = "$discourseDomain/t/$topicIdFromDB/notifications.json";

        $data = array(
            'notification_level' => "0"
        );
        
        $options = array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                'Api-Key: ' . $apiKey,
                'Api-Username: ' . $userName,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        );        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
        } else {
            error_log($response);
        }
        curl_close($ch);
    }

    public function remove_user_from_task($project_id, $user_id){

        global $app;
        $projectDao = new DAO\ProjectDao();        
        $userDao = new DAO\UserDao();
        $user = $userDao->getUser($user_id);
        $email = $user->email;

        $usersApiUrl = "https://app.asana.com/api/1.0/users?opt_fields=email";
    
        $task_ids = $projectDao->get_asana_tasks($project_id);
        $ch = curl_init();
        $token = Common\Lib\Settings::get('asana.api_key6');

        curl_setopt($ch, CURLOPT_URL, $usersApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $token, 'Content-Type: application/json' ]); 
   
        $response = curl_exec($ch); 
        if (curl_errno($ch)) { die('Error:' . curl_error($ch)); } 
        $responseData = json_decode($response, true);
        
        curl_close($ch);
        
        $userGid = null; 

        if (isset($responseData['data']) && !empty($task_ids)) { 
            foreach ($responseData['data'] as $user) { if (isset($user['email']) && $user['email'] === $email) { 
                
                $userGid = $user['gid'];
                error_log(" userGid : $userGid") ;               
                break; } } 
                
                if ($userGid === null) { error_log("User with email with : $email not found!"); } 
           
                foreach ($task_ids as $taskId) { 

                    $asanaTask = $taskId["asana_task_id"] ;
                      // Asana API endpoint to assign the task   
                    $tasksApiUrl = "https://app.asana.com/api/1.0/tasks/$asanaTask/$userGid"; 
                   
                    error_log("Asana Task ID : $asanaTask");

                    $data = [ 'assignee' => null ];
                     $ch = curl_init(); 
        
                     curl_setopt($ch, CURLOPT_URL, $tasksApiUrl); 
                     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
                     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
                     curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $token, 'Content-Type: application/json' ]); 
                     $response = curl_exec($ch); 

                     if (curl_errno($ch)) { echo 'Error re-assigning task ' . $taskId . ': ' . curl_error($ch) . '<br>'; } 
                     else { 
                      
                        $responseData = json_decode($response, true); 
                        error_log(print_r($taskId,true) ); } 
                        curl_close($ch); } } else { error_log("no users or task found !"); }  

    }

    public function assign_user_to_task($project_id, $user_id)
    {
       
        global $app;
        $projectDao = new DAO\ProjectDao();        
        $userDao = new DAO\UserDao();
        $user = $userDao->getUser($user_id);
          
        // Can be used for testing
        $projectId = 9446 ;   
        $email = $user->email;
        $usersApiUrl = "https://app.asana.com/api/1.0/users?opt_fields=email";
        $task_ids = $projectDao->get_asana_tasks($projectId);


        // Section for getting the  user gid
               
        $userGid = null; 
        $ch = curl_init();
        $token = Common\Lib\Settings::get('asana.api_key6');

        curl_setopt($ch, CURLOPT_URL, $usersApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $token, 'Content-Type: application/json' ]);    
        $response = curl_exec($ch); 
        if (curl_errno($ch)) { die('Error:' . curl_error($ch)); } 
        curl_close($ch);
        $responseData = json_decode($response, true); 


        if (isset($responseData['data']) && !empty($task_ids)) { 
            foreach ($responseData['data'] as $user) { if (isset($user['email']) && $user['email'] === $email) {                 
                $userGid = $user['gid'];
                error_log(" userGid : $userGid") ;               
                break; } } 
                
                if ($userGid === null) { error_log("User with email with : $email not found!"); } 
           
                foreach ($task_ids as $taskId) { 
                    $asanaTask = $taskId["asana_task_id"] ;

                    
                    // Asana API endpoint to assign the task   
                    $tasksApiUrl = 'https://app.asana.com/api/1.0/tasks/' . $asanaTask; 
                    // Asana Api endpoint to get task subtask
                    $taskSubtask = "https://app.asana.com/api/1.0/tasks/$asanaTask/subtasks"; 
                   
                    //Action: check if the task is is complete 


                    $data =['data' => [ 'assignee' => $userGid ]];

                    // first get the task to check if it uncomplete

                    $taskch = curl_init();

                    curl_setopt($taskch, CURLOPT_URL, $tasksApiUrl); 
                    curl_setopt($taskch, CURLOPT_RETURNTRANSFER, true); 
                    curl_setopt($taskch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $token, 'Content-Type: application/json' ]); 
                    $taskStatusResponse = curl_exec($taskch); 
                    $taskData = json_decode($taskStatusResponse, true);
                    error_log("below status is complete or ");
                    error_log($taskData['data']['completed']); 

                    curl_close($taskch);


                    
                    // Section for assign a task to  the suer 
                    $ch = curl_init(); 
        
                    curl_setopt($ch, CURLOPT_URL, $tasksApiUrl); 
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $token, 'Content-Type: application/json' ]); 
                    $response = curl_exec($ch); 


                    if (curl_errno($ch)) { echo 'Error assigning task ' . $taskId . ': ' . curl_error($ch) . '<br>'; } 
                     else { 
                        $responseData = json_decode($response, true); 
                        
                         } 
                       
                    curl_close($ch);
                    

                    // Call to get all subtask 

                    $ch1 = curl_init(); 
        
                    curl_setopt($ch1, CURLOPT_URL, $taskSubtask); 
                    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); 
                    curl_setopt($ch1, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $token, 'Content-Type: application/json' ]); 
                    
                    $response_sub = curl_exec($ch1); 
                    $res = var_export($response_sub, true);
                    
                 

                    if (curl_errno($ch1)) { echo "subtask not retrived "; } else{
                    

                    // array with subtask
                    $responseDataSub = json_decode($response_sub, true); 

                    if(isset($responseDataSub['data'])){
                        $ch2 = curl_init(); 
                        foreach($responseDataSub['data'] as $subtask){

                           if($subtask['complete']){


                            $subGid  = $subtask['gid'] ;
                            error_log("subtask gid is $subGid");
                            $taskSubUrl = 'https://app.asana.com/api/1.0/tasks/' . $subGid;

                            $dataSub = ['data'=>[
                                'assignee' =>  $userGid
                            ]];


                            curl_setopt($ch2, CURLOPT_URL, $taskSubUrl); 
                            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); 
                            curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'PUT'); 
                            curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($dataSub)); 
                            curl_setopt($ch2, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $token, 'Content-Type: application/json' ]); 
                            $responseSub = curl_exec($ch2); 

                           } 

                            if (curl_errno($ch2)) { echo 'Error assigning subtask ' . $taskId . ': ' . curl_error($ch) . '<br>'; } 
                            else { 
                             
                               $responseData = json_decode($response, true); 
                               
                                } 
                              
                           curl_close($ch2);

                        }
                    }

                    error_log(print_r($responseDataSub,true));
                    }
                    curl_close($ch1);
                    
                    } } 
                    
                    else { error_log("no users or task found !"); }

                    // Call subtasks
                                   
                     
        }
                        


    

    public function create_discourse_topic($projectId, $targetlanguages, $memsource_project = 0, $earthquake = 0)
    {
        global $app;
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $project = $projectDao->getProject($projectId);
        $org_id = $project->getOrganisationId();
        $orgDao = new DAO\OrganisationDao();
        $org = $orgDao->getOrganisation($org_id);
        $org_name = $org->getName();

        $selections = $projectDao->generate_language_selection();
        $langcodearray = explode(',', $targetlanguages);
        $languages = array();
        foreach($langcodearray as $langcode){
            if (!empty($langcode) && !empty($selections[$langcode])) {
                $languages[] = $selections[$langcode];
            }
        }

        $creator = $taskDao->get_creator($projectId, $memsource_project);
        $pm = $creator['email'];
        if (strpos($pm, '@translatorswithoutborders.org') === false && strpos($pm, '@clearglobal.org') === false) $pm = 'projects@translatorswithoutborders.org';

        $title = str_replace(array('\r\n', '\n', '\r', '\t'), ' ', $project->getTitle() . " $projectId"); // Discourse does not like duplicates
        $discourseapiparams = array(
            'category' => '7',
            'title' => $title,
            'raw' => "Partner: $org_name. Project Manager: $pm URL: /"."/".$_SERVER['SERVER_NAME']."/project/$projectId/view ".str_replace(array('\r\n', '\n', '\r', '\t'), ' ', strip_tags($project->getDescription())),
        );
        $fields = '';
        foreach($discourseapiparams as $name => $value){
            $fields .= urlencode($name).'='.urlencode($value).'&';
        }
        $fields .= 'tags[]=' . urlencode($org_name);
        $language_count = 0;
        foreach($languages as $language){
            // We cannot pass the post fields as array because multiple languages mean duplicate tags[] keys
            $fields .= '&tags[]=' . urlencode($language);
            if (++$language_count == 4) break; // Limit in Discourse on number of tags?
        }
error_log("fields: $fields targetlanguages: $targetlanguages");//(**)

        $re = curl_init(Common\Lib\Settings::get('discourse.url').'/posts');
        curl_setopt($re, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($re, CURLOPT_HTTPHEADER, ['Api-Key: ' . Common\Lib\Settings::get('discourse.api_key'), 'Api-Username: ' . Common\Lib\Settings::get('discourse.api_username')]);

        $res = curl_exec($re);
        if ($error_number = curl_errno($re)) {
          error_log("Discourse API error ($error_number): " . curl_error($re));
        } else {
            $response_data = json_decode($res, true);
            if (!empty($response_data['topic_id'])) {
                $topic_id = $response_data['topic_id'];
                $projectDao->set_discourse_id($projectId, $topic_id);
            } else {
                error_log('Discourse API error: No topic_id returned');
                error_log($res);
            }
        }
        curl_close($re);
    }

    public function project_cron_1_minute(Request $request)
    {
      $fp_for_lock = fopen(__DIR__ . '/project_cron_1_minute_lock.txt', 'r');
      if (flock($fp_for_lock, LOCK_EX | LOCK_NB)) { // Acquire an exclusive lock, if possible, if not we will wait for next time
        $projectDao = new DAO\ProjectDao();
        $possible_completes = $projectDao->get_possible_completes();
        foreach ($possible_completes as $possible_complete) {
            $translations_not_all_complete = $projectDao->identify_claimed_but_not_yet_in_progress($possible_complete['project_id']);
            foreach ($translations_not_all_complete as $task_id => $task_item) {
                $projectDao->update_tasks_status_conditionally($task_id, $task_item ? Common\Enums\TaskStatusEnum::CLAIMED : Common\Enums\TaskStatusEnum::IN_PROGRESS);
            }
        }
        flock($fp_for_lock, LOCK_UN); // Release the lock
      }
      fclose($fp_for_lock);

      if (strpos((string)$request->getUri(), '/getwordcount')) return;
      die;
    }

    public function task_cron_1_minute()
    {
        $projectDao = new DAO\ProjectDao();
        $taskDao = new DAO\TaskDao();
        $orgDao = new DAO\OrganisationDao();
        $memsourceApiToken = Common\Lib\Settings::get('memsource.memsource_api_token');

        $fp_for_lock = fopen(__DIR__ . '/task_cron_1_minute_lock.txt', 'r');
        if (flock($fp_for_lock, LOCK_EX | LOCK_NB)) { // Acquire an exclusive lock, if possible, if not we will wait for next time
            $queue_copy_task_original_files = $projectDao->get_queue_copy_task_original_files();
            $count = 0;
            foreach ($queue_copy_task_original_files as $queue_copy_task_original_file) {
                if (++$count > 4) break; // Limit number done at one time, just in case

                $project_id         = $queue_copy_task_original_file['project_id'];
                $task_id            = $queue_copy_task_original_file['task_id'];
                $memsource_task_uid = $queue_copy_task_original_file['memsource_task_uid'];
                $filename           = $queue_copy_task_original_file['filename'];

                $memsource_project = $projectDao->get_memsource_project($project_id);
                $memsource_project_uid = $memsource_project['memsource_project_uid'];
                $owner_uid             = $memsource_project['owner_uid'];
                $user_id = $projectDao->get_user_id_from_memsource_user($owner_uid);
                if (!$user_id) $user_id = 62927; // translators@translatorswithoutborders.org
//(**)dev server                if (!$user_id) $user_id = 3297;

                $memsourceApiV1 = Common\Lib\Settings::get("memsource.api_url_v1");                    
                $url = "{$memsourceApiV1}projects/$memsource_project_uid/jobs/$memsource_task_uid/original";

                $re = curl_init($url);
                $httpHeaders = ["Authorization: Bearer $memsourceApiToken"];
                curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);
                curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($re, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock

                $res = curl_exec($re);
                if ($error_number = curl_errno($re)) {
                    error_log("task_cron ($task_id) Curl error ($error_number): " . curl_error($re)); 
                }

                $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                curl_close($re);

                if ($responseCode == 200) {
                    if (strlen($res) <= 100000000) {
                        $projectDao->save_task_file($user_id, $project_id, $task_id, $filename, $res);
                    } else {
                        error_log('File too big: ' . strlen($res));
                    }
                    error_log("dequeue_copy_task_original_file() task_id: $task_id Removing");
                    $projectDao->dequeue_copy_task_original_file($task_id);
                } else {
                    if (in_array($responseCode,[400,401,403,404,405,410,415,501,202])) {
                        $projectDao->dequeue_copy_task_original_file($task_id);
                    }
                    error_log("task_cron ERROR ($task_id) responseCode: $responseCode");
                }

                // Patch the KP Project URL into Memsource Project PO (this will happen many times)
                $ch = curl_init("https://cloud.memsource.com/web/api2/v1/projects/$memsource_project_uid");
                $data = [
                    'purchaseOrder' => "https://twbplatform.org/project/$project_id/view",
                ];
                $payload = json_encode($data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $memsourceApiToken"]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $result = curl_exec($ch);
                curl_close($ch);
            }

            if (($task_id = $projectDao->get_task_analysis_trigger()) && ($memsource_task = $projectDao->get_memsource_task($task_id)) && !preg_match('/^\d*$/', $memsource_task_uid = $memsource_task['memsource_task_uid'])) {
                $task = $taskDao->getTask($task_id);
                if ($task->getTaskStatus() == Common\Enums\TaskStatusEnum::COMPLETE) {
                    list($translation_level, $revision_level) = $this->get_workflow_levels($task->getProjectId());
                    $this_level = $memsource_task['workflowLevel'];
                    $this->insert_analysis_request($task_id, ['jobs' => [['uid' => $memsource_task_uid]], 'type' => 'PostAnalyse', 'transMemoryPostEditing' => true, 'nonTranslatablePostEditing' => true, 'machineTranslatePostEditing' => true, 'name' => $task->getTitle()]);

                    if ($task->getTaskType() == Common\Enums\TaskTypeEnum::PROOFREADING) {
                        if ($translation_level) {
                            $this->insert_analysis_request($task_id, ['jobs' => [['uid' => $memsource_task_uid]], 'type' => 'Compare', 'compareWorkflowLevel' => $translation_level, 'name' => "Compare R{$memsource_task['internalId']} to T"]);

                            // Find all Translation(s) which overlap with this Revision
                            $top_level = $projectDao->get_top_level($memsource_task['internalId']);
                            $project_tasks = $projectDao->get_tasks_for_project($task->getProjectId());
                            foreach ($project_tasks as $project_task) {
                                if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                                    if ($project_task['workflowLevel'] == $translation_level) {
                                        if (($memsource_task['beginIndex'] <= $project_task['endIndex']) && ($project_task['beginIndex'] <= $memsource_task['endIndex'])) { // Overlap
                                            if (($memsource_task['beginIndex'] != $project_task['beginIndex']) || ($memsource_task['endIndex'] != $project_task['endIndex'])) // Not identical
                                                $this->insert_analysis_request($project_task['task_id'], ['jobs' => [['uid' => $project_task['memsource_task_uid']]], 'type' => 'Compare', 'compareWorkflowLevel' => $memsource_task['workflowLevel'], 'name' => "Compare T{$project_task['internalId']} to R"]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $count = 0;
            while ($task_id = $projectDao->get_task_resource_info_trigger()) {
                error_log("get_task_resource_info_trigger: $task_id, $count");
                $task = $taskDao->getTask($task_id);
                $task_id = $task->getId();
                $memsource_task = $projectDao->get_memsource_task($task_id);
                $memsource_project = $projectDao->get_memsource_project($task->getProjectId());
                $memsource_task_uid = $memsource_task['memsource_task_uid'];
                $memsource_project_uid = $memsource_project['memsource_project_uid'];

                $url = "https://cloud.memsource.com/web/api2/v1/projects/$memsource_project_uid/jobs/$memsource_task_uid/translationResources";
                $ch = curl_init("https://cloud.memsource.com/web/api2/v1/projects/$memsource_project_uid/jobs/$memsource_task_uid/translationResources");
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $memsourceApiToken"]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock
                $result = curl_exec($ch);
                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($responseCode == 200) {
                    $resultset = json_decode($result, true);
                    $MT_name = '';
                    if (!empty($resultset['machineTranslateSettings'])) $MT_name = !empty($resultset['machineTranslateSettings']['name']) ? mb_substr($resultset['machineTranslateSettings']['name'], 0, 128) : '';

                        $task_resource_TBs = [];
                        if (empty($resultset['termBases'])) $resultset['termBases'] = [];
                        foreach ($resultset['termBases'] as $item) {
                            list($language_code, $country_code) = $projectDao->convert_memsource_to_language_country($item['targetLang']);
                            $task_resource_TBs[] = [
                                'name'       => !empty($item['name']) ? mb_substr($item['name'], 0, 128) : '',
                                'readMode'   => !empty($item['readMode']) ? $item['readMode'] : 0,
                                'writeMode'  => !empty($item['writeMode']) ? $item['writeMode'] : 0,
                                'targetLang' => "{$language_code}-{$country_code}"
                            ];
                        }
                        $task_resource_TMs = [];
                        if (empty($resultset['translationMemories'])) $resultset['translationMemories'] = [];
                        foreach ($resultset['translationMemories'] as $item) {
                            list($language_code, $country_code) = $projectDao->convert_memsource_to_language_country($item['targetLang']);
                            $task_resource_TMs[] = [
                                'name'       => !empty($item['name']) ? mb_substr($item['name'], 0, 128) : '',
                                'readMode'   => !empty($item['readMode']) ? $item['readMode'] : 0,
                                'writeMode'  => !empty($item['writeMode']) ? $item['writeMode'] : 0,
                                'penalty'    => $item['penalty'],
                                'targetLang' => "{$language_code}-{$country_code}"
                            ];
                        }
                        $projectDao->update_task_resource_info($task_id, $MT_name, $task_resource_TBs, $task_resource_TMs, $url);
                } else error_log("$task_id $url responseCode: $responseCode $result");
                if (++$count >= 20) break;
            }

            $queue_asana_projects = $projectDao->get_queue_asana_projects();
            $count = 0;
            foreach ($queue_asana_projects as $queue_asana_project) {
                if (++$count > 4) break; // Limit number done at one time, just in case
                $projectId = $queue_asana_project['project_id'];
                if ($projectId < 28433) { // Before cutover
                    $projectDao->dequeue_asana_project($projectId);
                    break;
                }
error_log("get_queue_asana_projects: $projectId");//(**)
                $project = $projectDao->getProject($projectId);
                $org_id = $project->getOrganisationId();
                $org = $orgDao->getOrganisation($org_id);
                $org_name = $org->getName();
                $project_name = $project->getTitle();
                $project_url = 'https://' . $_SERVER['SERVER_NAME'] . "/project/$projectId/view/";
                $objDateTime = new \DateTime($project->getDeadline());
                $sourceLocale_code = $project->getSourceLocale()->getLanguageCode() .  '-'  . $project->getSourceLocale()->getCountryCode();
                $sourceLocale      = $project->getSourceLocale()->getLanguageName() . ' - ' . $project->getSourceLocale()->getCountryName();
                $selections = $projectDao->generate_language_selection();
                $source_name_asana = $sourceLocale;
                if (!empty($selections[$sourceLocale_code])) $source_name_asana = $selections[$sourceLocale_code];
                $source_code_asana = str_replace('---', '', $sourceLocale_code);

                $memsource_project = $projectDao->get_memsource_project($projectId);
                $creator = $taskDao->get_creator($projectId, $memsource_project);
                $pm = $creator['email'];
                $memsource_project_id = $memsource_project['memsource_project_id'];
                $self_service = (strpos($pm, '@translatorswithoutborders.org') === false && strpos($pm, '@clearglobal.org') === false) || $projectDao->get_memsource_self_service_project($memsource_project_id);
                if ($self_service) $asana_project = '778921846018141';
                else               $asana_project = '1200067882657242';

                $tasks = $projectDao->getProjectTasksArray($projectId);
                $asana_task_splits = [];
                foreach ($tasks as $task) {
                    $targetLanguageCode = $task['targetLanguageCode'] .  '-'  . $task['targetCountryCode'];
                    $asana_task_split_key = "$targetLanguageCode:" . Common\Enums\TaskTypeEnum::$enum_to_UI[$task['taskType']]['type_category'];
                    $targetLanguageName = $task['targetLanguageName'] . ' - ' . $task['targetCountryName'];
                    if (empty($asana_task_splits[$asana_task_split_key])) {
                        $asana_task_splits[$asana_task_split_key] = [
                            'targetLanguageCode' => $targetLanguageCode,
                            'targetLanguageName' => $targetLanguageName,
                            'type_category'      => Common\Enums\TaskTypeEnum::$enum_to_UI[$task['taskType']]['type_category'],
                            'type_category_text' => Common\Enums\TaskTypeEnum::$enum_to_UI[$task['taskType']]['type_category_text'],
                            'quantities'         => ''];
                        foreach (Common\Enums\TaskTypeEnum::$task_type_to_enum as $to_enum) $asana_task_splits[$asana_task_split_key][$to_enum] = 0;
                    }
                    foreach (Common\Enums\TaskTypeEnum::$task_type_to_enum as $to_enum) if ($task['taskType'] == $to_enum) $asana_task_splits[$asana_task_split_key][$to_enum] += $task['wordCount'];
                    $source_quantity_text = $task['source_quantity'] . ' ' . Common\Enums\TaskTypeEnum::$enum_to_UI[$task['taskType']]['source_unit_for_later_stats'];
                    $wordCount_text       = $task['wordCount']       . ' ' . Common\Enums\TaskTypeEnum::$enum_to_UI[$task['taskType']]['pricing_and_recognition_unit_text'];
                    $full_line = Common\Enums\TaskTypeEnum::$enum_to_UI[$task['taskType']]['type_text'] . ': ' . $source_quantity_text;
                    if ($source_quantity_text != $wordCount_text) $full_line .= ', ' . $wordCount_text;
                    $full_line .= "\n";
                    $asana_task_splits[$asana_task_split_key]['quantities'] .= $full_line;
                }

                $asana_tasks = $projectDao->get_asana_tasks($projectId);
                $dequeue = true;
                foreach ($asana_task_splits as $key => $asana_task_split) {
                    if (empty($asana_tasks[$key])) {
                        $create = true;
                        $url = 'https://app.asana.com/api/1.0/tasks';
                    } else {
                        $create = false;
                        $url = 'https://app.asana.com/api/1.0/tasks/' . $asana_tasks[$key]['asana_task_id'];
                        error_log('Updating Asana task: ' . $asana_tasks[$key]['asana_task_id']);
                    }
                    $targetLocale =       $asana_task_split['targetLanguageName'];
                    $targetLocale_code =  $asana_task_split['targetLanguageCode'];
                    $type_category =      $asana_task_split['type_category'];
                    $type_category_text = $asana_task_split['type_category_text'];
                    $quantities =         $asana_task_split['quantities'];

                    $ch = curl_init($url);

                    $wordCount = 0; // Pick the first nonzero wordcount for type_enum (but use zero wordcount if not a Phrase type)...
                    foreach (Common\Enums\TaskTypeEnum::$task_type_to_enum as $to_enum) if ($wordCount == 0 && Common\Enums\TaskTypeEnum::$enum_to_UI[$to_enum]['type_category'] == 1) $wordCount = $asana_task_split[$to_enum];

                    // https://developers.asana.com/docs/create-a-task
                    // https://developers.asana.com/docs/update-a-task
                    if ($create) {
                        $target_name_asana = $targetLocale;
                        if (!empty($selections[$targetLocale_code])) $target_name_asana = $selections[$targetLocale_code];
                        $target_code_asana = str_replace('---', '', $targetLocale_code);
                        $data = array('data' => array(
                            "name" => $project_name,
                            "projects" => array(
                                $asana_project
                            ),
                            "custom_fields" => array(
                                "1200067882657247" => $wordCount,
                                "1200067882657245" => $org_name,
                                "1200068101079960" => $source_name_asana,
                                "1200269602122253" => $source_code_asana,
                                "1200067882657251" => $target_name_asana,
                                "1200269602122255" => $target_code_asana,
                                '1204581772156605' => $type_category_text,
                                '1204581656972591' => $quantities,
                                "1200226775862070" => $project_url,
                                '1202126000618445' => ($memsource_project && !preg_match('/^\d*$/', $memsource_project['memsource_project_uid'])) ? $taskDao->get_matecat_analyze_url($projectId, $memsource_project) : '',
                                "1200269602122257" => "$projectId"
                            ),
                            "due_at" => $objDateTime->format('c'),
                            'notes' => "TWB Platform details for Task area/language pair, Project ID: $projectId. See Project Dashboard: https://twbplatform.org/metabase/projectdash.php?projectid=$projectId",
                            ));
                            if (!$self_service) $data['data']['assignee'] = $pm;
                    } else {
                        $data = array('data' => array(
                            "custom_fields" => array(
                                "1200067882657247" => $wordCount,
                                "1200067882657245" => $org_name,
                                '1204581656972591' => $quantities,
                            ),
                            "due_at" => $objDateTime->format('c'),
                            ));
                        if (!$self_service) $data['data']['assignee'] = $pm;
                    }
                    $payload = json_encode($data);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    if ($create) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    else         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    $authorization = "Authorization: Bearer ". Common\Lib\Settings::get('asana.api_key6');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock
                    $result = curl_exec($ch);
                    curl_close($ch);
                    error_log("POST/PUT Asana task ($targetLocale_code, $type_category_text), result: $result");

                    $asana_task_details = json_decode($result, true);
                    if (!empty($asana_task_details['errors'][0]['message'])) {
                      if (strpos($asana_task_details['errors'][0]['message'], 'Not a user in Organization') !== false) {
                        unset($data['data']['assignee']);
                        $ch = curl_init($url);
                        $payload = json_encode($data);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                        if ($create) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                        else         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 300); // Just so it does not hang forever and block because of file lock
                        $result = curl_exec($ch);
                        curl_close($ch);
                        error_log("POST/PUT Asana task ($targetLocale_code, $type_category_text), result: $result");
                      } elseif (strpos($asana_task_details['errors'][0]['message'], 'Usually waiting and then retrying') !== false) {
                        $dequeue = false;
                      }
                    }

                    if ($create) {
                        $asana_task_details = json_decode($result, true);
                        if (!empty($asana_task_details['data']['gid'])) {
                            $asana_task_id = $asana_task_details['data']['gid'];
                            $projectDao->set_asana_task($projectId, $sourceLocale_code, $targetLocale_code, $type_category, $asana_task_id);
                        }
                    }
                }

                if ($dequeue) {
                error_log("dequeue_asana_project() project_id: $projectId Removing");
                $projectDao->dequeue_asana_project($projectId);
                }
            }

            $projectDao->delete_not_accepted_user();

            $taskDao->update_native_matching();

            flock($fp_for_lock, LOCK_UN); // Release the lock
        }
        fclose($fp_for_lock);

        die;
    }

    public function insert_analysis_request($task_id, $data)
    {
        $projectDao = new DAO\ProjectDao();
        $memsourceApiToken = Common\Lib\Settings::get('memsource.memsource_api_token');
        $ch = curl_init('https://cloud.memsource.com/web/api2/v1/analyses/byProviders');
        $payload = json_encode($data);
        error_log("analyses/byProviders ($task_id) payload: $payload");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $memsourceApiToken"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        if ($error_number = curl_errno($ch)) error_log("analyses/byProviders ($task_id) Curl error ($error_number): " . curl_error($ch));
        else {
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $resultset = json_decode($result, true);
            if (!empty($resultset['analyses'][0]['analyse']['id'])) $projectDao->insert_requested_analysis($task_id, $resultset['analyses'][0]['analyse']['id']);
            error_log("analyses/byProviders ($task_id) responseCode: $responseCode");
            error_log(print_r($resultset, true));
        }
        curl_close($ch);
    }

    public function project_get_wordcount(Request $request, Response $response, $args)
    {
        $project_id = $args['project_id'];

        $projectDao = new DAO\ProjectDao();
        $project = $projectDao->getProject($project_id);

        if (!empty($project)) {
        $this->project_cron_1_minute($request); // Trigger update

        $word_count = $project->getWordCount();
        }
        if (empty($word_count) || $word_count == 1) $word_count = '-';

        $response->getBody()->write((string)$word_count);
        return $response->withHeader('Content-Type', 'text/html;charset=UTF-8');
    }
}

$route_handler = new ProjectRouteHandler();
$route_handler->init();
unset ($route_handler);
