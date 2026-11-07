<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\API\Lib as LibAPI;
use \SolasMatch\Common as Common;

require_once __DIR__.'/../../api/lib/PDOWrapper.class.php';

/*
 * Wordbee Translator: an alternative to Phrase TMS, chosen per project on project create (see TmsDao).
 *
 * Uses API2 (https://help.wordbee.com/api2) and API1 (https://help.wordbee.com/api1) only where API2 has no equivalent
 * (create project, project deadline).
 *
 * A TWB project is a Wordbee Standard project with one document. For each target language, once the document is imported,
 * TWB asks Wordbee for jobs from the Translation + Revision workflow profile: split into segment chunks of about 2000 words
 * when the document is longer (ProjectAndSegments mode), which is the Wordbee equivalent of Phrase job splitting.
 * TR jobs become TWB Translation tasks (workflowLevel 1), RV jobs Revision tasks (workflowLevel 2).
 *
 * Long API2 operations are asynchronous ("trm" requests): TWB waits a few seconds, then continues from the Wordbee callback
 * (/wordbee_hook?type=trm) or from cron. Job status changes come from Wordbee webhooks (configured in the Wordbee UI) and
 * from a cron poll, both of which run sync_project().
 */
class WordbeeDao
{
    const TASK_CODE_TO_WORKFLOW_LEVEL = ['TR' => 1, 'RV' => 2];
    const WORKFLOW_LEVELS = ['Translation', 'Revision', '', '', '', '', '', '', '', '', '', ''];

    // API2 job status (https://help.wordbee.com/api2/job-status)
    const JOB_IN_PROGRESS = 5;
    const JOB_COMPLETED = 6;
    const JOB_DELIVERED = 7;
    const JOB_ARCHIVED = 8;
    const JOB_CANCELLED = 10;

    // workflows/jobs/new modes, to confirm against the platform (README Step 0)
    const JOBS_MODE_WHOLE = 'Project';
    const JOBS_MODE_SPLIT = 'ProjectAndSegments';

    const WORDS_PER_PART = 2000;
    const POLL_MINUTES = 10;
    const TOKEN_MAX_IDLE = 1500; // API2 tokens expire after 30 minutes without use

    public static function setting($key, $default = '')
    {
        try {
            return Common\Lib\Settings::get("wordbee.$key");
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function enabled()
    {
        return in_array(strtolower((string)self::setting('enabled')), ['y', 'yes', '1', 'true', 'on']) && self::setting('api2_key') !== '' && self::setting('account_id') !== '';
    }

    private function cache_get($key, $max_age)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_cache', LibAPI\PDOWrapper::cleanseWrapStr($key));
        if (empty($result) || $result[0]['age'] > $max_age) return null;
        return $result[0]['v'];
    }

    private function cache_set($key, $value)
    {
        LibAPI\PDOWrapper::call('set_wordbee_cache', LibAPI\PDOWrapper::cleanseWrapStr($key) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($value));
    }

    /*
     * curl helper, returns [HTTP code, body, response headers]
     */
    protected function http($method, $url, $headers, $body = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        if (!is_null($body)) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($error_number = curl_errno($ch)) error_log("Wordbee $method $url Curl error ($error_number): " . curl_error($ch));
        curl_close($ch);
        if ($responseCode < 200 || $responseCode > 299) error_log("Wordbee $method $url responseCode: $responseCode " . mb_substr((string)$result, 0, 2000));
        if ($responseCode == 429) error_log('Wordbee API throttled (1000 calls per hour), will retry later');
        return [$responseCode, $result];
    }

    private function api2_token($renew = false)
    {
        if (!$renew && ($token = $this->cache_get('token2', self::TOKEN_MAX_IDLE))) return $token;
        [$code, $body] = $this->http('POST', self::setting('api2_url') . 'auth/token', ['Content-Type: application/json'],
            json_encode(['accountid' => self::setting('account_id'), 'key' => self::setting('api2_key')]));
        if ($code != 200) return '';
        $token = trim($body, "\" \r\n");
        $this->cache_set('token2', $token);
        return $token;
    }

    /*
     * API2 call, returns [HTTP code, decoded JSON (or raw body)]
     * $body: array (sent as JSON), or ['file' => CURLFile] with $multipart
     */
    public function api2($method, $path, $body = null, $multipart = false, $raw = false)
    {
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $token = $this->api2_token($attempt > 0);
            if ($token === '') return [401, null];
            $headers = ["X-Auth-Token: $token", 'X-Auth-AccountId: ' . self::setting('account_id')];
            if (!is_null($body) && !$multipart) {
                $headers[] = 'Content-Type: application/json';
                $payload = json_encode($body);
            } else {
                $payload = $body;
            }
            [$code, $result] = $this->http($method, self::setting('api2_url') . ltrim($path, '/'), $headers, $payload);
            if (($code == 401 || ($code == 400 && stripos((string)$result, 'token invalid or expired') !== false)) && $attempt == 0) continue;
            if ($code >= 200 && $code <= 299) $this->cache_set('token2', $token); // Keep track of idle time
            return [$code, $raw ? $result : json_decode((string)$result, true)];
        }
    }

