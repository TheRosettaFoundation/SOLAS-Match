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

    public function testing_center()
    {
        $result = LibAPI\PDOWrapper::call('testing_center', '');
        return $result;
    }

    public function complete_matecat()
    {
        return LibAPI\PDOWrapper::call('complete_matecat', '');
    }

    public function get_matecat_task_urls($task_id, $task_type, $project_id, $matecat_langpair, $matecat_id_job, $matecat_id_job_password)
    {
        $matecat_api = Common\Lib\Settings::get('matecat.url');
        $taskDao = new TaskDao();
        $stats = array();
        $we_are_a_subchunk = false;
        if ($task_type == Common\Enums\TaskTypeEnum::TRANSLATION || $task_type == Common\Enums\TaskTypeEnum::PROOFREADING) {
            $job_first_segment = '';
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
                    $job_first_segment       = $matecat_tasks[0]['job_first_segment'];
                }
            }

            if (!empty($matecat_langpair) && !empty($matecat_id_job) && !empty($matecat_id_job_password)) {
                  if (!$we_are_a_subchunk && $taskDao->getTaskSubChunks($matecat_id_job)) {
                      $stats['parent_of_chunked'] = 1;
                  } else {
                $stats['matecat_url'] = "{$matecat_api}$translate/proj-" . $project_id . '/' . str_replace('|', '-', $matecat_langpair) . "/$matecat_id_job-$matecat_id_job_password$job_first_segment";
                $stats['matecat_langpair_or_blank'] = $matecat_langpair;
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
            if (!empty($stats['matecat_url']))               $result[$index]['matecat_url']               = $stats['matecat_url'];
            if (!empty($stats['matecat_langpair_or_blank'])) $result[$index]['matecat_langpair_or_blank'] = $stats['matecat_langpair_or_blank'];
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

    public function user_words_by_language()
    {
        $result = LibAPI\PDOWrapper::call('user_words_by_language', '');
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

    public function user_task_reviews()
    {
        $result = LibAPI\PDOWrapper::call('user_task_reviews', '');
        return $result;
    }

    public function peer_to_peer_vetting()
    {
        $levels_by_user_language_pair_reduced = [];
        $result = LibAPI\PDOWrapper::call('peer_to_peer_vetting_qualification_level', '');
        foreach ($result as $row) {
            $levels_by_user_language_pair_reduced[$row['user_language_pair_reduced']] = $row;
        }

        $average_reviews_by_user_language_pair = [];
        $result = LibAPI\PDOWrapper::call('peer_to_peer_vetting_reviews', '');
        foreach ($result as $row) {
            $average_reviews_by_user_language_pair[$row['user_language_pair']] = $row;
        }

        $result = LibAPI\PDOWrapper::call('peer_to_peer_vetting', '');
        foreach ($result as $index => $row) {
            if (!empty($levels_by_user_language_pair_reduced[$row['user_language_pair_reduced']])) {
                $result[$index]['level'] = $levels_by_user_language_pair_reduced[$row['user_language_pair_reduced']]['level'];
            } else {
                $result[$index]['level'] = '';
            }

            if (!empty($average_reviews_by_user_language_pair[$row['user_language_pair']])) {
                $result[$index]['average_reviews'] = $average_reviews_by_user_language_pair[$row['user_language_pair']]['average_reviews'];
            } else {
                $result[$index]['average_reviews'] = '';
            }

            if (!empty($average_reviews_by_user_language_pair[$row['user_language_pair']])) {
                $result[$index]['number_reviews'] = $average_reviews_by_user_language_pair[$row['user_language_pair']]['number_reviews'];
            } else {
                $result[$index]['number_reviews'] = 0;
            }
        }
        return $result;
    }

    public function submitted_task_reviews()
    {
        $result = LibAPI\PDOWrapper::call('submitted_task_reviews', '');
        return $result;
    }

    public function tasks_no_reviews()
    {
        $result = LibAPI\PDOWrapper::call('tasks_no_reviews', '');
        return $result;
    }

    public function project_source_file_scores()
    {
        $result = LibAPI\PDOWrapper::call('project_source_file_scores', '');
        return $result;
    }

    public function matecat_analyse_status()
    {
        $result = LibAPI\PDOWrapper::call('matecat_analyse_status', '');
        return $result;
    }

    public function list_memsource_projects()
    {
        $result = LibAPI\PDOWrapper::call('list_memsource_projects', '');
        return $result;
    }

    public function covid_projects()
    {
        $result = LibAPI\PDOWrapper::call('covid_projects', '');
        return $result;
    }

    public function afghanistan_2021_projects()
    {
        $result = LibAPI\PDOWrapper::call('afghanistan_2021_projects', '');
        return $result;
    }

    public function haiti_2021_projects()
    {
        $result = LibAPI\PDOWrapper::call('haiti_2021_projects', '');
        return $result;
    }

    public function deal_id_report($deal_id)
    {
        $result = LibAPI\PDOWrapper::call('deal_id_report', LibAPI\PDOWrapper::cleanse($deal_id));
        if (empty($result)) $result = LibAPI\PDOWrapper::call('get_hubspot_deal', LibAPI\PDOWrapper::cleanse($deal_id));
        else {
            $allocated_budget = [];
            $result[0]['total_total_expected_cost'] = 0;
            $result[0]['total_total_expected_price'] = 0;
            $result[0]['total_paid_words_only_words'] = 0;
            $result[0]['total_paid_words_only_hours'] = 0;
            $result[0]['total_paid_words_only_terms'] = 0;
            foreach ($result as $r) {
                $allocated_budget[$r['project_id']] = $r['allocated_budget'];
                $result[0]['total_total_expected_cost'] += $r['total_expected_cost'];
                $result[0]['total_total_expected_price'] += $r['total_expected_price'];
                if ($r['pricing_and_recognition_unit_text_hours'] == 'Words')       $result[0]['total_paid_words_only_words'] += $r['total_paid_words'];
                if ($r['pricing_and_recognition_unit_text_hours'] == 'Labor hours') $result[0]['total_paid_words_only_hours'] += $r['total_paid_words'];
                if ($r['pricing_and_recognition_unit_text_hours'] == 'Terms')       $result[0]['total_paid_words_only_terms'] += $r['total_paid_words'];
            }
            $result[0]['total_allocated_budget'] = array_sum($allocated_budget);
        }
        if (empty($result)) $result = [];
        return $result;
    }

    public function paid_projects()
    {
        return LibAPI\PDOWrapper::call('get_paid_project_data', '');
    }

    public function all_deals_report()
    {
        $result = LibAPI\PDOWrapper::call('all_deals_report', '');
        $deals = [];
        foreach ($result as $r) {
            $deals[$r['deal_id']] = $r;
        }
        $result = LibAPI\PDOWrapper::call('all_deals_report_allocated_budget', '');
        foreach ($result as $r) {
            $deals[$r['deal_id']]['allocated_budget'] = $r['deal_allocated_budget'];
        }
        return $deals;
    }

    public function partner_deals($org_id)
    {
        $result = LibAPI\PDOWrapper::call('partner_deals', LibAPI\PDOWrapper::cleanse($org_id));
        if (empty($result)) return [];
        $deals = [];
        $deal_id = null;
        foreach ($result as $r) {
            if (empty($deals) || $r['deal_id'] != $deal_id) {
                $deal_id = $r['deal_id']; // Start a new deal
                $deals[$deal_id][] = $r;
                $deals[$deal_id][0]['total_expected_price'] = 0;
                $deals[$deal_id][0]['total_words'] = 0;
                $deals[$deal_id][0]['total_hours'] = 0;
                $deals[$deal_id][0]['total_terms'] = 0;

            } else {
                $deals[$deal_id][] = $r;
            }
            $deals[$deal_id][0]['total_expected_price'] += $r['expected_price'];
            $deals[$deal_id][0]['total_words'] += $r['words'];
            $deals[$deal_id][0]['total_hours'] += $r['hours'];
            $deals[$deal_id][0]['total_terms'] += $r['terms'];
        }
        return $deals;
    }

    public function sow_report()
    {
        $result = LibAPI\PDOWrapper::call('sow_report', '');
        if (empty($result)) return [];
        return $result;
    }

    public function sow_linguist_report()
    {
        $result = LibAPI\PDOWrapper::call('sow_linguist_report', '');
        if (empty($result)) return [];
        return $result;
    }
}
