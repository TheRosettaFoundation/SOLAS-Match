<?php

namespace SolasMatch\Common\Lib;

require_once __DIR__ . '/Settings.class.php';

/**
 * Thin client for the Katong review service API (see https://katong.bredenkamp.ch/docs).
 *
 * Used by both the UI and the API layers, so it only depends on Settings and curl.
 * Configuration is in the [katong] section of conf.ini.
 *
 * Workflow levels for Katong projects are fixed:
 *   1 => Translation (Katong task kind "mt", done by machine translation, claimed by katong.default_user_id)
 *   2 => Revision    (Katong task kind "review")
 *   3 => SME Review  (Katong task kind "sme_review")
 */
class Katong
{
    const TMS_PHRASE = 0;
    const TMS_KATONG = 1;

    const WORKFLOW_LEVELS = ['Translation', 'Revision', 'SME Review', '', '', '', '', '', '', '', '', ''];

    const KIND_TO_LEVEL = ['mt' => 1, 'translate' => 1, 'review' => 2, 'sme_review' => 3];

    const LEVEL_TO_ROLE = [1 => 'translator', 2 => 'reviewer', 3 => 'sme'];

    // Katong accepts ISO 639-1 codes; map the few 3 letter codes TWB/Phrase may use
    const LANGUAGE_MAP = ['swh' => 'sw', 'swa' => 'sw', 'arb' => 'ar', 'ara' => 'ar', 'ben' => 'bn', 'fra' => 'fr', 'fre' => 'fr', 'spa' => 'es', 'por' => 'pt', 'eng' => 'en'];

