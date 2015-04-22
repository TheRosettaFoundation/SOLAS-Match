{include file="header.tpl" body_id="home"}

{if !isset($user)}
    <div class="hero-unit">
        <h1>{Localisation::getTranslation('index_translation_commons')}</h1>
        <p>{Localisation::getTranslation('index_0')}</p>
        <p>
            <a class="btn btn-success btn-large" href="{urlFor name="register"}">
                <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_register')}
            </a>
            <a class="btn btn-primary btn-large" href="{urlFor name="login"}">
                <i class="icon-share icon-white"></i> {Localisation::getTranslation('common_log_in')}
            </a>
        </p>
    </div>
{/if}

{if isset($flash['error'])}
    <br>
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{$flash['error']|escape:'html':'UTF-8'}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p><strong>{Localisation::getTranslation('common_note')} </strong>{$flash['info']|escape:'html':'UTF-8'}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_success')}! </strong>{$flash['success']|escape:'html':'UTF-8'}</p>
    </div>
{/if}

{if isset($flash['warning'])}
    <div class="alert alert-warning">
        <p><strong>{$flash['warning']|escape:'html':'UTF-8'}</strong></p>
    </div>
{/if}

<div class="page-header">
   <h1>
        {Localisation::getTranslation('index_translation_tasks')} <small>{Localisation::getTranslation('index_1')}</small>
        <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
            <i class="icon-search icon-white"></i> {Localisation::getTranslation('common_search_for_organisations')}
        </a>
    </h1>
</div>

