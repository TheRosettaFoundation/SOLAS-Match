{include file="header.tpl"}

<div class="page-header">
    <h1>
        <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
        {TemplateHelper::uiCleanseHTML($user->getDisplayName())}
        <small>{Localisation::getTranslation('user_task_stream_notification_edit_0')}</small>
    </h1>
</div>

{if !(isset($strict))}
    {assign var="strict" value=false}
{/if}

{include file="handle-flash-messages.tpl"}

{assign var="user_id" value=$user->getId()}
<form method="post" action="{urlFor name="stream-notification-edit" options="user_id.$user_id"}">
    <p>
        {Localisation::getTranslation('user_task_stream_notification_edit_1')}
    </p>
    <p>
        <hr />
        {if isset($interval)}
            <p>
                {Localisation::getTranslation('common_how_often_receiving_emails')}
                <strong>{$interval}</strong>
            </p>
            <p>
                {if $lastSent != null}
                    {sprintf(Localisation::getTranslation('common_the_last_email_was_sent_on'), {$lastSent})}
                {else}
                    {Localisation::getTranslation('common_no_emails_have_been_sent_yet')}
                {/if}
            </p>
        {else}
            {Localisation::getTranslation('common_you_are_not_currently_receiving_task_stream_notification_emails')}
        {/if}
        <hr />
    </p>
    <p>
        {Localisation::getTranslation('user_task_stream_notification_edit_5')}
    </p>
    <p>
        {Localisation::getTranslation('user_task_stream_notification_edit_6')}
    </p>
    <p>
        <select name="interval">
            <option value="0"
                {if !isset($intervalId)}
                    selected="true"
                {/if}
            >
               {Localisation::getTranslation('user_task_stream_notification_edit_never')}
            </option>
            <option value="{NotificationIntervalEnum::DAILY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}
                    selected="true"
                {/if}
            >
                {Localisation::getTranslation('user_task_stream_notification_edit_daily')}
            </option>
            <option value="{NotificationIntervalEnum::WEEKLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}
                    selected="true"
                {/if}
            >
                {Localisation::getTranslation('user_task_stream_notification_edit_weekly')}
            </option>
            <option value="{NotificationIntervalEnum::MONTHLY}"
                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}
                    selected="true"
                {/if}
            >
                {Localisation::getTranslation('user_task_stream_notification_edit_monthly')}
            </option>
        </select>
    </p>
    <button type="submit" value="Submit" class="btn btn-success">
        <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_submit')}
    </button>      
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>

{include file="footer.tpl"}
