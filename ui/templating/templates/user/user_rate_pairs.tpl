<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

<span class="hidden">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="user_id">{$user_id}</div>
    <div id="user_rate_pairs_count">{$user_rate_pairs_count}</div>
    {assign var="i" value=0}
    {foreach $user_rate_pairs as $user_rate_pair}
        <div id="user_rate_pair_task_type_{$i}">{$user_rate_pair['task_type']}</div>
        <div id="user_rate_pair_language_id_source_{$i}">{$user_rate_pair['language_id_source']}</div>
        <div id="user_rate_pair_language_country_id_target_{$i}">{$user_rate_pair['language_country_id_target']}</div>
        <div id="user_rate_pair_unit_rate_{$i}">{$user_rate_pair['unit_rate']}</div>
        {assign var="i" value=$i+1}
    {/foreach}
</span>

<div class="well">
    {if isset($flash['error'])}
        <p class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <h2 class="twb_color">Linguist Unit Rate Exceptions for: {$user->getEmail()}</h2>

    <form method="post" id="user_rate_pairs" action="{urlFor name="user_rate_pairs" options="user_id.$user_id"}" enctype="multipart/form-data" accept-charset="utf-8">
        <div id="buildyourform">
            <div class="row-fluid" >
                <div class="span2">
                    <label class="clear_brand required"><strong>Task Type</strong>       <i class="icon-question-sign" id="tool_type"   data-toggle="tooltip" title="Task Type for which this Rate applies."></i></label>
                </div>
                <div class="span3">
                    <label class="clear_brand required"><strong>Source Language</strong> <i class="icon-question-sign" id="tool_source" data-toggle="tooltip" title="Source Language for which this Rate applies."></i></label>
                </div>
                <div class="span4">
                    <label class="clear_brand required"><strong>Target Language</strong> <i class="icon-question-sign" id="tool_target" data-toggle="tooltip" title="Target Locale for which this Rate applies."></i></label>
                </div>
                <div class="span2">
                    <label class="clear_brand required"><strong>Unit Rate</strong>       <i class="icon-question-sign" id="tool_rate"   data-toggle="tooltip" title="Exception Rate for this Linguist."></i></label>
                </div>
            </div>
        </div>
        
        <button type="submit"  class='pull-right btn btn-primary' id="updateBtn">
            <i class="icon-refresh icon-white"></i> Submit
        </button>
        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>
</div>

{include file='footer.tpl'}
