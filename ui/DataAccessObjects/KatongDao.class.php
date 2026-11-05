<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;

require_once __DIR__."/BaseDao.php";
require_once __DIR__.'/../../api/lib/PDOWrapper.class.php';

/*
 * Katong review service: an alternative to Phrase TMS, chosen per project on project create.
 *
 * A Katong project writes the same TWB rows as a Phrase self service project (Projects, MemsourceProjects,
 * MemsourceProjectLanguages, MemsourceSelfServiceProjects, MemsourceTasks, Tasks...). The Memsource rows follow
 * the Shell Task convention (numeric uid = TWB project/task id) as Katong ids are UUIDs, the real Katong ids are
 * in KatongProjects/KatongTasks.
 *
 * Katong job model: one job per project, for each target language an 'mt' task (runs by itself), a 'review' task
 * (TWB Translation, workflowLevel 1, may be split into parts) and optionally an 'sme_review' task (TWB Revision,
 * workflowLevel 2). TWB Tasks for a language are created when its 'mt' task completes (word counts are only known
 * then), which is the Katong equivalent of the Phrase JOB_CREATED hook.
 */
class KatongDao extends BaseDao
{
    const KIND_TO_WORKFLOW_LEVEL = ['review' => 1, 'sme_review' => 2];
    const WORKFLOW_LEVELS = ['Translation', 'Revision', '', '', '', '', '', '', '', '', '', ''];
    const FILE_EXTENSIONS = ['docx', 'pptx', 'xlsx', 'txt', 'html', 'htm', 'srt', 'vtt'];
    const CONTENT_TYPES = ['general', 'health', 'legal'];
    const UI_LANGS = ['en', 'fr', 'es', 'ar', 'sw', 'pt'];
    // Katong maps es to es-419 and pt to pt-PT, so only allow the matching TWB (Phrase) locales for those
    const ONLY_VARIANTS = ['es' => ['es', 'es_419'], 'pt' => ['pt', 'pt_pt']];

    public function __construct()
    {
        $this->siteApi = Common\Lib\Settings::get('site.api');
    }

