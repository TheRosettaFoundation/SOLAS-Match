<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;

require_once __DIR__.'/../../api/lib/PDOWrapper.class.php';

/*
 * TMSs other than Phrase (Katong, Wordbee) selectable by Site Admins on project create.
 *
 * Their projects write the same TWB rows as a Phrase self service project, with MemsourceProjects/MemsourceTasks using
 * the Shell Task convention (numeric uid = TWB project/task id). Each TMS keeps its own ids in its own tables.
 *
 * Each TMS DAO implements:
 *     assign($task_id, $user_id, $deadline)      on claim (and to resend a deadline), returns success
 *     unassign($task_id)                          on unclaim/decline
 *     set_task_deadline($task_id, $deadline)
 *     set_project_deadline($project_id, $deadline)
 *     set_cancelled($task_id, $cancelled)
 *     get_editor_url($task_id)
 *     get_target_file($task_id)                   file contents or 0
 *     sync_project($project_id)                   create any missing TWB Tasks and catch up on statuses
 *     poll_jobs()                                 cron fallback for missed webhooks
 */
class TmsDao
{
    /*
     * The TMS DAO for a TWB project, or null for Phrase and Shell projects
     */
    public static function for_project($project_id)
    {
        if (!$project_id) return null;
        $katongDao = new KatongDao();
        if ($katongDao->get_katong_project($project_id)) return $katongDao;
        $wordbeeDao = new WordbeeDao();
        if ($wordbeeDao->get_wordbee_project($project_id)) return $wordbeeDao;
        return null;
    }

    /*
     * The TMS DAO for a TWB task, or null for Phrase and Shell tasks
     */
    public static function for_task($task_id)
    {
        if (!$task_id) return null;
        $katongDao = new KatongDao();
        if ($katongDao->get_katong_task_by_task_id($task_id)) return $katongDao;
        $wordbeeDao = new WordbeeDao();
        if ($wordbeeDao->get_wordbee_task_by_task_id($task_id)) return $wordbeeDao;
        return null;
    }

    /*
     * Only call for_task() for MemsourceTasks rows that may belong to another TMS (Phrase uids are never numeric)
     */
    public static function for_memsource_task($memsource_task)
    {
        if (empty($memsource_task) || !preg_match('/^\d+$/', $memsource_task['memsource_task_uid'])) return null;
        return self::for_task($memsource_task['task_id']);
    }

    /*
     * The TMSs enabled for the Site Admin switch on project create: ['katong' => KatongDao, 'wordbee' => WordbeeDao]
     */
    public static function enabled_tms()
    {
        $tms = [];
        if (KatongDao::enabled())  $tms['katong']  = new KatongDao();
        if (WordbeeDao::enabled()) $tms['wordbee'] = new WordbeeDao();
        return $tms;
    }