    /*
     * API1 call (connects and disconnects each time, API1 is only used for a few calls), returns [HTTP code, decoded JSON]
     */
    public function api1($method, $path, $params = [])
    {
        $base = self::setting('api1_url');
        [$code, $token] = $this->http('GET', $base . 'connect?' . http_build_query(['account' => self::setting('account_id'), 'pwd' => self::setting('api1_password'), 'json' => 'true']), []);
        if ($code != 200) return [$code, null];
        $token = trim($token, "\" \r\n");
        $params['token'] = $token;
        [$code, $result] = $this->http($method, $base . ltrim($path, '/') . '?' . http_build_query($params), ['Content-Type: application/json'], $method == 'GET' ? null : '');
        $this->http('PUT', $base . 'disconnect?' . http_build_query(['token' => $token]), ['Content-Type: application/json'], '');
        return [$code, json_decode((string)$result, true)];
    }

    /*
     * Waits for an API2 asynchronous operation, returns the final trm response, or the last one if still running after $max_seconds
     */
    public function trm_wait($response, $max_seconds = 20)
    {
        $start = time();
        while (!empty($response['trm']) && in_array($response['trm']['status'], ['Waiting', 'Executing']) && !empty($response['trm']['requestid']) && time() - $start < $max_seconds) {
            sleep(3); // Wordbee asks for 2-5 seconds between polls
            [$code, $status] = $this->api2('GET', 'trm/status?requestid=' . $response['trm']['requestid']);
            if ($code != 200 || empty($status['trm'])) break;
            $response = $status;
        }
        return $response;
    }

    private function callback()
    {
        return ['url' => Common\Lib\Settings::get('site.location') . 'wordbee_hook?type=trm', 'headers' => [['key' => 'X-TWB-Webhook-Secret', 'value' => self::setting('webhook_secret')]]];
    }

    private function iso8601($date)
    {
        return substr($date, 0, 10) . 'T' . substr($date, 11, 8) . 'Z';
    }

    /*
     * Wordbee language codes ['fr-fr' => 'fr-FR', 'en' => 'en', ...], keys in lower case
     */
    public function get_languages()
    {
        static $languages = null;
        if (!is_null($languages)) return $languages;
        $json = $this->cache_get('languages', 24*60*60);
        if (is_null($json)) {
            [$code, $codes] = $this->api2('GET', 'settings/languages/codes');
            if ($code != 200 || !is_array($codes)) return $languages = [];
            $json = json_encode($codes);
            $this->cache_set('languages', $json);
        }
        $languages = [];
        foreach (json_decode($json, true) as $language) if (!empty($language['v'])) $languages[strtolower($language['v'])] = $language['v'];
        return $languages;
    }

    /*
     * Wordbee code for a TWB language/country, 0 if Wordbee does not have it
     */
    public function locale($kp_language, $kp_country)
    {
        $languages = $this->get_languages();
        $projectDao = new ProjectDao();
        $memsource = str_replace('_', '-', strtolower((string)$projectDao->convert_language_country_to_memsource($kp_language, $kp_country)));
        foreach ([$memsource, strtolower("$kp_language-$kp_country"), strtolower($kp_language)] as $candidate) {
            if ($candidate !== '' && $candidate !== '0' && !empty($languages[$candidate])) return $languages[$candidate];
        }
        return 0;
    }

    /*
     * Allowed upload extensions (from GET settings/documentformats)
     */
    public function get_file_extensions()
    {
        $json = $this->cache_get('formats', 24*60*60);
        if (is_null($json)) {
            [$code, $formats] = $this->api2('GET', 'settings/documentformats');
            if ($code != 200 || !is_array($formats)) return ['docx', 'xlsx', 'pptx', 'odt', 'txt', 'csv', 'html', 'srt'];
            $json = json_encode($formats);
            $this->cache_set('formats', $json);
        }
        $extensions = [];
        foreach (json_decode($json, true) as $format) {
            if (!empty($format['extensions'])) foreach ($format['extensions'] as $extension) $extensions[strtolower(ltrim($extension, '.*'))] = 1;
        }
        return array_keys($extensions);
    }

    /*
     * For the project create page: TWB selections ('xx-YY') usable with Wordbee mapped to their Wordbee code, plus file extensions
     */
    public function get_selection_for_create()
    {
        $projectDao = new ProjectDao();
        $selections = [];
        foreach ($projectDao->get_selections() as $selection) {
            if (!$selection['enabled']) continue;
            if ($code = $this->locale($selection['language_code'], $selection['country_code'])) $selections[$selection['language_code'] . '-' . $selection['country_code']] = $code;
        }
        return ['selections' => $selections, 'pairs' => [], 'extensions' => $this->get_file_extensions()];
    }

    public function get_wordbee_project($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_project', LibAPI\PDOWrapper::cleanse($project_id));
        return empty($result) ? 0 : $result[0];
    }

