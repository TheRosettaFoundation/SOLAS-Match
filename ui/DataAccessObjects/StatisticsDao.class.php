<?php

namespace SolasMatch\UI\DAO;

use \SolasMatch\Common as Common;
use \SolasMatch\API\Lib as LibAPI;

require_once __DIR__."/../../Common/lib/APIHelper.class.php";
require_once __DIR__."/BaseDao.php";
require_once __DIR__."/../../api/lib/PDOWrapper.class.php";

class StatisticsDao extends BaseDao
{
    public function __construct()
    {
        $this->client = new Common\Lib\APIHelper(Common\Lib\Settings::get("ui.api_format"));
        $this->siteApi = Common\Lib\Settings::get("site.api");
    }

    public function getStats()
    {
        $stats = Common\Lib\CacheHelper::getCached(
            Common\Lib\CacheHelper::STATISTICS,
            Common\Enums\TimeToLiveEnum::HOUR,
            function ($args) {
                $request = "{$args[1]}v0/stats";
                return $args[0]->call(array("\SolasMatch\Common\Protobufs\Models\Statistic"), $request);
            },
            array($this->client, $this->siteApi)
        );
        return $stats;
    }
    
    public function getStat($stat)
    {
        $ret = null;
        $request = "{$this->siteApi}v0/stats/$stat";
        $ret = $this->client->call("\SolasMatch\Common\Protobufs\Models\Statistic", $request);
        return $ret;
    }

    public function getUsers()
    {
        $result = LibAPI\PDOWrapper::call('getUsers', '');
        return $result;
    }

    public function active_now()
    {
        $result = LibAPI\PDOWrapper::call('active_now', '');
        return $result;
    }

    public function active_now_matecat()
    {
        $result = LibAPI\PDOWrapper::call('active_now_matecat', '');

        foreach ($result as $index => $user_row) {
           $stats = $this->get_matecat_task_stats($user_row['task_id'], $user_row['task_type'], $user_row['project_id'], $user_row['matecat_langpair_or_blank'], $user_row['matecat_id_job_or_zero'], $user_row['matecat_id_job_password_or_blank']);

           $result[$index]['DOWNLOAD_STATUS'] = '';
           $result[$index]['TRANSLATED_PERC_FORMATTED'] = '';
           $result[$index]['APPROVED_PERC_FORMATTED'] = '';
           $result[$index]['matecat_url'] = '';

            if (!empty($stats['DOWNLOAD_STATUS']))           $result[$index]['DOWNLOAD_STATUS']           = $stats['DOWNLOAD_STATUS'];
            if (!empty($stats['TRANSLATED_PERC_FORMATTED'])) $result[$index]['TRANSLATED_PERC_FORMATTED'] = $stats['TRANSLATED_PERC_FORMATTED'] . '%';
            if (!empty($stats['APPROVED_PERC_FORMATTED']))   $result[$index]['APPROVED_PERC_FORMATTED']   = $stats['APPROVED_PERC_FORMATTED'] . '%';
            if (!empty($stats['matecat_url']))               $result[$index]['matecat_url']               = $stats['matecat_url'];
        }
        return $result;
    }

    public function late_matecat()
    {
        $result = LibAPI\PDOWrapper::call('late_matecat', '');

        foreach ($result as $index => $user_row) {
           $stats = $this->get_matecat_task_stats($user_row['task_id'], $user_row['task_type'], $user_row['project_id'], $user_row['matecat_langpair_or_blank'], $user_row['matecat_id_job_or_zero'], $user_row['matecat_id_job_password_or_blank']);

           $result[$index]['DOWNLOAD_STATUS'] = '';
           $result[$index]['TRANSLATED_PERC_FORMATTED'] = '';
           $result[$index]['APPROVED_PERC_FORMATTED'] = '';
           $result[$index]['matecat_url'] = '';

            if (!empty($stats['DOWNLOAD_STATUS']))           $result[$index]['DOWNLOAD_STATUS']           = $stats['DOWNLOAD_STATUS'];
            if (!empty($stats['TRANSLATED_PERC_FORMATTED'])) $result[$index]['TRANSLATED_PERC_FORMATTED'] = $stats['TRANSLATED_PERC_FORMATTED'] . '%';
            if (!empty($stats['APPROVED_PERC_FORMATTED']))   $result[$index]['APPROVED_PERC_FORMATTED']   = $stats['APPROVED_PERC_FORMATTED'] . '%';
            if (!empty($stats['matecat_url']))               $result[$index]['matecat_url']               = $stats['matecat_url'];
        }
        return $result;
    }

