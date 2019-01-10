<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas1.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="10%">ID</th>
    <th width="20%">Title</th>
    <th width="15%">Analyze URL</th>
    <th width="10%">Word Count</th>
    <th width="15%">State</th>
    <th width="10%">Status</th>
    <th width="20%">Message</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td>{$user_row['project_id']}</td>
      <td><a href="{urlFor name="project-view" options="project_id.{$user_row['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['title'])}</a></td>
      {if !empty($user_row['matecat_id_project']) && !empty($user_row['matecat_id_project_pass'])}
      <td><a href="https://tm.translatorswb.org/analyze/proj-{$user_row['project_id']}/{$user_row['matecat_id_project']}-{$user_row['matecat_id_project_pass']}" target="_blank">Analyze URL</td>
      {else}
      <td></td>
      {/if}
      <td>{$user_row['matecat_word_count']}</td>
      <td>
      {if $user_row['state'] == 0}<span                                 >Waiting</span>{/if}
      {if $user_row['state'] == 1}<span style="background-color:Yellow;">Uploaded</span>{/if}
      {if $user_row['state'] == 2}<span style="background-color:Green;" >Success</span>{/if}
      {if $user_row['state'] == 3}<span style="background-color:Red;"   >Fail</span>{/if}
      </td>
      <td>{$user_row['status']}</td>
      <td>{$user_row['message']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