    /*
     * Creates one TWB Task for a TMS task, mirroring what ProjectRouteHandler::create_task() does for a Phrase jobPart
     * $spec: ref (TMS id, for logs), workflowLevel (1 Translation, 2 Revision), internalId, kp_target ('xx-YY'), words,
     *        begin, end (segment range), round, file_name
     * $on_created($task_id) is called as soon as the Task exists (to link it to the TMS task)
     * Returns the new task_id or 0
     */
    public static function create_twb_task($project_id, $spec, $on_created = null)
    {
        $projectDao = new ProjectDao();
        $taskDao    = new TaskDao();

        $memsource_project = $projectDao->get_memsource_project($project_id);
        $self_service_project = $projectDao->get_memsource_self_service_project($memsource_project['memsource_project_id'] ?? $project_id);
        $workflow_levels = [$memsource_project['workflow_level_1'], $memsource_project['workflow_level_2'], $memsource_project['workflow_level_3'], $memsource_project['workflow_level_4'], $memsource_project['workflow_level_5'], $memsource_project['workflow_level_6'], $memsource_project['workflow_level_7'], $memsource_project['workflow_level_8'], $memsource_project['workflow_level_9'], $memsource_project['workflow_level_10'], $memsource_project['workflow_level_11'], $memsource_project['workflow_level_12']];
        $workflowLevel = $spec['workflowLevel'];
        $internalId = $spec['internalId'];
        $taskType = $workflowLevel == 1 ? Common\Enums\TaskTypeEnum::TRANSLATION : Common\Enums\TaskTypeEnum::PROOFREADING;
        $words = (int)$spec['words'];

        $project = $projectDao->getProject($project_id);

        $task = new Common\Protobufs\Models\Task();
        $task->setProjectId($project_id);
        $task->setTitle(mb_substr("$internalId {$spec['file_name']}", 0, 128));

        $projectSourceLocale = $project->getSourceLocale();
        $taskSourceLocale = new Common\Protobufs\Models\Locale();
        $taskSourceLocale->setLanguageCode($projectSourceLocale->getLanguageCode());
        $taskSourceLocale->setCountryCode($projectSourceLocale->getCountryCode());
        $task->setSourceLocale($taskSourceLocale);
        $task->setTaskStatus(Common\Enums\TaskStatusEnum::WAITING_FOR_PREREQUISITES);

        $project_languages = $projectDao->get_memsource_project_languages($project_id);
        $kp_target_language_pairs = explode(',', $project_languages['kp_target_language_pairs']);
        [$target_language, $target_country] = $projectDao->convert_selection_to_language_country($spec['kp_target']);
        $taskTargetLocale = new Common\Protobufs\Models\Locale();
        $taskTargetLocale->setLanguageCode($target_language);
        $taskTargetLocale->setCountryCode($target_country);
        $task->setTargetLocale($taskTargetLocale);
        $task->setTaskType($taskType);

        if ($words) {
            $task->setWordCount($words);
            $task->set_word_count_partner_weighted($words);
            $task->set_word_count_original($words);
            $task->set_source_quantity($words);
            $projectDao->queue_asana_project($project_id);
            // Parts are split before their TWB Tasks are created (unlike Phrase), so add each part, they sum to the whole
            if ($taskType == Common\Enums\TaskTypeEnum::TRANSLATION && (empty($spec['round']) || $spec['round'] == 1) && $spec['kp_target'] === $kp_target_language_pairs[0]) {
                $projectDao->add_to_project_word_count($project_id, $words);
            }
        } else {
            $task->setWordCount(1);
        }

        // Same deadlines as a Phrase self service split project
        $deadline = strtotime($project->getDeadline());
        $now = time();
        $total = $deadline - $now;
        if ($total < 0) $total = 0;
        $deadline_less_4_days = $deadline - $total*45/100;
        $deadline_less_1_days = $deadline - $total*5/100;
        if ($workflow_levels[1] === '') $deadline_less_4_days = $deadline_less_1_days; // Only 1 workflow, give all the time to that
        $task->setDeadline(gmdate('Y-m-d H:i:s', $taskType == Common\Enums\TaskTypeEnum::TRANSLATION ? $deadline_less_4_days : $deadline_less_1_days));
        $task->setPublished(1);

        $task_id = $taskDao->createTaskDirectly($task);
        if (!$task_id) {
            error_log("Failed to add task for TMS task {$spec['ref']}");
            return 0;
        }
        error_log("Added Task: $task_id for TMS task {$spec['ref']}");
        if ($on_created) $on_created($task_id);

        $success = $projectDao->set_memsource_task($task_id, 0, $task_id, $target_language, $internalId, $workflowLevel, (int)$spec['begin'], (int)$spec['end'], 0);
        if (!$success) {
            $projectDao->delete_task_directly($task_id);
            error_log("delete_task_directly($task_id) because of set_memsource_task fail (TMS task {$spec['ref']})");
            return 0;
        }
        if ($taskType == Common\Enums\TaskTypeEnum::TRANSLATION && $words) $projectDao->increment_used_entitlement($task_id, 0, $words);

        $forward_order = [];
        $reverse_order = [];
        foreach ($workflow_levels as $i => $workflow_level) {
            if (!empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level])) {
                $forward_order[Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level]] = ($i == 11 || empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i + 1]])) ? 0 : Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i + 1]];
                $reverse_order[Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_level]] = ($i ==  0 || empty(Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i - 1]])) ? 0 : Common\Enums\TaskTypeEnum::$task_type_to_enum[$workflow_levels[$i - 1]];
            }
        }
        $top_level = $projectDao->get_top_level($internalId);
        foreach ($projectDao->get_tasks_for_project($project_id) as $project_task) {
            if ($project_task['task_id'] == $task_id) continue;
            if ($top_level == $projectDao->get_top_level($project_task['internalId'])) {
                if (!empty($forward_order[$taskType]) && $forward_order[$taskType] == $project_task['task-type_id'])
                    $projectDao->set_taskclaims_required_to_make_claimable($task_id, $project_task['task_id'], $project_id);
                if (!empty($reverse_order[$taskType]) && $reverse_order[$taskType] == $project_task['task-type_id'])
                    $projectDao->set_taskclaims_required_to_make_claimable($project_task['task_id'], $task_id, $project_id);
            }
        }

        if ($projectDao->is_task_claimable($task_id)) $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::PENDING_CLAIM);

        if ($self_service_project) {
            $creator = $taskDao->get_self_creator_from_project_file($project_id);
            $taskDao->trackTaskDirectly($creator['id'], $task_id);
        }

        // Original (source) file for the Task: copy the project file (Phrase needs a cron download)
        $uploadFolder = Common\Lib\Settings::get('files.upload_path') . "proj-$project_id/task-$task_id/v-0";
        mkdir($uploadFolder, 0755, true);
        $filesFolder = Common\Lib\Settings::get('files.upload_path') . "files/proj-$project_id/task-$task_id/v-0";
        mkdir($filesFolder, 0755, true);
        $filename = str_replace('/', '_', $spec['file_name']);
        $source = $taskDao->getPhysicalProjectFilePath($project_id, $spec['file_name']);
        if ($source === false || !copy($source, "$filesFolder/$filename")) file_put_contents("$filesFolder/$filename", '');
        file_put_contents("$uploadFolder/$filename", "files/proj-$project_id/task-$task_id/v-0/$filename");

        return $task_id;
    }

    /*
     * TWB side of a task completed in the TMS (same as Phrase COMPLETED_BY_LINGUIST in ProjectRouteHandler::update_task_status())
     */
    public static function complete_task($task_id)
    {
        $taskDao = new TaskDao();
        if (!$taskDao->taskIsClaimed($task_id)) $taskDao->claimTask($task_id, 62927); // translators@translatorswithoutborders.org
        if ($taskDao->getTaskStatus($task_id) != Common\Enums\TaskStatusEnum::COMPLETE) {
            $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::COMPLETE);
            $taskDao->sendTaskUploadNotifications($task_id, 1);
            $taskDao->set_task_complete_date($task_id);
            error_log("TMS completed task_id: $task_id");
        }
    }

    /*
     * TWB side of a task reopened in the TMS
     */
    public static function reopen_task($task_id)
    {
        $taskDao = new TaskDao();
        if ($taskDao->taskIsClaimed($task_id) && $taskDao->getTaskStatus($task_id) == Common\Enums\TaskStatusEnum::COMPLETE) {
            $taskDao->setTaskStatus($task_id, Common\Enums\TaskStatusEnum::IN_PROGRESS);
            error_log("TMS reopened task_id: $task_id, reverting from COMPLETE");
        }
    }
}
