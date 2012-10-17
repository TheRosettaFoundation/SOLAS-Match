{if isset($user_tags) AND is_array($user_tags)}
    <h3><i class="icon-tags"></i>Tags You Have Subscribed To</h3>
    <ul class="nav nav-list unstyled">
        {foreach $user_tags as $tag}
            <li>
                {assign var="tag_label" value=$tag->getLabel()}
                <div class="tag">
                    <a class="label" href="{urlFor name="tag-details" options="label.$tag_label"}">{$tag_label}</a>
                    <a class="label label-close" href="{urlFor name="home"}tag/{$tag_label}/false" 
                    title="Click to remove tag from subscription list"><b>| x</b></a>
                </div>
            </li>
        {/foreach}
    </ul>
    <p style="margin-bottom:20px;"></p>
{/if}

