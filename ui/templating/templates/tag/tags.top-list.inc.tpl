{if isset($top_tags) AND is_array($top_tags) AND count($top_tags) > 0}
    <h3><i class="icon-tags"></i> Popular Tags:</h3>
    <ul class="nav nav-list unstyled">
        <li>
            {foreach $top_tags as $tag}
                <div class="tag">
                    {assign var="tag_label" value=$tag->getLabel()}
                    {assign var="tagId" value=$tag->getId()}
                    <a href="{urlFor name="tag-details" options="id.$tagId"}" class="label">{$tag_label}</a>
                </div>            
            {/foreach}
            <div class="tag">
                <a class="btn btn-primary btn-small" href="{urlFor name="tags-list"}"><i class="icon-list icon-white"></i> More Tags</a>
            </div>
        </li>
    </ul>
    <p style="margin-bottom:20px;"/>        
{/if}
