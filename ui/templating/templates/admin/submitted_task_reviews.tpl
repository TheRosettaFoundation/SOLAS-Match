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
    <th width="10%">Completed</th>
    <th width="15%">Revision Task</th>
    <th width="10%">Reviser</th>
    <th width="10%">Translator</th>
    <th width="10%">Language Pair</th>
    <th width="5%">Accuracy</th>
    <th width="5%">Fluency</th>
    <th width="5%">Terminology</th>
    <th width="5%">Style</th>
    <th width="5%">Design</th>
    <th width="20%">Comment</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td>{$user_row['complete_date']}</td>
      NEED task_title...
      <td><a href="{urlFor name="task-view" options="task_id.{$user_row['revise_task_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['task_title'])}</a></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['reviser_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['reviser_name'])}</a></td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['translator_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['translator_name'])}</a></td>
      <td>{$user_row['language_pair']}</td>
      <td>{$user_row['accuracy']}</td>
      <td>{$user_row['fluency']}</td>
      <td>{$user_row['terminology']}</td>
      <td>{$user_row['style']}</td>
      <td>{$user_row['design']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['comment'])}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