    public function complete_matecat()
    {
        $result = LibAPI\PDOWrapper::call('complete_matecat', '');

        foreach ($result as $index => $user_row) {
           $stats = $this->get_matecat_task_stats($user_row['task_id'], $user_row['task_type'], $user_row['project_id'], $user_row['matecat_langpair_or_blank'], $user_row['matecat_id_job_or_zero'], $user_row['matecat_id_job_password_or_blank']);

           $result[$index]['DOWNLOAD_STATUS'] = '';
           $result[$index]['TRANSLATED_PERC_FORMATTED'] = '';
           $result[$index]['APPROVED_PERC_FORMATTED'] = '';
           $result[$index]['matecat_url'] = '';

            if (!empty($stats['DOWNLOAD_STATUS']))           $result[$index]['DOWNLOAD_STATUS']           = $stats['DOWNLOAD_STATUS'];
            if (!empty($stats['TRANSLATED_PERC_FORMATTED'])) $result[$index]['TRANSLATED_PERC_FORMATTED'] = $stats['TRANSLATED_PERC_FORMATTED'] . '%';
            if (!empty($stats['APPROVED_PERC_FORMATTED']))   $result[$index]['APPROVED_PERC_FORMATTED']   = $stats['APPROVED_PERC_FORMATTED'] . '%';
            if (!empty($stats['matecat_url']))               $result[$index]['matecat_url']               = $stats['matecat_url'];
        }
        return $result;
    }

