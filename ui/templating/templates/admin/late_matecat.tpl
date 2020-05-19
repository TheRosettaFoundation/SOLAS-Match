<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas1.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#myTable').DataTable(
          {
            "paging": false
          }
        );
      });
    </script>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="10%">Partner</th>
    <th width="9%">Claimed</th>
    <th width="10%">Email</th>
    <th width="10%">Task Title</th>
    <th width="7%">Words</th>
    <th width="9%">Task Type</th>
    <th width="8%">Deadline</th>
    <th width="12%">Kató TM Status</th>
    <th width="9%">Translated</th>
    <th width="8%">Approved</th>
    <th width="8%">URL</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    {if $user_row['red'] == 1}
      {assign var="deadline_text" value="has passed"}
    {else}
      {assign var="deadline_text" value="is close to"}
    {/if}

    {assign var="subject" value="Deadline for '{$user_row['task_title']}' on the Kató Platform"}
    {assign var="body" value="Your claimed task '{$user_row['task_title']}' on the Kató Platform\r\n{$deadline_text} its deadline of {$user_row['deadline']}.\r\n\r\nIf you are using Kató TM, here is the link: {$user_row['matecat_url']}\r\n\r\nYou can submit your completed work here: https://kato.translatorswb.org/task/{$user_row['task_id']}/id\r\n\r\n"}

    <tr>
      <td><a href="{urlFor name="org-public-profile" options="org_id.{$user_row['org_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['org_name'])}</a></td>
      <td>{if !empty($user_row['user_id'])}<a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</a> {$user_row['claimed_time']}{/if}</td>
      <td>{if !empty($user_row['email'])}<a href="mailto:{$user_row['email']}?subject={rawurlencode($subject)}&body={rawurlencode($body)}" target="_blank">{$user_row['email']}</a>{/if}</td>
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['task_title'])}</a></td>
      <td>{$user_row['word_count']}</td>
      <td>{$user_row['task_type_text']}</td>
      <td><span {if $user_row['red'] == 1}style="color: red"{/if}>{$user_row['deadline']}</span></td>
      <td>{$user_row['DOWNLOAD_STATUS']}</td>
      <td>{$user_row['TRANSLATED_PERC_FORMATTED']}</td>
      <td>{$user_row['APPROVED_PERC_FORMATTED']}</td>
      <td>{if !empty($user_row['matecat_url'])}<a href="{$user_row['matecat_url']}" target="_blank">{$user_row['matecat_langpair_or_blank']}</a>{else}{$user_row['language_pair']}{/if}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