    /**
     * Settings::get() throws if a key is missing; a deployment without a [katong] section must keep working for Phrase
     */
    public static function cfg($key, $default = '')
    {
        try {
            return Settings::get("katong.$key");
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function configured()
    {
        return self::cfg('api_url') !== '' && self::cfg('api_key') !== '';
    }

    public static function api_url()
    {
        return rtrim(self::cfg('api_url'), '/') . '/';
    }

    public static function base_url()
    {
        return rtrim(self::cfg('base_url'), '/') . '/';
    }

    public static function default_user_id()
    {
        return (int)self::cfg('default_user_id', 0);
    }

    public static function default_user_name()
    {
        $name = self::cfg('default_user_name');
        return empty($name) ? 'Katong MT' : $name;
    }

    public static function work_url($katong_task_uid)
    {
        return self::base_url() . "work/$katong_task_uid";
    }

    public static function level_for_kind($kind)
    {
        return empty(self::KIND_TO_LEVEL[$kind]) ? 0 : self::KIND_TO_LEVEL[$kind];
    }

    public static function role_for_level($level)
    {
        return empty(self::LEVEL_TO_ROLE[$level]) ? 'reviewer' : self::LEVEL_TO_ROLE[$level];
    }

    /**
     * Convert a Phrase/selections "memsource" code (e.g. "fr_fr", "sw", "es_419") to the code sent to Katong.
     */
    public static function memsource_to_katong_lang($memsource_code)
    {
        $code = strtolower($memsource_code);
        $pos = strpos($code, '_');
        if ($pos !== false) $code = substr($code, 0, $pos);
        if (!empty(self::LANGUAGE_MAP[$code])) $code = self::LANGUAGE_MAP[$code];
        return $code;
    }

    /**
     * Normalise a language code returned by Katong (it may return a locale such as "es-419") to the code TWB sent.
     */
    public static function normalise_lang($lang)
    {
        $lang = strtolower(str_replace('_', '-', $lang));
        $pos = strpos($lang, '-');
        if ($pos !== false) $lang = substr($lang, 0, $pos);
        if (!empty(self::LANGUAGE_MAP[$lang])) $lang = self::LANGUAGE_MAP[$lang];
        return $lang;
    }

    /**
     * Katong wants ISO 8601 with a zone; TWB stores 'Y-m-d H:i:s' in UTC.
     */
    public static function iso_date($twb_date)
    {
        if (empty($twb_date)) return null;
        return substr($twb_date, 0, 10) . 'T' . substr($twb_date, 11, 8) . 'Z';
    }

    /**
     * Convert a Katong ISO 8601 date to TWB 'Y-m-d H:i:s' UTC.
     */
    public static function twb_date($iso_date)
    {
        if (empty($iso_date)) return null;
        $t = strtotime($iso_date);
        if ($t === false) return null;
        return gmdate('Y-m-d H:i:s', $t);
    }

    /**
     * Make a request. Returns [http_code, decoded_body_or_raw, headers].
     * $body: array (sent as JSON), or ['__multipart' => [...]] for file upload, or null.
     */
    public static function request($method, $path, $body = null, $raw = false, $timeout = 60)
    {
        $url = self::api_url() . ltrim($path, '/');
        $ch = curl_init($url);
        $headers = ['Authorization: Bearer ' . self::cfg('api_key'), 'Accept: application/json, application/problem+json, */*'];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        if ($body !== null) {
            if (isset($body['__multipart'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body['__multipart']);
            } else {
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response_headers = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$response_headers) {
            $len = strlen($header);
            $parts = explode(':', $header, 2);
            if (count($parts) == 2) $response_headers[strtolower(trim($parts[0]))] = trim($parts[1]);
            return $len;
        });

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($error_number = curl_errno($ch)) {
            error_log("Katong $method $url Curl error ($error_number): " . curl_error($ch));
            $code = 0;
        }
        curl_close($ch);

        if ($code < 200 || $code > 299) {
            error_log("Katong $method $url responseCode: $code " . (is_string($result) ? mb_substr($result, 0, 2000) : ''));
        }
        if ($raw) return [$code, $result, $response_headers];

        $decoded = json_decode($result, true);
        return [$code, $decoded === null ? $result : $decoded, $response_headers];
    }

    public static function upload_file($filename, $bytes, $purpose = null)
    {
        $tmp = tempnam(sys_get_temp_dir(), 'katong');
        file_put_contents($tmp, $bytes);
        $file = new \CURLFile($tmp, 'application/octet-stream', $filename);
        [$code, $result] = self::request('POST', 'files' . ($purpose ? "?purpose=$purpose" : ''), ['__multipart' => ['file' => $file]], false, 300);
        unlink($tmp);
        if ($code != 201 && $code != 200) return 0;
        return $result;
    }

    public static function create_job($data)
    {
        [$code, $result] = self::request('POST', 'jobs', $data, false, 300);
        if ($code != 201 && $code != 200) return 0;
        return $result;
    }

    public static function get_job($job_uid)
    {
        [$code, $result] = self::request('GET', "jobs/$job_uid");
        if ($code != 200) return 0;
        return $result;
    }

    public static function get_job_by_external_ref($external_ref)
    {
        [$code, $result] = self::request('GET', 'jobs?external_ref=' . rawurlencode($external_ref));
        if ($code != 200) return 0;
        if (isset($result['id'])) return $result;
        if (isset($result[0]['id'])) return $result[0];
        return 0;
    }

    /**
     * Assign (or reassign) a task. Katong has no unassign, so to "unassign" assign to the default user.
     */
    public static function assign_task($task_uid, $assignee_id, $display_name, $role, $languages = null, $deadline = null)
    {
        $data = [
            'assignee_id'  => (string)$assignee_id,
            'display_name' => mb_substr(empty($display_name) ? "User $assignee_id" : $display_name, 0, 200),
            'role'         => $role,
        ];
        if (!empty($languages)) $data['languages'] = array_values($languages);
        if (!empty($deadline))  $data['deadline']  = self::iso_date($deadline);
        [$code, $result] = self::request('POST', "tasks/$task_uid/assign", $data);
        if ($code != 200) return 0;
        return $result;
    }

    public static function unassign_task($task_uid, $role, $languages = null, $deadline = null)
    {
        return self::assign_task($task_uid, self::default_user_id(), self::default_user_name(), $role, $languages, $deadline);
    }

    public static function cancel_task($task_uid, $reason = null)
    {
        [$code, $result] = self::request('POST', "tasks/$task_uid/cancel", ['reason' => $reason ? mb_substr($reason, 0, 2000) : null]);
        return $code == 200 ? $result : 0;
    }

    public static function cancel_job($job_uid, $reason = null)
    {
        [$code, $result] = self::request('POST', "jobs/$job_uid/cancel", ['reason' => $reason ? mb_substr($reason, 0, 2000) : null]);
        return $code == 200 ? $result : 0;
    }

    public static function get_task_metrics($task_uid)
    {
        [$code, $result] = self::request('GET', "tasks/$task_uid/metrics");
        if ($code != 200) return 0;
        return $result;
    }

    /**
     * Returns ['body' => bytes, 'content_type' => ..., 'untranslated' => int] or 0
     */
    public static function get_deliverable($job_uid, $lang, $format = null)
    {
        [$code, $result, $headers] = self::request('GET', "jobs/$job_uid/deliverables/" . rawurlencode($lang) . ($format ? "?format=$format" : ''), null, true, 300);
        if ($code != 200) return 0;
        return [
            'body'         => $result,
            'content_type' => empty($headers['content-type']) ? 'application/octet-stream' : $headers['content-type'],
            'untranslated' => isset($headers['x-untranslated-segments']) ? (int)$headers['x-untranslated-segments'] : 0,
        ];
    }

    public static function register_webhook($url, $events = ['task.status_changed', 'job.completed', 'job.reopened', 'job.cancelled', 'segment.flagged'])
    {
        [$code, $result] = self::request('POST', 'webhooks', ['url' => $url, 'events' => $events]);
        if ($code != 201 && $code != 200) return 0;
        return $result;
    }

    public static function list_webhooks()
    {
        [$code, $result] = self::request('GET', 'webhooks');
        return $code == 200 ? $result : [];
    }

    /**
     * Verify X-Katong-Signature: "t=<unix seconds>,v1=<hex>", v1 = HMAC-SHA256(secret, "<t>." . raw body)
     */
    public static function verify_signature($secret, $header, $raw_body, $tolerance = 300, $now = null)
    {
        if (empty($secret) || empty($header)) return false;
        $parts = [];
        foreach (explode(',', $header) as $p) {
            $kv = explode('=', trim($p), 2);
            if (count($kv) == 2) $parts[$kv[0]] = $kv[1];
        }
        if (empty($parts['t']) || empty($parts['v1']) || !ctype_digit($parts['t'])) return false;
        if ($now === null) $now = time();
        if (abs($now - (int)$parts['t']) > $tolerance) return false;
        $expected = hash_hmac('sha256', $parts['t'] . '.' . $raw_body, $secret);
        return hash_equals($expected, $parts['v1']);
    }

    /**
     * Filename for the deliverable: original filename, but with the deliverable's extension if it differs (e.g. PDF => DOCX)
     */
    public static function deliverable_filename($original_filename, $deliverable_format)
    {
        if (empty($deliverable_format)) return $original_filename;
        $deliverable_format = strtolower($deliverable_format);
        $pos = strrpos($original_filename, '.');
        if ($pos === false) return "$original_filename.$deliverable_format";
        $ext = strtolower(substr($original_filename, $pos + 1));
        if ($ext === $deliverable_format) return $original_filename;
        return substr($original_filename, 0, $pos + 1) . $deliverable_format;
    }
}