    public function get_wordbee_project_by_wb_id($wb_project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_project_by_wb_id', LibAPI\PDOWrapper::cleanse($wb_project_id));
        return empty($result) ? 0 : $result[0];
    }

    public function get_wordbee_task_by_task_id($task_id)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_task_by_task_id', LibAPI\PDOWrapper::cleanse($task_id));
        return empty($result) ? 0 : $result[0];
    }

    private function get_wordbee_task($wb_job_id)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_task', LibAPI\PDOWrapper::cleanseWrapStr($wb_job_id));
        return empty($result) ? 0 : $result[0];
    }

    private function get_wordbee_tasks_for_project($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_tasks_for_project', LibAPI\PDOWrapper::cleanse($project_id));
        return empty($result) ? [] : $result;
    }

    private function get_wordbee_languages($project_id)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_languages', LibAPI\PDOWrapper::cleanse($project_id));
        return empty($result) ? [] : $result;
    }

    private function set_wordbee_language($project_id, $trg, $state, $words = 0, $segments = 0, $max_segments = 0)
    {
        LibAPI\PDOWrapper::call('set_wordbee_language', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($trg) . ',' .
            LibAPI\PDOWrapper::cleanse($state) . ',' . LibAPI\PDOWrapper::cleanse($words) . ',' . LibAPI\PDOWrapper::cleanse($segments) . ',' . LibAPI\PDOWrapper::cleanse($max_segments));
    }

    private function claim_wordbee_language($project_id, $trg, $from_state, $to_state)
    {
        $result = LibAPI\PDOWrapper::call('claim_wordbee_language', LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($trg) . ',' .
            LibAPI\PDOWrapper::cleanse($from_state) . ',' . LibAPI\PDOWrapper::cleanse($to_state));
        return !empty($result[0]['result']);
    }

    private function set_async_op($requestid, $kind, $project_id, $trg = '')
    {
        LibAPI\PDOWrapper::call('set_wordbee_async_op', LibAPI\PDOWrapper::cleanse($requestid) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($kind) . ',' .
            LibAPI\PDOWrapper::cleanse($project_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($trg));
    }

    /*
     * Creates the Wordbee project for a TWB project just created on the project create page (Wordbee equivalent of UserDao::create_memsource_project())
     * Returns the MemsourceProjects row (as create_memsource_project() does) or 0 on failure
     */
    public function create_wordbee_project($post, $project, $file_name, $file, $user_id)
    {
        $projectDao = new ProjectDao();
        $project_id = $project->getId();

        [$kp_language, $kp_country] = $projectDao->convert_selection_to_language_country($post['sourceLanguageSelect']);
        $src = $this->locale($kp_language, $kp_country);
        if (!$src) {
            error_log("create_wordbee_project($project_id) source language not in Wordbee: {$post['sourceLanguageSelect']}");
            return 0;
        }
        $kp_source_language = "{$kp_language}-{$kp_country}";

        $targetCount = 0;
        $trgs = [];
        $kp_target_languages = [];
        while (!empty($post["target_language_$targetCount"])) {
            [$kp_language, $kp_country] = $projectDao->convert_selection_to_language_country($post["target_language_$targetCount"]);
            $trg = $this->locale($kp_language, $kp_country);
            if (!$trg || strcasecmp($trg, $src) == 0 || in_array($trg, $trgs)) {
                error_log("create_wordbee_project($project_id) target language not in Wordbee or repeated: " . $post["target_language_$targetCount"]);
                return 0;
            }
            $trgs[] = $trg;
            $kp_target_languages[] = "{$kp_language}-{$kp_country}";
            $targetCount++;
        }
        if (empty($trgs)) return 0;

        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($extension, $this->get_file_extensions())) {
            error_log("create_wordbee_project($project_id) file type not supported: $file_name");
            return 0;
        }

        // 1. Project (API1 only), a Standard project with Translation + Revision
        $reference = "twb-project-$project_id";
        $revision = !empty($post['wordbee_revision']) ? 1 : 0;
        $params = [
            'projecttype'   => 0,
            'reference'     => $reference,
            'deadline'      => $this->iso8601($project->getDeadline()),
            'sourcelocale'  => $src,
            'targetlocales' => implode(',', $trgs),
            'tasks'         => $revision ? 'TR,RV' : 'TR',
        ];
        if (self::setting('client_company_id') !== '') $params['clientcompanyid'] = self::setting('client_company_id');
        if (self::setting('manager_person_id') !== '') $params['managerpersonid'] = self::setting('manager_person_id');
        [$code, $wb_project_id] = $this->api1('POST', 'projects', $params);
        if ($code != 200 || !is_numeric($wb_project_id) || $wb_project_id <= 0) return 0;
        $wb_project_id = (int)$wb_project_id;
        error_log("create_wordbee_project($project_id) Wordbee project: $wb_project_id");

        if (self::setting('project_workflow_id') !== '') $this->api2('PUT', "projects/$wb_project_id/settings/workflow?id=" . urlencode(self::setting('project_workflow_id')));

        // 2. Same rows as a Phrase self service project, using the Shell convention for ids
        $projectDao->record_memsource_project_languages($project_id, $kp_source_language, implode(',', $kp_target_languages));
        LibAPI\PDOWrapper::call('set_wordbee_project',
            LibAPI\PDOWrapper::cleanse($project_id) . ',' .
            LibAPI\PDOWrapper::cleanse($wb_project_id) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($reference) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($file_name) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr($src) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr(implode(',', $trgs)) . ',' .
            LibAPI\PDOWrapper::cleanse($revision));
        foreach ($trgs as $trg) $this->set_wordbee_language($project_id, $trg, 0);
        $workflow_levels = self::WORKFLOW_LEVELS;
        if (!$revision) $workflow_levels[1] = '';
        $projectDao->set_memsource_project($project_id, $project_id, $project_id, $user_id, $user_id, $workflow_levels);
        $projectDao->update_project_owner_id($project_id, $user_id, 1);
        $projectDao->set_memsource_self_service_project($project_id, 1);

        // 3. Source document (async: segmentation and word count), no jobs yet (TWB asks for them per language)
        $tmp = tempnam(sys_get_temp_dir(), 'wordbee');
        file_put_contents($tmp, $file);
        [$code, $upload] = $this->api2('POST', 'media/upload', ['file' => new \CURLFile($tmp, 'application/octet-stream', $file_name)], true);
        unlink($tmp);
        $media_token = $upload['result']['token'] ?? ($upload['token'] ?? '');
        if ($code != 200 || $media_token === '') return 0;

        [$code, $response] = $this->api2('POST', "projects/$wb_project_id/workflows/new", [
            'files'    => [['token' => $media_token, 'name' => $file_name]],
            'src'      => $src,
            'trgs'     => $trgs,
            'deadline' => $this->iso8601($project->getDeadline()),
            'workflow' => 'None',
            'callback' => $this->callback(),
        ]);
        if ($code != 200 || empty($response['trm'])) return 0;
        if (!empty($response['trm']['requestid'])) $this->set_async_op($response['trm']['requestid'], 'import', $project_id);
        $response = $this->trm_wait($response);
        if ($response['trm']['status'] == 'Failed') {
            error_log("create_wordbee_project($project_id) import failed: " . json_encode($response['trm']));
            return 0;
        }
        if ($response['trm']['status'] == 'Finished') $this->continue_async($response, ['kind' => 'import', 'project_id' => $project_id, 'target_locale' => '']);

        return $projectDao->get_memsource_project($project_id);
    }

    /*
     * Continues after an asynchronous operation (inline wait, Wordbee callback or cron)
     * $op (kind, project_id, target_locale) is only needed when Wordbee finished at once (requestid 0, nothing stored)
     */
    public function continue_async($response, $op = null)
    {
        $trm = $response['trm'];
        if (in_array($trm['status'], ['Waiting', 'Executing'])) return;
        if (!empty($trm['requestid'])) {
            $result = LibAPI\PDOWrapper::call('get_wordbee_async_op', LibAPI\PDOWrapper::cleanse($trm['requestid']));
            if (empty($result)) return;
            $op = $result[0];
            $claim = LibAPI\PDOWrapper::call('claim_wordbee_async_op', LibAPI\PDOWrapper::cleanse($trm['requestid']) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($trm['status']));
            if (empty($claim[0]['result'])) return; // Already handled
        } elseif (empty($op)) {
            return;
        }

        if ($trm['status'] == 'Failed') {
            error_log("Wordbee {$op['kind']} failed for project {$op['project_id']}: " . json_encode($trm));
            if ($op['kind'] == 'jobs') $this->set_wordbee_language($op['project_id'], $op['target_locale'], 0); // Retry from cron
            else LibAPI\PDOWrapper::call('update_wordbee_project_status', LibAPI\PDOWrapper::cleanse($op['project_id']) . ",'failed'");
            return;
        }

        if ($op['kind'] == 'import') {
            $files = $response['custom']['files'] ?? ($response['result']['files'] ?? []);
            if (empty($files[0]['did']) || isset($files[0]['success']) && !$files[0]['success']) {
                error_log("Wordbee import gave no document for project {$op['project_id']}: " . json_encode($response));
                LibAPI\PDOWrapper::call('update_wordbee_project_status', LibAPI\PDOWrapper::cleanse($op['project_id']) . ",'failed'");
                return;
            }
            LibAPI\PDOWrapper::call('update_wordbee_project_document', LibAPI\PDOWrapper::cleanse($op['project_id']) . ',' . LibAPI\PDOWrapper::cleanse($files[0]['did']) . ",'active'");
            foreach ($this->get_wordbee_languages($op['project_id']) as $language) $this->request_jobs($op['project_id'], $language['target_locale']);
        } elseif ($op['kind'] == 'jobs') {
            $this->materialise_language($op['project_id'], $op['target_locale'], $response);
        }
    }

    /*
     * Asks Wordbee for the jobs of one target language: whole document, or segment chunks of about 2000 words
     */
    public function request_jobs($project_id, $trg)
    {
        if (!$this->claim_wordbee_language($project_id, $trg, 0, 1)) return; // Already requested
        $wordbee_project = $this->get_wordbee_project($project_id);
        $pid = $wordbee_project['wb_project_id'];
        $did = $wordbee_project['document_id'];

        [$code, $counts] = $this->api2('GET', "projects/$pid/workflows/$did/wordcounts/" . rawurlencode($trg));
        $words    = (int)($counts['words'] ?? ($counts['counts']['words'] ?? 0));
        $segments = (int)($counts['segments'] ?? ($counts['counts']['segments'] ?? 0));
        if ($code != 200) {
            $this->set_wordbee_language($project_id, $trg, 0);
            return;
        }

        $parts = $words > self::WORDS_PER_PART && $segments > 1 ? (int)ceil($words/self::WORDS_PER_PART) : 1;
        $max_segments = $parts > 1 ? (int)ceil($segments/$parts) : $segments;
        $this->set_wordbee_language($project_id, $trg, 1, $words, $segments, $max_segments);

        $projectDao = new ProjectDao();
        $project = $projectDao->getProject($project_id);
        $data = [
            'mode'          => $parts > 1 ? self::JOBS_MODE_SPLIT : self::JOBS_MODE_WHOLE,
            'documentIds'   => [(int)$did],
            'trgs'          => [$trg],
            'deadline'      => $this->iso8601($project->getDeadline()),
            'startWorkflow' => true,
            'callback'      => $this->callback(),
        ];
        if (self::setting('workflow_profile_id') !== '') $data['workflowProfileId'] = (int)self::setting('workflow_profile_id');
        if ($parts > 1) {
            $data['segments'] = ['maxSegments' => $max_segments];
            $data['filterOption'] = 'Unassigned';
        }
        [$code, $response] = $this->api2('POST', "projects/$pid/workflows/jobs/new", $data);
        if ($code != 200 || empty($response['trm'])) {
            $this->set_wordbee_language($project_id, $trg, 0, $words, $segments, $max_segments); // Retry from cron
            return;
        }
        if (!empty($response['trm']['requestid'])) $this->set_async_op($response['trm']['requestid'], 'jobs', $project_id, $trg);
        $response = $this->trm_wait($response);
        $this->continue_async($response, ['kind' => 'jobs', 'project_id' => $project_id, 'target_locale' => $trg]);
    }

    /*
     * Wordbee equivalent of the Phrase JOB_CREATED hook (ProjectRouteHandler::create_task()) for one target language
     */
    public function materialise_language($project_id, $trg, $response)
    {
        $result = $response['custom'] ?? ($response['result'] ?? []);
        $job_ids = [];
        if (!empty($result['jobsByLanguage'])) {
            foreach ($result['jobsByLanguage'] as $key => $value) {
                // {"fr-FR": [1, 2]} or [{"language": "fr-FR", "jobIds": [1, 2]}]
                if (is_array($value) && isset($value['jobIds'])) { if (strcasecmp($value['language'] ?? $trg, $trg) == 0) $job_ids = array_merge($job_ids, $value['jobIds']); }
                elseif (strcasecmp($key, $trg) == 0) $job_ids = array_merge($job_ids, (array)$value);
            }
        }
        if (empty($job_ids) && !empty($result['jobIds'])) $job_ids = $result['jobIds'];
        if (empty($job_ids)) {
            error_log("Wordbee gave no jobs for project $project_id $trg: " . json_encode($response));
            return;
        }

        $languages = [];
        foreach ($this->get_wordbee_languages($project_id) as $language) $languages[$language['target_locale']] = $language;
        $language = $languages[$trg];
        $max_segments = max(1, (int)$language['max_segments']);

        // Jobs in creation order: chunk k has a TR (and RV) job
        sort($job_ids);
        $jobs = [];
        $part_by_code = [];
        foreach ($job_ids as $job_id) {
            [$code, $summary] = $this->api2('GET', "jobs/$job_id/summary");
            if ($code != 200) continue;
            $task_code = strtoupper($summary['task'] ?? '');
            if (empty(self::TASK_CODE_TO_WORKFLOW_LEVEL[$task_code])) {
                error_log("Wordbee job $job_id has task $task_code, not TR or RV, ignored");
                continue;
            }
            $part = $part_by_code[$task_code] = ($part_by_code[$task_code] ?? 0) + 1;
            $seg_from = ($part - 1)*$max_segments;
            $seg_to   = min($part*$max_segments, max(1, (int)$language['segments'])) - 1;
            LibAPI\PDOWrapper::call('set_wordbee_task',
                LibAPI\PDOWrapper::cleanseWrapStr((string)$job_id) . ',' . LibAPI\PDOWrapper::cleanse($project_id) . ',' .
                LibAPI\PDOWrapper::cleanseWrapStr($task_code) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($trg) . ',' .
                LibAPI\PDOWrapper::cleanse($part) . ',' . LibAPI\PDOWrapper::cleanse($seg_from) . ',' . LibAPI\PDOWrapper::cleanse(max($seg_from, $seg_to)) . ',' .
                LibAPI\PDOWrapper::cleanse((int)($summary['status'] ?? 0)));
            $jobs[] = ['id' => (string)$job_id, 'code' => $task_code, 'part' => $part, 'seg_from' => $seg_from, 'seg_to' => max($seg_from, $seg_to)];
        }
        $parts = max(1, $part_by_code['TR'] ?? 1);

        $projectDao = new ProjectDao();
        $wordbee_project = $this->get_wordbee_project($project_id);
        $project_languages = $projectDao->get_memsource_project_languages($project_id);
        $kp_target_language_pairs = explode(',', $project_languages['kp_target_language_pairs']);
        $trgs = explode(',', $wordbee_project['target_locales']);
        $index = array_search($trg, $trgs);

        foreach ($jobs as $job) {
            $claim = LibAPI\PDOWrapper::call('claim_wordbee_task_for_materialising', LibAPI\PDOWrapper::cleanseWrapStr($job['id']));
            if (empty($claim[0]['result'])) continue;

            $segments_in_part = $job['seg_to'] - $job['seg_from'] + 1;
            $words = $parts > 1 && $language['segments'] ? (int)round($language['words']*$segments_in_part/$language['segments']) : (int)$language['words']; // Wordbee gives counts per language, estimate per chunk

            $task_id = TmsDao::create_twb_task($project_id, [
                'ref'           => "Wordbee job {$job['id']}",
                'workflowLevel' => self::TASK_CODE_TO_WORKFLOW_LEVEL[$job['code']],
                'internalId'    => ($index + 1) . ($parts > 1 ? ".{$job['part']}" : ''),
                'kp_target'     => $kp_target_language_pairs[$index],
                'words'         => $words,
                'begin'         => $job['seg_from'],
                'end'           => $job['seg_to'],
                'round'         => 1,
                'file_name'     => $wordbee_project['file_name'],
            ], function($task_id) use ($job) {
                LibAPI\PDOWrapper::call('link_wordbee_task', LibAPI\PDOWrapper::cleanseWrapStr($job['id']) . ',' . LibAPI\PDOWrapper::cleanse($task_id));
            });
            if (!$task_id) {
                LibAPI\PDOWrapper::call('unclaim_wordbee_task_for_materialising', LibAPI\PDOWrapper::cleanseWrapStr($job['id']));
                continue;
            }
            // TWB computes the step deadlines (as for Phrase self service), give them to Wordbee
            $taskDao = new TaskDao();
            $this->api2('POST', "jobs/{$job['id']}/properties", ['deadline' => $this->iso8601($taskDao->getTask($task_id)->getDeadline())]);
        }
        $this->set_wordbee_language($project_id, $trg, 2, $language['words'], $language['segments'], $language['max_segments']);
    }

    /*
     * The Wordbee person for a TWB user, created (no password, linguists sign in with TWB SAML) on first claim
     */
    public function ensure_person($user_id)
    {
        $result = LibAPI\PDOWrapper::call('get_wordbee_user', LibAPI\PDOWrapper::cleanse($user_id));
        if (!empty($result)) return $result[0];

        $userDao = new UserDao();
        $user = $userDao->getUser($user_id);
        $info = $userDao->getUserPersonalInformation($user_id);
        $company_id = (int)self::setting('supplier_company_id');
        $person = [
            'action' => 'ChangeOrCreate',
            'cid'    => $company_id,
            'email'  => $user->getEmail(),
            'login'  => "twb_$user_id",
            'fname'  => $info->getFirstName() ?: $user->getDisplayName(),
            'lname'  => $info->getLastName() ?: '-',
            'active' => true,
        ];
        if (self::setting('person_profile_id') !== '') $person['profile'] = (int)self::setting('person_profile_id');
        [$code, $response] = $this->api2('POST', 'persons/io/update', $person);
        $response = $this->trm_wait(is_array($response) ? $response : []);
        $person_id = $response['id'] ?? ($response['uid'] ?? ($response['custom']['id'] ?? ($response['custom']['uid'] ?? ($response['result']['id'] ?? 0))));
        if ($code != 200 || !$person_id) {
            error_log("Wordbee person not created for $user_id: " . json_encode($response));
            return 0;
        }
        LibAPI\PDOWrapper::call('set_wordbee_user', LibAPI\PDOWrapper::cleanse($user_id) . ',' . LibAPI\PDOWrapper::cleanse($person_id) . ',' . LibAPI\PDOWrapper::cleanse($company_id));
        error_log("Wordbee person $person_id created for $user_id");
        return ['user_id' => $user_id, 'wb_person_id' => $person_id, 'wb_company_id' => $company_id];
    }

    public function assign($task_id, $user_id, $deadline)
    {
        $wordbee_task = $this->get_wordbee_task_by_task_id($task_id);
        if (empty($wordbee_task) || !($person = $this->ensure_person($user_id))) return 0;
        [$code] = $this->api2('POST', "jobs/{$wordbee_task['wb_job_id']}/supplier", ['personid' => (int)$person['wb_person_id'], 'companyid' => (int)$person['wb_company_id'], 'sendproposal' => false]);
        if ($code != 200) return 0;
        LibAPI\PDOWrapper::call('update_wordbee_task_status', LibAPI\PDOWrapper::cleanseWrapStr($wordbee_task['wb_job_id']) . ',' . LibAPI\PDOWrapper::cleanse($wordbee_task['status']) . ',' . LibAPI\PDOWrapper::cleanse($person['wb_person_id']));
        $this->set_task_deadline($task_id, $deadline);
        return 1;
    }

    public function unassign($task_id)
    {
        $wordbee_task = $this->get_wordbee_task_by_task_id($task_id);
        if (empty($wordbee_task)) return 0;
        [$code] = $this->api2('POST', "jobs/{$wordbee_task['wb_job_id']}/supplier", (object)[]);
        LibAPI\PDOWrapper::call('update_wordbee_task_status', LibAPI\PDOWrapper::cleanseWrapStr($wordbee_task['wb_job_id']) . ',' . LibAPI\PDOWrapper::cleanse($wordbee_task['status']) . ',0');
        return $code == 200;
    }

    public function set_task_deadline($task_id, $deadline)
    {
        $wordbee_task = $this->get_wordbee_task_by_task_id($task_id);
        if (empty($wordbee_task)) return;
        $this->api2('POST', "jobs/{$wordbee_task['wb_job_id']}/properties", ['deadline' => $this->iso8601($deadline)]);
    }

    public function set_project_deadline($project_id, $deadline)
    {
        $wordbee_project = $this->get_wordbee_project($project_id);
        if (empty($wordbee_project)) return;
        $this->api1('PUT', "projects/{$wordbee_project['wb_project_id']}", ['deadline' => $this->iso8601($deadline)]);
    }

    public function set_cancelled($task_id, $cancelled)
    {
        $wordbee_task = $this->get_wordbee_task_by_task_id($task_id);
        if (empty($wordbee_task)) return;
        // Uncancel puts the job back to Not started (TWB status follows from the claim)
        $this->api2('POST', "jobs/{$wordbee_task['wb_job_id']}/properties", ['status' => $cancelled ? self::JOB_CANCELLED : 4]);
    }

    public function get_editor_url($task_id)
    {
        $wordbee_task = $this->get_wordbee_task_by_task_id($task_id);
        if (empty($wordbee_task)) return '';
        $url = 'https://' . self::setting('editor_domain') . '/ux/editor/go?s=jbs' . rawurlencode($wordbee_task['wb_job_id']);
        if (self::setting('saml_sp_entity_id') === '') return $url;
        return Common\Lib\Settings::get('site.location') . 'simplesaml/saml2/idp/SSOService.php?spentityid=' . rawurlencode(self::setting('saml_sp_entity_id')) . '&RelayState=' . rawurlencode($url);
    }

    public function get_target_file($task_id)
    {
        $wordbee_task = $this->get_wordbee_task_by_task_id($task_id);
        if (empty($wordbee_task)) return 0;
        $wordbee_project = $this->get_wordbee_project($wordbee_task['project_id']);
        [$code, $file] = $this->api2('GET', "projects/{$wordbee_project['wb_project_id']}/workflows/{$wordbee_project['document_id']}/files/" . rawurlencode($wordbee_task['target_locale']) . '/file', null, false, true);
        if ($code != 200 || strlen($file) > 100000000) return 0;
        return $file;
    }

    private function store_metrics($task_id, $wordbee_task)
    {
        $wordbee_project = $this->get_wordbee_project($wordbee_task['project_id']);
        [$code, $c] = $this->api2('GET', "projects/{$wordbee_project['wb_project_id']}/workflows/{$wordbee_project['document_id']}/wordcounts/" . rawurlencode($wordbee_task['target_locale']));
        if ($code != 200 || !is_array($c)) return;
        $c = $c['counts'] ?? $c;
        $n = function($name) use ($c) { return (int)($c[$name] ?? 0); };
        LibAPI\PDOWrapper::call('set_wordbee_task_metrics',
            LibAPI\PDOWrapper::cleanse($task_id) . ',' . LibAPI\PDOWrapper::cleanseWrapStr($wordbee_task['wb_job_id']) . ',' .
            implode(',', array_map(function($v) { return LibAPI\PDOWrapper::cleanse($v); }, [$n('words'), $n('segments'), $n('wd110'), $n('wd100'), $n('wdMatch1'), $n('wdMatch2'), $n('wdMatch3'), $n('wdMatch4'), $n('wdMatch5'), $n('wdPretransIdenticalMT')])) . ',' .
            LibAPI\PDOWrapper::cleanseWrapStr(json_encode($c)));
    }

    /*
     * Catches TWB up with Wordbee: missing jobs, job status and assignee changes (from webhooks, the Sync button and cron)
     */
    public function sync_project($project_id)
    {
        $wordbee_project = $this->get_wordbee_project($project_id);
        if (empty($wordbee_project)) return 'Not a Wordbee project';
        LibAPI\PDOWrapper::call('set_wordbee_project_polled', LibAPI\PDOWrapper::cleanse($project_id));
        if (!$wordbee_project['document_id']) return 'The Wordbee document is still being imported';

        foreach ($this->get_wordbee_languages($project_id) as $language) {
            if ($language['state'] == 0) $this->request_jobs($project_id, $language['target_locale']);
        }

        $taskDao = new TaskDao();
        $projectDao = new ProjectDao();
        $all_done = true;
        foreach ($this->get_wordbee_tasks_for_project($project_id) as $wordbee_task) {
            if (in_array($wordbee_task['status'], [self::JOB_COMPLETED, self::JOB_DELIVERED, self::JOB_ARCHIVED, self::JOB_CANCELLED])) continue;
            $all_done = false;
            [$code, $summary] = $this->api2('GET', "jobs/{$wordbee_task['wb_job_id']}/summary");
            if ($code != 200 || !isset($summary['status'])) continue;
            $status = (int)$summary['status'];
            $person = (int)($summary['assignment']['uid'] ?? 0);
            LibAPI\PDOWrapper::call('update_wordbee_task_status', LibAPI\PDOWrapper::cleanseWrapStr($wordbee_task['wb_job_id']) . ',' . LibAPI\PDOWrapper::cleanse($status) . ',' . LibAPI\PDOWrapper::cleanse($person));
            if (!($task_id = $wordbee_task['task_id'])) continue;

            if (in_array($status, [self::JOB_COMPLETED, self::JOB_DELIVERED, self::JOB_ARCHIVED])) {
                TmsDao::complete_task($task_id);
                $this->store_metrics($task_id, $wordbee_task);
            } elseif ($status == self::JOB_IN_PROGRESS) {
                TmsDao::reopen_task($task_id);
            } elseif ($status == self::JOB_CANCELLED) {
                $userDao = new UserDao();
                $userDao->propagate_cancelled(1, $projectDao->get_memsource_project($project_id), $task_id, 'Cancelled in Wordbee', 0, 1);
            } elseif (!$person && $wordbee_task['assignee'] && ($user_id = $projectDao->getUserClaimedTask($task_id)) && $taskDao->getTaskStatus($task_id) != Common\Enums\TaskStatusEnum::COMPLETE) {
                // Supplier removed in Wordbee
                $taskDao->unClaimTask($task_id, $user_id);
                error_log("Wordbee job {$wordbee_task['wb_job_id']} unassigned in Wordbee, unclaimed task_id: $task_id");
            }
        }
        $pending = array_filter($this->get_wordbee_languages($project_id), function($l) { return $l['state'] != 2; });
        if ($all_done && empty($pending) && $this->get_wordbee_tasks_for_project($project_id)) LibAPI\PDOWrapper::call('update_wordbee_project_status', LibAPI\PDOWrapper::cleanse($project_id) . ",'completed'");
        return '';
    }

    /*
     * Webhook (configured in the Wordbee UI) or async callback, already checked for the shared secret
     */
    public function handle_event($type, $event)
    {
        if ($type == 'trm') {
            if (!empty($event['trm'])) $this->continue_async($event);
            return;
        }
        $wb_project_id = $event['projectId'] ?? ($event['projectid'] ?? 0);
        if (!$wb_project_id || !($wordbee_project = $this->get_wordbee_project_by_wb_id($wb_project_id))) {
            error_log('Wordbee webhook for unknown project: ' . json_encode($event));
            return;
        }
        $this->sync_project($wordbee_project['project_id']);
    }

    public static function verify_secret($header)
    {
        $secret = self::setting('webhook_secret');
        return $secret !== '' && hash_equals($secret, (string)$header);
    }

    /*
     * Cron fallback: finish async operations whose callback was missed, poll open projects (every POLL_MINUTES, the API allows 1000 calls an hour)
     */
    public function poll_jobs()
    {
        if (!self::enabled()) return;
        $ops = LibAPI\PDOWrapper::call('get_wordbee_async_ops_pending', '');
        if (!empty($ops)) {
            foreach ($ops as $op) {
                [$code, $status] = $this->api2('GET', "trm/status?requestid={$op['requestid']}");
                if ($code == 200 && !empty($status['trm'])) $this->continue_async($status);
            }
        }
        $projects = LibAPI\PDOWrapper::call('get_wordbee_projects_to_poll', LibAPI\PDOWrapper::cleanse(self::POLL_MINUTES));
        if (!empty($projects)) foreach ($projects as $wordbee_project) $this->sync_project($wordbee_project['project_id']);
    }
}
