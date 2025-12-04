{include file="new_header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

<div class="container">
    <span class="d-none">
        <!-- Parameters... -->
        <div id="siteLocation">{$siteLocation}</div>
    </span>
</div>

<div class="container-xxl px-4 px-sm-5 px-lg-5 pb-5 pt-4">
    <a href="{urlFor name="home"}" class="text-decoration-none fw-medium transition-colors mb-3 d-inline-flex align-items-center twb-core-blue">
        &larr; Back to Dashboard
    </a>

    <h1 class="fs-3 fw-bolder text-dark mb-4">
        {if $type == 1}
        News Archive (All Articles)
        {/if}
        {if $type == 2}
        Resources Archive (All Articles)
        {/if}
    </h1>

    {if !empty($news)}
    <div class="row g-4">

        {assign var="count" value=0}
        {foreach from=$news item=new}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card article-card h-100 border-0 rounded-3 overflow-hidden d-flex flex-column">
                <a href="{if empty($new['external_link'])}{urlFor name="content_display" options="item_id.{$new['id']}"}{else}{$new['external_link']}{/if}" class="d-block">
                    <div class="image-aspect-ratio-9-6">
                            {assign var="col" value="143878"}
                            {assign var="txt" value="OTHER"}
                            {assign var="alt" value="Other"}
                        {if $new['type'] == 11}
                            {assign var="col" value="143878"}
                            {assign var="txt" value="ARTICLE"}
                            {assign var="alt" value="Article"}
                        {/if}
                        {if $new['type'] == 13}
                            {assign var="col" value="E8991C"}
                            {assign var="txt" value="EVENT"}
                            {assign var="alt" value="Event"}
                        {/if}
                        {if $new['type'] == 12}
                            {assign var="col" value="143878"}
                            {assign var="txt" value="NEWSLETTER"}
                            {assign var="alt" value="Newsletter"}
                        {/if}
                        {if $new['type'] == 14}
                            {assign var="col" value="29528D"}
                            {assign var="txt" value="REPORT"}
                            {assign var="alt" value="Report"}
                        {/if}
                        {if $new['type'] == 21}
                            {assign var="col" value="143878"}
                            {assign var="txt" value="RESOURCE"}
                            {assign var="alt" value="Resource"}
                        {/if}

                        {if !empty($images[$new['id']])}
                        <img src="data:image/jpeg;base64,{$images[$new['id']]}" alt="{$alt}" class="card-img-top" />
                        {else}
                        <img src="https://placehold.co/120x80/{$col}/ffffff?text={$txt}" alt="{$alt}" class="card-img-top" />
                        {/if}
                    </div>
                </a>
                <div class="card-body p-4 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        {if $new['type'] == 11}
                        <span class="badge rounded-pill text-white twb-bg-core-blue fw-semibold p-1 px-2">Article</span>
                        {/if}
                        {if $new['type'] == 13}
                        <span class="badge rounded-pill text-white twb-bg-accent fw-semibold p-1 px-2">Event</span>
                        {/if}
                        {if $new['type'] == 12}
                        <span class="badge rounded-pill text-white twb-bg-core-blue fw-semibold p-1 px-2">Newsletter</span>
                        {/if}
                        {if $new['type'] == 14}
                        <span class="badge rounded-pill text-white twb-bg-core-blue fw-semibold p-1 px-2">Report</span>
                        {/if}
                        {if $new['type'] == 21}
                        <span class="badge rounded-pill text-white twb-bg-core-blue fw-semibold p-1 px-2">Resource</span>
                        {/if}
                        <span class="text-sm text-secondary">Updated: {substr($new['update_date'], 0, 10)}</span>
                    </div>
                    <a href="{if empty($new['external_link'])}{urlFor name="content_display" options="item_id.{$new['id']}"}{else}{$new['external_link']}{/if}" class="fs-5 fw-bold text-dark text-decoration-none article-title d-block">
                        {$new['title']}
                    </a>
                    <p class="text-sm text-secondary mt-2 line-clamp-3">
                        {if !empty($new['snippet'])}{$new['snippet']}{/if}
                    </p>
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    <a href="{if empty($new['external_link'])}{urlFor name="content_display" options="item_id.{$new['id']}"}{else}{$new['external_link']}{/if}" class="text-decoration-none fw-semibold d-flex align-items-center twb-core-blue">
                        Read Article &rarr;
                    </a>
                </div>
            </div>
        </div>
        {assign var="count" value=($count + 1)}
        {/foreach}

    </div>
    {/if}
</div>

{include file="footer2.tpl"}
