{include file="new_header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

<div class="container">
    <span class="d-none">
        <!-- Parameters... -->
        <div id="siteLocation">{$siteLocation}</div>
    </span>
</div>

<div class="container py-4" style="max-width: 800px;">{$new['type']}+++{intdiv($new['type'], 10)}
    <a href="{$siteLocation}content_list/{intdiv($new['type'], 10)}/" class="text-decoration-none fw-medium transition-colors mb-3 d-inline-flex align-items-center twb-core-blue">
        &larr; Back to {if intdiv($new['type'], 10) == 2}Resources{else}News{/if} Archive
    </a>

    <div class="bg-white p-4 p-sm-5 rounded-3 shadow-lg mt-4">

        <div class="mb-4 mb-sm-5 pb-4 border-bottom">
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
            <span class="badge rounded-pill text-white twb-bg-core-blue fw-semibold p-2 px-3">
                {$alt}
            </span>
            <h1 class="display-6 fw-bolder text-dark mt-3 mb-2 lh-sm">
                {$new['title']}
            </h1>
            <p class="text-lg text-secondary">
                <span class="fw-medium">Updated:</span> {substr($new['update_date'], 0, 10)}
            </p>
            {if !empty($image)}
            <img src="data:image/jpeg;base64,{$image}" alt="{$alt}" alt="{$alt}" class="w-100 mt-4 rounded-3 object-fit-cover" />
            {else}
            <img src="https://placehold.co/700x350/{$col}/ffffff?text={$txt}" alt="{$alt}" class="w-100 mt-4 rounded-3 object-fit-cover" />
            {/if}
        </div>

        <div class="article-content text-secondary-emphasis">
            {$new['body']}
        </div>
    </div>
</div>

{include file="footer2.tpl"}
