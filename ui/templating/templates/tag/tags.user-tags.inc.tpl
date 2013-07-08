{if isset($user_tags) AND is_array($user_tags) AND count($user_tags) > 0}
    <h3><i class="icon-tags"></i> {Localisation::getTranslation(Strings::TAGS_USER_TAGS_INC_0)}</h3>
    <ul class="nav nav-list unstyled">
        {foreach $user_tags as $tag}
            <li>
                {assign var="tag_label" value=$tag->getLabel()}
                {assign var="tagId" value=$tag->getId()}
                <div class="tag">
                    <a class="label" href="{urlFor name="tag-details" options="id.$tagId"}">{$tag_label}</a>
                    <a class="label label-close" href="{urlFor name="home"}tag/{$tag_label}/false" 
                    title="{Localisation::getTranslation(Strings::TAGS_USER_TAGS_INC_1)}"><strong>| x</strong></a>
                </div>
            </li>
        {/foreach}
    </ul>
    <p style="margin-bottom:20px;"/>
{/if}