<div class="row">
    <div class="span4 pull-right">
        <section class="donate-block">
            <p>{Localisation::getTranslation('index_donate_free_service')}</p>
            <a href="http://www.therosettafoundation.org" target="_blank">
                <img id="donate-trf-logo" src="{urlFor name='home'}ui/img/TheRosettaFoundationLogo.png" alt="The logo of The Rosetta Foundation" height="60"/>
            </a>
            <p>
                <strong>{Localisation::getTranslation('index_donate_support_us')}</strong>
            </p>
            <a id="donate" href="http://www.therosettafoundation.org/donate/" target="_blank">
                <div class="donate-button">
                    {Localisation::getTranslation('index_donate_support_trommons')}
                </div>
            </a>
        </section>

        {include file="tag/tags.user-tags.inc.tpl"}
        {include file="tag/tags.top-list.inc.tpl"}
        {if isset($statsArray) && is_array($statsArray)}
            {include file="statistics.tpl"}
        {/if}
        <div id="globe" style="text-align: center">
            <br/>
            <script type="text/javascript" src="http://jh.revolvermaps.com/p.js"></script><script type="text/javascript">rm2d_ki101('7','300','150','7puikkj5km8','ff00ff',0);</script>
            <br/>
        </div>
    </div>

    <div class="pull-left" style="max-width: 70%; overflow-wrap: break-word; word-break:break-all;">

        <div id="loading_warning">
            <p>{Localisation::getTranslation('common_loading')}</p>
        </div>

        {if isset($user)}
            <h3>{Localisation::getTranslation('index_filter_available_tasks')}</h3>
            <table>
                <thead>
                    <tr>
                        <th>{Localisation::getTranslation('common_task_type')}</th>
                        <th>{Localisation::getTranslation('common_source_language')}<span style="color: red">*</span></th>
                        <th>{Localisation::getTranslation('common_target_language')}<span style="color: red">*</span></th>
                    </tr>
                </thead>
                <tbody>
                    <form method="post" action="{urlFor name="home"}">
                        <tr>
                            <td>
                                <select name="taskTypes" id="taskTypes">
                                    <option value="-1" {if ($selectedTaskType == -1)}selected="selected"{/if}>{Localisation::getTranslation('index_any_task_type')}</option>
                                    <option value="1"  {if ($selectedTaskType ==  1)}selected="selected"{/if}>{Localisation::getTranslation('common_segmentation')}</option>
                                    <option value="2"  {if ($selectedTaskType ==  2)}selected="selected"{/if}>{Localisation::getTranslation('common_translation')}</option>
                                    <option value="3"  {if ($selectedTaskType ==  3)}selected="selected"{/if}>{Localisation::getTranslation('common_proofreading')}</option>
                                    <option value="4"  {if ($selectedTaskType ==  4)}selected="selected"{/if}>{Localisation::getTranslation('common_desegmentation')}</option>
                                 </select>
                            </td>
                            <td>
                                <select name="sourceLanguage" ID="sourceLanguage">
                                    <option value="-1" {if ($selectedSourceLanguageCode == -1)}selected="selected"{/if}>{Localisation::getTranslation("index_any_source_language")}</option>
                                    {foreach $activeSourceLanguages as $lang}
                                        <option value="{$lang->getCode()}" {if ($selectedSourceLanguageCode == $lang->getCode())}selected="selected"{/if}>{$lang->getName()}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <select name="targetLanguage" ID="targetLanguage">
                                    <option value="-1" {if ($selectedTargetLanguageCode == -1)}selected="selected"{/if}>{Localisation::getTranslation("index_any_target_language")}</option>
                                    {foreach $activeTargetLanguages as $lang}
                                        <option value="{$lang->getCode()}" {if ($selectedTargetLanguageCode == $lang->getCode())}selected="selected"{/if}>{$lang->getName()}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button class="btn btn-primary" type="submit">
                                    <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('index_filter_task_stream')}
                                </button>
                            </td>
                        </tr>
                    </form>
                </tbody>
            </table>
            <hr />
        {/if}
        {if isset($topTasks) && count($topTasks) > 0}
            <div class="ts">
                {for $count=0 to $limit-1}
                    {assign var="task" value=$topTasks[$count]}
                    <div class="ts-task">
                        {assign var="task_id" value=$task->getId()}
                        {assign var="type_id" value=$task->getTaskType()}
                        {assign var="task_title" value=$task->getTitle()}

                        {if $taskImages[$task_id]}
                        <div style="width:65%; word-break: break-word" class="pull-left" id="task_{$task_id}">
                        {else}
                        <div style="width:100%; word-break: break-word" class="pull-left" id="task_{$task_id}">
                        {/if}
                            <h2>
                                <a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/id">{$task_title|escape:'html':'UTF-8'}</a>
                            </h2>
                            <p>
                                {Localisation::getTranslation('common_type')}: <span class="label label-info" style="background-color: {$taskTypeColours[$type_id]}">{$taskTypeTexts[$type_id]}</span>
                            </p>
                            <p>
                                {Localisation::getTranslation('common_from')}: <strong>{TemplateHelper::getLanguageAndCountry($task->getSourceLocale())}</strong>
                            </p>
                            <p>
                                {Localisation::getTranslation('common_to')}: <strong>{TemplateHelper::getLanguageAndCountry($task->getTargetLocale())}</strong>
                            </p>
                            <p>
                                {if count($taskTags[$task_id]) gt 0}
                                    {foreach $taskTags[$task_id] as $tag}
                                        <a href="{$siteLocation}tag/{$tag->getId()}" class="label"><span class="label">{trim(trim($tag->getLabel()),",")}</span></a>
                                    {/foreach}
                                {/if}
                            </p>
                            <p>
                                {if $task->getWordCount()}
                                    {Localisation::getTranslation('common_word_count')}: <strong>{$task->getWordCount()}</strong>
                                {/if}
                            </p>

                            <p class="task_details"><div class="process_created_time_utc" style="visibility: hidden">{$created_timestamps[$task_id]}</div></p>
                            <p><div class="process_deadline_utc" style="visibility: hidden">{$deadline_timestamps[$task_id]}</div></p>
                            <p id="parents_{$task_id}">{$projectAndOrgs[$task_id]}</p>
                            <br />
                        </div>
                        {if $taskImages[$task_id]}
                            <div id="img_{$task_id}" class="pull-right task-stream-img" style="text-align:right; width:35%;">
                                <img src="{$taskImages[$task_id]}">
                            </div>
                        {else}
                            <div id="img_{$task_id}" class="pull-right task-stream-img" style="text-align:right"></div>
                        {/if}
                        <hr>
                    </div>
                {/for}
            </div>

            {* pagination begins here *}
            {assign var="url_name" value="home-paged"}
            <ul class="pager">
                <div class="pagination-centered">
                    {if $current_page > 1}
                        <li>
                            <a href="{urlFor name="$url_name" options="page_no.1"}" title="First">&lt;&lt;</a>
                        </li>
                        <li class="ts-previous">
                            {assign var="previous" value=($current_page - 1)}
                            <a href="{urlFor name="$url_name" options="page_no.$previous"}" title="Previous">&lt;</a>
                        </li>
                    {/if}
                    <li>
                        <a href="">{sprintf(Localisation::getTranslation('pagination_page_of'), {$current_page}, {$last_page})}</a>
                    </li>
                    {if $current_page < $last_page}
                        <li class="ts-next">
                            {assign var="next" value=($current_page + 1)}
                            <a href="{urlFor name="$url_name" options="page_no.$next"}" title="Next" >&gt;</a>
                        </li>
                        <li>
                            <a href="{urlFor name="$url_name" options="page_no.$last_page"}" title="Last">&gt;&gt;</a>
                        </li>
                    {/if}
                </div>
            </ul>
        {else}
            <p>{Localisation::getTranslation('index_no_tasks_available')}</p>
        {/if}
        <br />

        {if !isset($user)}
            <div class="alert pull-left" style="width: 100%; margin-top: 10px;">
                <p>{Localisation::getTranslation('index_6')}</p>
                <p>{sprintf(Localisation::getTranslation('index_register_now'), {urlFor name='register'})}</p>
            </div>
        {/if}
    </div>
</div>
            
{include file="footer.tpl"}