    public function get_matecat_task_stats($task_id, $task_type, $project_id, $matecat_langpair, $matecat_id_job, $matecat_id_job_password)
    {
        $taskDao = new DAO\TaskDao();
        $stats = array();
        $we_are_a_subchunk = false;
        if ($task_type == Common\Enums\TaskTypeEnum::TRANSLATION || $task_type == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $translate = 'translate';
            if ($task_type == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';

            if (empty($matecat_id_job)) {
                // Might be a chunk...
                $matecat_tasks = $taskDao->getTaskChunk($task_id);
                if (!empty($matecat_tasks)) {
                    $we_are_a_subchunk = true;
                    $matecat_langpair        = $matecat_tasks[0]['matecat_langpair'];
                    $matecat_id_job          = $matecat_tasks[0]['matecat_id_job'];
                    $matecat_id_job_password = $matecat_tasks[0]['matecat_id_chunk_password'];
                }
            }

            if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                  if (!$we_are_a_subchunk && $taskDao->getTaskSubChunks($matecat_id_job)) {
                      // This has been chunked, so need to accumulate status of all chunks
                      $chunks = $taskDao->getStatusOfSubChunks($project_id, $matecat_langpair, $matecat_id_job, $matecat_id_job_password);
                      $translated_status = true;
                      $approved_status   = true;
                      foreach ($chunks as $index => $chunk) {
                          if ($chunk['DOWNLOAD_STATUS'] === 'draft') $translated_status = false;
                          if ($chunk['DOWNLOAD_STATUS'] === 'draft' || $chunk['DOWNLOAD_STATUS'] === 'translated') $approved_status = false;
                      }
                      if     ($approved_status)   $stats['DOWNLOAD_STATUS'] = 'approved (Split Job)';
                      elseif ($translated_status) $stats['DOWNLOAD_STATUS'] = 'translated (Split Job)';
                      else                        $stats['DOWNLOAD_STATUS'] = 'draft (Split Job)';
                  } else {
                // https://www.matecat.com/api/docs#!/Project/get_v1_jobs_id_job_password_stats
                $re = curl_init("https://tm.translatorswb.org/api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats");

                curl_setopt($re, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($re, CURLOPT_COOKIESESSION, true);
                curl_setopt($re, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($re, CURLOPT_AUTOREFERER, true);

                $httpHeaders = array(
                    'Expect:'
                );
                curl_setopt($re, CURLOPT_HTTPHEADER, $httpHeaders);

                curl_setopt($re, CURLOPT_HEADER, true);
                curl_setopt($re, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($re, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($re, CURLOPT_RETURNTRANSFER, true);
                $res = curl_exec($re);
                $header_size = curl_getinfo($re, CURLINFO_HEADER_SIZE);
                $header = substr($res, 0, $header_size);
                $res = substr($res, $header_size);
                $responseCode = curl_getinfo($re, CURLINFO_HTTP_CODE);

                curl_close($re);

                if ($responseCode == 200) {
                    $response_data = json_decode($res, true);

                    if (!empty($response_data['stats'])) {
                        $stats = $response_data['stats'];
                        $stats['matecat_url'] = "https://tm.translatorswb.org/$translate/proj-" . $project_id . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password";
                    } else {
                        error_log("https://tm.translatorswb.org/api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats get_matecat_task_stats($task_id...) stats empty!");
                    }
                } else {
                    error_log("https://tm.translatorswb.org/api/v1/jobs/$matecat_id_job/$matecat_id_job_password/stats get_matecat_task_stats($task_id...) responseCode: $responseCode");
                }
                  }
            }
        }
        return $stats;
    }

    public function get_matecat_task_urls($task_id, $task_type, $project_id, $matecat_langpair, $matecat_id_job, $matecat_id_job_password)
    {
        $taskDao = new DAO\TaskDao();
        $stats = array();
        $we_are_a_subchunk = false;
        if ($task_type == Common\Enums\TaskTypeEnum::TRANSLATION || $task_type == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $translate = 'translate';
            if ($task_type == Common\Enums\TaskTypeEnum::PROOFREADING) $translate = 'revise';

            if (empty($matecat_id_job)) {
                // Might be a chunk...
                $matecat_tasks = $taskDao->getTaskChunk($task_id);
                if (!empty($matecat_tasks)) {
                    $we_are_a_subchunk = true;
                    $matecat_langpair        = $matecat_tasks[0]['matecat_langpair'];
                    $matecat_id_job          = $matecat_tasks[0]['matecat_id_job'];
                    $matecat_id_job_password = $matecat_tasks[0]['matecat_id_chunk_password'];
                }
            }

            if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                  if (!$we_are_a_subchunk && $taskDao->getTaskSubChunks($matecat_id_job)) {
                      $stats['parent_of_chunked'] = 1;
                  } else {
                $stats['matecat_url'] = "https://tm.translatorswb.org/$translate/proj-" . $project_id . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password";
                  }
            }
        }
        return $stats;
    }

    public function active_users()
    {
        $result = LibAPI\PDOWrapper::call('active_users', '');
        return $result;
    }

    public function unclaimed_tasks()
    {
        $result = LibAPI\PDOWrapper::call('unclaimed_tasks', '');

        foreach ($result as $index => $user_row) {
           $stats = $this->get_matecat_task_urls($user_row['task_id'], $user_row['task_type'], $user_row['project_id'], $user_row['matecat_langpair_or_blank'], $user_row['matecat_id_job_or_zero'], $user_row['matecat_id_job_password_or_blank']);

           $result[$index]['matecat_url'] = '';
            if (!empty($stats['matecat_url'])) $result[$index]['matecat_url'] = $stats['matecat_url'];
            if (!empty($stats['parent_of_chunked'])) $result[$index]['status'] .= ' (Split Job)';
        }

        return $result;
    }

    public function search_users_by_language_pair($source, $target)
    {
        $result = LibAPI\PDOWrapper::call('search_users_by_language_pair', LibAPI\PDOWrapper::cleanseNullOrWrapStr($source) . ',' . LibAPI\PDOWrapper::cleanseNullOrWrapStr($target));
        return $result;
    }

    public function user_languages($code)
    {
        $result = LibAPI\PDOWrapper::call('user_languages', LibAPI\PDOWrapper::cleanseNullOrWrapStr($code));
        return $result;
    }

    public function user_task_languages($code)
    {
        $result = LibAPI\PDOWrapper::call('user_task_languages', LibAPI\PDOWrapper::cleanseNullOrWrapStr($code));
        return $result;
    }

    public function community_stats()
    {
        $result = LibAPI\PDOWrapper::call('community_stats', '');
        return $result;
    }

    public function community_stats_secondary()
    {
        $result = LibAPI\PDOWrapper::call('community_stats_secondary', '');
        return $result;
    }

    public function community_stats_words()
    {
        $result = LibAPI\PDOWrapper::call('community_stats_words', '');
        return $result;
    }

    public function all_orgs()
    {
        $result = LibAPI\PDOWrapper::call('all_orgs', '');
        return $result;
    }

    public function all_org_admins()
    {
        $result = LibAPI\PDOWrapper::call('all_org_admins', '');
        return $result;
    }

    public function all_org_members()
    {
        $result = LibAPI\PDOWrapper::call('all_org_members', '');
        return $result;
    }

    public function org_stats_words()
    {
        $result = LibAPI\PDOWrapper::call('org_stats_words', '');
        return $result;
    }

    public function org_stats_words_req()
    {
        $result = LibAPI\PDOWrapper::call('org_stats_words_req', '');
        return $result;
    }

    public function org_stats_languages()
    {
        $result = LibAPI\PDOWrapper::call('org_stats_languages', '');
        return $result;
    }

    public function users_active()
    {
        $result = LibAPI\PDOWrapper::call('users_active', '');
        return $result;
    }

    public function users_signed_up()
    {
        $result = LibAPI\PDOWrapper::call('users_signed_up', '');
        return $result;
    }

    public function new_tasks()
    {
        $result = LibAPI\PDOWrapper::call('new_tasks', '');
        return $result;
    }

    public function average_time_to_assign()
    {
        $result = LibAPI\PDOWrapper::call('average_time_to_assign', '');
        return $result;
    }

    public function average_time_to_turnaround()
    {
        $result = LibAPI\PDOWrapper::call('average_time_to_turnaround', '');
        return $result;
    }

    public function users_who_logged_in()
    {
        $result = LibAPI\PDOWrapper::call('users_who_logged_in', '');
        return $result;
    }

    public function language_work_requested()
    {
        $result = LibAPI\PDOWrapper::call('language_work_requested', '');
        return $result;
    }

    public function translators_for_language_pairs()
    {
        $result = LibAPI\PDOWrapper::call('translators_for_language_pairs', '');
        return $result;
    }

    public function search_user($name)
    {
        $result = LibAPI\PDOWrapper::call('search_user', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
        return $result;
    }

    public function search_organisation($name)
    {
        $result = LibAPI\PDOWrapper::call('search_organisation', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
        return $result;
    }

    public function search_project($name)
    {
        $result = LibAPI\PDOWrapper::call('search_project', LibAPI\PDOWrapper::cleanseNullOrWrapStr($name));
        return $result;
    }
}
