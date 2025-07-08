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

    public function list_memsource_projects()
    {
        $result = LibAPI\PDOWrapper::call('list_memsource_projects', '');
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

    public function po_readyness_report()
    {
        $result = LibAPI\PDOWrapper::call('po_readyness_report', '');
        if (empty($result)) return [];
        return $result;
    }

    public function sun_po_errors()
    {
        $result = LibAPI\PDOWrapper::call('sun_po_errors', '');
        if (empty($result)) return [];
        return $result;
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

    public function pr_report()
    {
        return LibAPI\PDOWrapper::call('pr_report', '');
    }

    public function po_report()
    {
        return LibAPI\PDOWrapper::call('po_report', '');
    }
}