    public static function setting($key, $default = '')
    {
        try {
            return Common\Lib\Settings::get("katong.$key");
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function enabled()
    {
        return in_array(strtolower((string)self::setting('enabled')), ['y', 'yes', '1', 'true', 'on']) && self::setting('api_key') !== '';
    }

    /*
     * Calls the Katong API, returns [HTTP code, decoded JSON body (or raw body if $raw), response headers]
     * $body is JSON encoded unless $multipart is set (then it is passed to CURLOPT_POSTFIELDS as is)
     */
    public function api($method, $path, $body = null, $multipart = false, $raw = false)
    {
        $url = rtrim(self::setting('api_url', 'https://katong.bredenkamp.ch/v1/'), '/') . '/' . ltrim($path, '/');
        $headers = ['Authorization: Bearer ' . self::setting('api_key'), 'Accept: application/json'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        if (!is_null($body)) {
            if ($multipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            } else {
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response_headers = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$response_headers) {
            $parts = explode(':', $header, 2);
            if (count($parts) == 2) $response_headers[strtolower(trim($parts[0]))] = trim($parts[1]);
            return strlen($header);
        });
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($error_number = curl_errno($ch)) error_log("Katong $method $url Curl error ($error_number): " . curl_error($ch));
        curl_close($ch);
        if ($responseCode < 200 || $responseCode > 299) error_log("Katong $method $url responseCode: $responseCode " . mb_substr((string)$result, 0, 2000));
        return [$responseCode, $raw ? $result : json_decode((string)$result, true), $response_headers];
    }

    /*
     * Language code Katong uses for a TWB language/country (ISO 639-1 from the Phrase code in selections), 0 if not usable
     */
    public function katong_code($kp_language, $kp_country)
    {
        $projectDao = new ProjectDao();
        $memsource = strtolower((string)$projectDao->convert_language_country_to_memsource($kp_language, $kp_country));
        if ($memsource === '' || $memsource === '0') return 0;
        $code = explode('_', $memsource)[0];
        if (!empty(self::ONLY_VARIANTS[$code]) && !in_array($memsource, self::ONLY_VARIANTS[$code])) return 0;
        return $code;
    }

    /*
     * Language codes and pairs Katong engines support, from GET /v1/engines: ['codes' => ['en' => 1, ...], 'pairs' => ['en|fr' => 1, ...]]
     */
    public function get_engine_languages()
    {
        static $languages = null;
        if (!is_null($languages)) return $languages;

        $languages = ['codes' => [], 'pairs' => []];
        [$code, $engines] = $this->api('GET', 'engines');
        if ($code != 200 || !is_array($engines)) return $languages;
        foreach ($engines as $engine) {
            if (empty($engine['lang_pairs'])) continue;
            foreach ($engine['lang_pairs'] as $pair) {
                $codes = preg_split('/[^A-Za-z0-9]+/', strtolower($pair), -1, PREG_SPLIT_NO_EMPTY); // e.g. "en-fr", "en>fr" or "en|fr"
                if (count($codes) != 2) continue;
                $languages['codes'][$codes[0]] = 1;
                $languages['codes'][$codes[1]] = 1;
                $languages['pairs']["{$codes[0]}|{$codes[1]}"] = 1;
            }
        }
        return $languages;
    }

    /*
     * For the project create page: TWB selections ('xx-YY') usable with Katong mapped to their Katong code, plus the supported pairs
     */
    public function get_language_selection_for_create()
    {
        $projectDao = new ProjectDao();
        $engine_languages = $this->get_engine_languages();
        $selections = [];
        foreach ($projectDao->get_selections() as $selection) {
            if (!$selection['enabled']) continue;
            $code = $this->katong_code($selection['language_code'], $selection['country_code']);
            if ($code && !empty($engine_languages['codes'][$code])) $selections[$selection['language_code'] . '-' . $selection['country_code']] = $code;
        }
        return ['selections' => $selections, 'pairs' => array_keys($engine_languages['pairs'])];
    }

    public function get_katong_project($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_katong_project', LibAPI\PDOWrapper::cleanse($project_id));
        if (empty($result)) return 0;
        return $result[0];
    }

    public function get_katong_project_by_job($job_id)
    {
        $result = LibAPI\PDOWrapper::call('get_katong_project_by_job', LibAPI\PDOWrapper::cleanseWrapStr($job_id));
        if (empty($result)) return 0;
        return $result[0];
    }

    public function get_katong_task($katong_task_id)
    {
        $result = LibAPI\PDOWrapper::call('get_katong_task', LibAPI\PDOWrapper::cleanseWrapStr($katong_task_id));
        if (empty($result)) return 0;
        return $result[0];
    }

    public function get_katong_task_by_task_id($task_id)
    {
        $result = LibAPI\PDOWrapper::call('get_katong_task_by_task_id', LibAPI\PDOWrapper::cleanse($task_id));
        if (empty($result)) return 0;
        return $result[0];
    }

    public function is_katong_project($project_id)
    {
        return !empty($this->get_katong_project($project_id));
    }

    private function set_katong_task($job_id, $task)
    {
        LibAPI\PDOWrapper::call('set_katong_task',
            LibAPI\PDOWrapper::cleanseWrapStr($task['id']) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($job_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($task['kind']) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($task['target_lang']) . ',' .
            LibAPI\PDOWrapper::cleanse(empty($task['round']) ? 1 : $task['round']) . ',' .
            LibAPI\PDOWrapper::cleanse(empty($task['part']) ? 1 : $task['part']) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($task['status']));
    }

    private function update_katong_task_status($katong_task_id, $status)
    {
        LibAPI\PDOWrapper::call('update_katong_task_status', LibAPI\PDOWrapper::cleanseWrapStr($katong_task_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($status));
    }

    private function iso8601($date)
    {
        return substr($date, 0, 10) . 'T' . substr($date, 11, 8) . 'Z';
    }

    /*
     * Creates the Katong job for a TWB project just created on the project create page (Katong equivalent of UserDao::create_memsource_project())
     * Returns the MemsourceProjects row (as create_memsource_project() does) or 0 on failure
     */
    public function create_katong_project($post, $project, $file_name, $file, $user_id)
    {
        $projectDao = new ProjectDao();
        $project_id = $project->getId();

        [$kp_language, $kp_country] = $projectDao->convert_selection_to_language_country($post['sourceLanguageSelect']);
        $source_lang = $this->katong_code($kp_language, $kp_country);
        if (!$source_lang) {
            error_log("create_katong_project($project_id) source language not supported: {$post['sourceLanguageSelect']}");
            return 0;
        }
        $kp_source_language = "{$kp_language}-{$kp_country}";

        $targetCount = 0;
        $target_langs = [];
        $kp_target_languages = [];
        while (!empty($post["target_language_$targetCount"])) {
            [$kp_language, $kp_country] = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
            $lang = $this->katong_code($kp_language, $kp_country);
            if (!$lang || $lang === $source_lang || in_array($lang, $target_langs)) {
                error_log("create_katong_project($project_id) target language not supported or repeated: " . $post["target_language_$targetCount"]);
                return 0;
            }
            $target_langs[] = $lang;
            $kp_target_languages[] = "{$kp_language}-{$kp_country}";
            $targetCount++;
        }
        if (empty($target_langs)) return 0;

        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($extension, self::FILE_EXTENSIONS)) {
            error_log("create_katong_project($project_id) file type not supported: $file_name");
            return 0;
        }

        // Upload source file
        $tmp = tempnam(sys_get_temp_dir(), 'katong');
        file_put_contents($tmp, $file);
        [$code, $uploaded] = $this->api('POST', 'files', ['file' => new \CURLFile($tmp, 'application/octet-stream', $file_name)], true);
        unlink($tmp);
        if ($code != 201 || empty($uploaded['id'])) return 0;

        // Create job
        $content_type = !empty($post['katong_content_type']) && in_array($post['katong_content_type'], self::CONTENT_TYPES) ? $post['katong_content_type'] : 'general';
        $sme = !empty($post['katong_sme_review']);
        $external_ref = "twb-project-$project_id";
        $job = [
            'external_ref' => $external_ref,
            'partner_id'   => self::setting('partner_id', 'twb-sandbox'),
            'source_lang'  => $source_lang,
            'target_langs' => $target_langs,
            'content_type' => $content_type,
            'title'        => mb_substr($project->getTitle(), 0, 500),
            'due_at'       => $this->iso8601($project->getDeadline()),
            'source'       => ['file_id' => $uploaded['id']],
            'review_langs' => $target_langs,
            'brief'        => mb_substr($project->getDescription(), 0, 20000),
        ];
        if ($sme) {
            $job['sme_langs'] = $target_langs;
            $job['sme_mode']  = 'edit';
        }
        [$code, $job_result] = $this->api('POST', 'jobs', $job);
        if (($code != 201 && $code != 200) || empty($job_result['id'])) return 0;
        $job_id = $job_result['id'];
        error_log("create_katong_project($project_id) job: $job_id");

        $projectDao->record_memsource_project_languages($project_id, $kp_source_language, implode(',', $kp_target_languages));

        LibAPI\PDOWrapper::call('set_katong_project',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($job_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($external_ref) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($content_type) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($file_name) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr(empty($job_result['status']) ? 'queued' : $job_result['status']));
        foreach ($job_result['tasks'] as $task) $this->set_katong_task($job_id, $task);

        // Same rows as a Phrase self service project, using the Shell convention for ids
        $workflow_levels = self::WORKFLOW_LEVELS;
        if (!$sme) $workflow_levels[1] = '';
        $projectDao->set_memsource_project($project_id, $project_id, $project_id, $user_id, $user_id, $workflow_levels);
        $projectDao->update_project_owner_id($project_id, $user_id, 1);
        $projectDao->set_memsource_self_service_project($project_id, 1);

        // In case MT has already finished (e.g. exact memory matches only)
        $this->materialise_job($job_result);

        return $projectDao->get_memsource_project($project_id);
    }

    /*
     * Creates TWB Tasks for every language of the job whose 'mt' task has finished and which do not have them yet
     */
    public function materialise_job($job)
    {
        if (empty($job['tasks'])) return;
        foreach ($job['tasks'] as $task) {
            if ($task['kind'] == 'mt' && in_array($task['status'], ['completed', 'delivered'])) $this->materialise_language($job, $task['target_lang']);
        }
    }

    public function get_job($job_id)
    {
        [$code, $job] = $this->api('GET', "jobs/$job_id");
        if ($code != 200 || empty($job['id'])) return 0;
        return $job;
    }

    /*
     * Katong equivalent of the Phrase JOB_CREATED hook (ProjectRouteHandler::create_task()) for one target language
     */
    public function materialise_language($job, $lang)
    {
        $projectDao = new ProjectDao();
        $katong_project = $this->get_katong_project_by_job($job['id']);
        if (empty($katong_project)) {
            error_log("materialise_language: no KatongProjects for job {$job['id']}");
            return;
        }
        $project_id = $katong_project['project_id'];
        $memsource_project = $projectDao->get_memsource_project($project_id);
        $self_service_project = $projectDao->get_memsource_self_service_project($project_id);

        $tasks = array_filter($job['tasks'], function($t) use ($lang) { return $t['target_lang'] === $lang && !empty(self::KIND_TO_WORKFLOW_LEVEL[$t['kind']]); });

        // Split large unsplit, unstarted review tasks the way Phrase jobs are split (ceil(words/2000) parts)
        $resplit = false;
        foreach ($tasks as $task) {
            if ($task['kind'] == 'review' && $self_service_project && $self_service_project['split'] && $task['status'] == 'queued'
                    && (empty($task['part']) || $task['part'] == 1) && count(array_filter($tasks, function($t) { return $t['kind'] == 'review' && !empty($t['part']) && $t['part'] > 1; })) == 0) {
                $local = $this->get_katong_task($task['id']);
                if ($local && $local['materialised']) continue;
                $words = $this->get_word_count($task['id']);
                if ($words > 2000) {
                    [$code, $parts] = $this->api('POST', "tasks/{$task['id']}/split", ['parts' => (int)ceil($words/2000.)]);
                    if ($code == 200) $resplit = true;
                }
            }
        }
        if ($resplit) {
            $job = $this->get_job($job['id']);
            if (empty($job)) return;
            $tasks = array_filter($job['tasks'], function($t) use ($lang) { return $t['target_lang'] === $lang && !empty(self::KIND_TO_WORKFLOW_LEVEL[$t['kind']]); });
        }

        $internal_top_level = array_search($lang, $job['target_langs']) + 1;
        $review_parts = count(array_filter($tasks, function($t) { return $t['kind'] == 'review'; }));
        usort($tasks, function($a, $b) { return [self::KIND_TO_WORKFLOW_LEVEL[$a['kind']], $a['round'] ?? 1, $a['part'] ?? 1] <=> [self::KIND_TO_WORKFLOW_LEVEL[$b['kind']], $b['round'] ?? 1, $b['part'] ?? 1]; });
        foreach ($tasks as $task) {
            $this->set_katong_task($job['id'], $task);
            $internalId = (string)$internal_top_level;
            if ($task['kind'] == 'review' && $review_parts > 1) $internalId .= '.' . (empty($task['part']) ? 1 : $task['part']);
            $this->create_twb_task($project_id, $memsource_project, $self_service_project, $job, $task, $internalId);
        }
    }

    public function get_word_count($katong_task_id)
    {
        [$code, $metrics] = $this->api('GET', "tasks/$katong_task_id/metrics");
        if ($code != 200 || empty($metrics['words']['total'])) return 0;
        return (int)$metrics['words']['total'];
    }

    /*
     * Creates one TWB Task for a Katong review/sme_review task (see TmsDao::create_twb_task())
     */
    private function create_twb_task($project_id, $memsource_project, $self_service_project, $job, $katong_task, $internalId)
    {
        $result = LibAPI\PDOWrapper::call('claim_katong_task_for_materialising', LibAPI\PDOWrapper::cleanseWrapStr($katong_task['id']));
        if (empty($result[0]['result'])) return; // Already done (or being done by a concurrent hook/cron)

        $projectDao = new ProjectDao();
        $katong_project = $this->get_katong_project($project_id);

        // Target locale is the TWB one chosen on create (same order as target_langs)
        $project_languages = $projectDao->get_memsource_project_languages($project_id);
        $kp_target_language_pairs = explode(',', $project_languages['kp_target_language_pairs']);

        $task_id = TmsDao::create_twb_task($project_id, [
            'ref'           => "Katong {$katong_task['id']}",
            'workflowLevel' => self::KIND_TO_WORKFLOW_LEVEL[$katong_task['kind']],
            'internalId'    => $internalId,
            'kp_target'     => $kp_target_language_pairs[array_search($katong_task['target_lang'], $job['target_langs'])],
            'words'         => $this->get_word_count($katong_task['id']),
            'begin'         => isset($katong_task['seg_begin']) ? (int)$katong_task['seg_begin'] : 0,
            'end'           => isset($katong_task['seg_end'])   ? (int)$katong_task['seg_end']   : (empty($katong_task['progress']['total']) ? 0 : $katong_task['progress']['total'] - 1),
            'round'         => empty($katong_task['round']) ? 1 : $katong_task['round'],
            'file_name'     => $katong_project['file_name'],
        ], function($task_id) use ($katong_task) {
            LibAPI\PDOWrapper::call('link_katong_task', LibAPI\PDOWrapper::cleanseWrapStr($katong_task['id']) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
        });
        if (!$task_id) LibAPI\PDOWrapper::call('unclaim_katong_task_for_materialising', LibAPI\PDOWrapper::cleanseWrapStr($katong_task['id']));
    }

    /*
     * On claim (UserDao::claimTask()) or deadline change: record the TWB assignee and deadline in Katong
     */
    public function assign($task_id, $user_id, $deadline)
    {
        $katong_task = $this->get_katong_task_by_task_id($task_id);
        if (empty($katong_task)) return 0;
        $userDao = new UserDao();
        $user = $userDao->getUser($user_id);
        [$code] = $this->api('POST', "tasks/{$katong_task['katong_task_id']}/assign", [
            'assignee_id'  => (string)$user_id,
            'display_name' => $user->getDisplayName(),
            'role'         => $katong_task['kind'] == 'sme_review' ? 'sme' : 'reviewer',
            'deadline'     => $this->iso8601($deadline),
        ]);
        return $code == 200;
    }

    public function unassign($task_id)
    {
        error_log("Katong has no unassign yet, task_id $task_id only unclaimed in TWB");
        return 1;
    }

    /*
     * Katong has no separate deadline update, the deadline is set on assign
     */
    public function set_task_deadline($task_id, $deadline)
    {
        $projectDao = new ProjectDao();
        if ($claimant_id = $projectDao->getUserClaimedTask($task_id)) $this->assign($task_id, $claimant_id, $deadline);
    }

    public function set_project_deadline($project_id, $deadline)
    {
        error_log("Katong has no API yet to change the job due date, project_id $project_id only changed in TWB");
    }

    public function set_cancelled($task_id, $cancelled)
    {
        error_log("Katong has no cancel yet, task_id $task_id only " . ($cancelled ? 'cancelled' : 'uncancelled') . ' in TWB');
    }

    public function sync_project($project_id)
    {
        $katong_project = $this->get_katong_project($project_id);
        if (empty($katong_project) || !($job = $this->get_job($katong_project['job_id']))) return 'Could not read the Katong job';
        foreach ($job['tasks'] as $task) $this->set_katong_task($job['id'], $task);
        $this->materialise_job($job);
        return '';
    }

    /*
     * URL in TWB that mints a fresh one time editor link (the Katong link itself expires in 15 minutes)
     */
    public function get_editor_url($task_id)
    {
        return Common\Lib\Settings::get('site.location') . "task/$task_id/katong_editor/";
    }

    public function mint_editor_session($task_id, $user_id, $lead = false)
    {
        $katong_task = $this->get_katong_task_by_task_id($task_id);
        if (empty($katong_task)) return [0, 'Not a Katong task'];
        $katong_project = $this->get_katong_project_by_job($katong_task['job_id']);
        $job = $this->get_job($katong_task['job_id']);
        if (empty($job)) return [0, 'Katong job not found'];

        $userDao = new UserDao();
        $user = $userDao->getUser($user_id);
        $ui_lang = substr((string)Common\Lib\UserSession::getUserLanguage(), 0, 2);
        $data = [
            'user_id'      => (string)$user_id,
            'display_name' => $user->getDisplayName(),
            'role'         => $lead ? 'lead' : ($katong_task['kind'] == 'sme_review' ? 'sme' : 'reviewer'),
            'languages'    => [$job['source_lang'], $katong_task['target_lang']],
        ];
        if (in_array($ui_lang, self::UI_LANGS)) $data['ui_lang'] = $ui_lang;
        [$code, $session] = $this->api('POST', "tasks/{$katong_task['katong_task_id']}/session", $data);
        if ($code == 409) return [0, 'This step cannot be opened until review of this language is complete.'];
        if ($code == 403) return [0, 'Only the person who claimed this task can open it.'];
        if ($code != 200 || empty($session['url'])) return [0, 'Could not open the Katong editor, please try again.'];
        return [$session['url'], ''];
    }

    /*
     * Katong equivalent of UserDao::memsource_get_target_file()
     */
    public function get_target_file($task_id)
    {
        $katong_task = $this->get_katong_task_by_task_id($task_id);
        if (empty($katong_task)) return 0;
        [$code, $file] = $this->api('GET', "jobs/{$katong_task['job_id']}/deliverables/{$katong_task['target_lang']}", null, false, true);
        if ($code != 200 || strlen($file) > 100000000) return 0;
        return $file;
    }

    public function store_metrics($task_id, $katong_task_id)
    {
        [$code, $m] = $this->api('GET', "tasks/$katong_task_id/metrics");
        if ($code != 200 || !isset($m['active_ms'])) return;
        $band = function($name) use ($m) { return empty($m['words']['by_band'][$name]) ? 0 : (int)$m['words']['by_band'][$name]; };
        LibAPI\PDOWrapper::call('set_katong_task_metrics',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($katong_task_id) . ',' .
            LibAPI\PDOWrapper::cleanse((int)$m['active_ms']) . ',' .
            LibAPI\PDOWrapper::cleanse((int)$m['wall_ms']) . ',' .
            LibAPI\PDOWrapper::cleanse((int)$m['segments_accepted']) . ',' .
            LibAPI\PDOWrapper::cleanse((int)$m['segments_edited']) . ',' .
            LibAPI\PDOWrapper::cleanse((int)$m['segments_flagged']) . ',' .
            (is_null($m['mean_edit_distance']) ? 'NULL' : LibAPI\PDOWrapper::cleanse((float)$m['mean_edit_distance'])) . ',' .
            LibAPI\PDOWrapper::cleanse(empty($m['words']['total']) ? 0 : (int)$m['words']['total']) . ',' .
            LibAPI\PDOWrapper::cleanse($band('tm_101')) . ',' .
            LibAPI\PDOWrapper::cleanse($band('tm_100')) . ',' .
            LibAPI\PDOWrapper::cleanse($band('fuzzy_95_99')) . ',' .
            LibAPI\PDOWrapper::cleanse($band('fuzzy_85_94')) . ',' .
            LibAPI\PDOWrapper::cleanse($band('fuzzy_75_84')) . ',' .
            LibAPI\PDOWrapper::cleanse($band('mt')) . ',' .
            LibAPI\PDOWrapper::cleanse($band('new')) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr(json_encode(empty($m['by_reviewer']) ? [] : $m['by_reviewer'])));
    }

    /*
     * X-Katong-Signature: t=<unix seconds>,v1=<hex HMAC-SHA256 with the secret over "<t>." + raw body>
     */
    public static function verify_signature($header, $body, $tolerance = 300)
    {
        $secret = self::setting('webhook_secret');
        if ($secret === '' || $header === '') return false;
        $parts = [];
        foreach (explode(',', $header) as $p) {
            $kv = explode('=', $p, 2);
            if (count($kv) == 2) $parts[trim($kv[0])] = trim($kv[1]);
        }
        if (empty($parts['t']) || empty($parts['v1']) || !ctype_digit($parts['t'])) return false;
        if (abs(time() - (int)$parts['t']) > $tolerance) return false;
        return hash_equals(hash_hmac('sha256', "{$parts['t']}.$body", $secret), $parts['v1']);
    }

    public function is_delivery_new($delivery_id)
    {
        $result = LibAPI\PDOWrapper::call('is_katong_delivery_new', LibAPI\PDOWrapper::cleanseWrapStr($delivery_id));
        return !empty($result[0]['result']);
    }

    public function handle_event($event)
    {
        $data = $event['data'];
        switch ($event['event']) {
            case 'task.status_changed':
                $this->task_status_changed($data);
                break;
            case 'job.completed':
                LibAPI\PDOWrapper::call('update_katong_project_status', LibAPI\PDOWrapper::cleanseWrapStr($data['job_id']) . ",'completed'");
                break;
            case 'job.reopened':
                // New review round tasks: create TWB Tasks for them as for round 1
                if ($job = $this->get_job($data['job_id'])) {
                    foreach ($job['tasks'] as $task) $this->set_katong_task($job['id'], $task);
                    foreach ($data['target_langs'] as $lang) $this->materialise_language($job, $lang);
                }
                break;
            case 'segment.flagged':
                error_log('Katong segment.flagged: ' . json_encode($data));
                break;
        }
    }

    private function task_status_changed($data)
    {
        $katong_task = $this->get_katong_task($data['task_id']);
        if (empty($katong_task)) {
            // e.g. a part created by a split, or an sme_review task created after review: fetch the job to record it
            if (!($job = $this->get_job($data['job_id']))) return;
            foreach ($job['tasks'] as $task) $this->set_katong_task($job['id'], $task);
            $katong_task = $this->get_katong_task($data['task_id']);
            if (empty($katong_task)) return;
        }
        $this->update_katong_task_status($data['task_id'], $data['status']);

        if ($data['kind'] == 'mt') {
            if ($data['status'] == 'completed' || $data['status'] == 'delivered') {
                if ($job = $this->get_job($data['job_id'])) $this->materialise_language($job, $data['target_lang']);
            } elseif ($data['status'] == 'failed') {
                error_log("Katong MT failed for job {$data['job_id']} {$data['target_lang']}: {$data['detail']}");
            }
            return;
        }

        if (empty($katong_task['task_id'])) {
            // An sme_review task becoming available may not have a TWB Task yet
            if ($job = $this->get_job($data['job_id'])) $this->materialise_language($job, $data['target_lang']);
            $katong_task = $this->get_katong_task($data['task_id']);
            if (empty($katong_task['task_id'])) return;
        }
        $task_id = $katong_task['task_id'];

        switch ($data['status']) {
            case 'in_progress':
            case 'needs_rework':
                TmsDao::reopen_task($task_id);
                break;
            case 'completed':
            case 'delivered':
                TmsDao::complete_task($task_id);
                $this->store_metrics($task_id, $data['task_id']);
                break;
            case 'failed':
                error_log("Katong task failed task_id: $task_id, Katong task: {$data['task_id']}: {$data['detail']}");
                break;
        }
    }

    /*
     * Cron fallback in case a webhook delivery is missed: create any missing TWB Tasks
     */
    public function poll_jobs()
    {
        if (!self::enabled()) return;
        $katong_projects = LibAPI\PDOWrapper::call('get_katong_projects_not_complete', '');
        if (empty($katong_projects)) return;
        foreach ($katong_projects as $katong_project) {
            if (!($job = $this->get_job($katong_project['job_id']))) continue;
            foreach ($job['tasks'] as $task) $this->set_katong_task($job['id'], $task);
            $this->materialise_job($job);
            if ($job['status'] !== $katong_project['status']) LibAPI\PDOWrapper::call('update_katong_project_status', LibAPI\PDOWrapper::cleanseWrapStr($job['id']) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($job['status']));
        }
    }
}
