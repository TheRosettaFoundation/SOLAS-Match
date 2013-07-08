{include file="header.tpl" body_id="home"}

{if !isset($user)}
    <div class="hero-unit">
        <h1>{Localisation::getTranslation(Strings::INDEX_TRANSLATION_COMMONS)}</h1>
        <p>{Localisation::getTranslation(Strings::INDEX_0)}</p>
        <p>
            <a class="btn btn-success btn-large" href="{urlFor name="register"}">
                <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::COMMON_REGISTER)}
            </a>
            <a class="btn btn-primary btn-large" href="{urlFor name="login"}">
                <i class="icon-share icon-white"></i> {Localisation::getTranslation(Strings::COMMON_LOG_IN)}
            </a>
        </p>
    </div>
{/if}

{if isset($flash['error'])}
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}! </strong>{$flash['error']}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation(Strings::COMMON_NOTE)}: </strong>{$flash['info']}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}! </strong>{$flash['success']}</p>
    </div>
{/if}

    <div class="page-header">
        <h1>
            {Localisation::getTranslation(Strings::INDEX_TRANSLATION_TASKS)} <small>{Localisation::getTranslation(Strings::INDEX_1)}</small>
            <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                <i class="icon-search icon-white"></i> {Localisation::getTranslation(Strings::COMMON_SEARCH_FOR_ORGANISATIONS)}
            </a>
        </h1>
    </div>

    <div class="row">
        <div class="span4 pull-right">
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
            {if isset($user) && isset($tasks)}
                <h3>{Localisation::getTranslation(Strings::INDEX_FILTER)}</h3>
                <div id="filter">
                    <form action="{urlFor name="home"}" method="post">
                        <table>
                            <th>{Localisation::getTranslation(Strings::COMMON_TASK_TYPE)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)} <span style="color: red">*</span></th>
                            <th>{Localisation::getTranslation(Strings::COMMON_TARGET_LANGUAGE)} <span style="color: red">*</span></th>
                            <tr>
                                <td>
                                    <select name="taskType">
                                        <option value="">{Localisation::getTranslation(Strings::INDEX_ANY)}</option>
                                        {foreach $taskTypes as $id => $typeName}
                                            {if $id == $selectedType}
                                                <option selected="true" value="{$id}">{$typeName}</option>
                                            {else}
                                                <option value="{$id}">{$typeName}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </td>
                                <td>
                                    <select name="sourceLanguage">
                                        <option value="">{Localisation::getTranslation(Strings::INDEX_ANY)}</option>
                                        {foreach $languageList as $language}
                                            {if $language->getCode() == $selectedSource}
                                                <option selected value="{$language->getCode()}">{$language->getName()}</option>
                                            {else}
                                                <option value="{$language->getCode()}">{$language->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </td>
                                <td>
                                    <select name="targetLanguage">
                                        <option value="">{Localisation::getTranslation(Strings::INDEX_ANY)}</option>
                                        {foreach $languageList as $language}
                                            {if $language->getCode() == $selectedTarget}
                                                <option selected value="{$language->getCode()}">{$language->getName()}</option>
                                            {else}
                                                <option value="{$language->getCode()}">{$language->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="submit" value="Filter" class="btn btn-primary">
                                        <i class="icon-refresh icon-white"></i> {Localisation::getTranslation(Strings::INDEX_FILTER_TASK_STREAM)}
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>
                    <small>
                        <span style="color: red">*</span> {Localisation::getTranslation(Strings::INDEX_2)}
                        {Localisation::getTranslation(Strings::INDEX_3)}
                    </small>
                </div>
                <hr />
            {/if}
            {if count($tasks) > 0}
                <div id="tasks">
                    {foreach from=$tasks item=task name=tasks_loop}
                        {include file="task/task.summary-link.tpl" task=$task}
                    {/foreach}
                </div>
            {else}
                <div class="alert alert-warning">
                    {if isset($user_is_organisation_member)}
                        <strong>{Localisation::getTranslation(Strings::INDEX_NO_OPEN_TASKS)}</strong> {Localisation::getTranslation(Strings::INDEX_4)}
                    {else}
                        <strong>{Localisation::getTranslation(Strings::INDEX_NO_TASKS_AVAILABLE)}</strong> {Localisation::getTranslation(Strings::INDEX_5)}
                    {/if}
                </div>
            {/if}

            {if !isset($user)}
                <div class="alert">
                    <p>{Localisation::getTranslation(Strings::INDEX_6)}</p>
                    <p><a href={urlFor name="register"}>{Localisation::getTranslation(Strings::INDEX_REGISTER_NOW)}</a> {Localisation::getTranslation(Strings::INDEX_7)}</p>
                </div>
            {/if}      
        </div>
    </div>
            
{include file="footer.tpl"}
