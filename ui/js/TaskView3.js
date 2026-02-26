<script type="text/javascript">

var intervalID = null; // Global ID for interval timer for getting wordcount


$(document).ready(documentReady);

/**
 * Called by the DOM when the Document is Ready.
 */
function documentReady()
{
  $(".convert_utc_to_local").each(function ()
    {
      $(this).removeClass("convert_utc_to_local");
      var dUTC = $(this).text();
      var year    = dUTC.substring(0, 4);
      var month   = dUTC.substring(5, 7) -1;
      var day     = dUTC.substring(8, 10);
      var hour    = dUTC.substring(11, 13);
      var minutes = dUTC.substring(14, 16);
      var seconds = dUTC.substring(17, 19);

      var d = new Date(Date.UTC(year, month, day, hour, minutes, seconds));

      var m = d.getMonth() + 1;
      if (m < 10) {
        m = "0" + m;
      }
      var da = d.getDate();
      if (da < 10) {
        da = "0" + da;
      }
      var h = d.getHours();
      if (h < 10) {
        h = "0" + h;
      }
      var mi = d.getMinutes();
      if (mi < 10) {
        mi = "0" + mi;
      }
      var s = d.getSeconds();
      if (s < 10) {
        s = "0" + s;
      }
      $(this).html(d.getFullYear() + "-" + m + "-" + da + " " + h + ":" + mi + ":" + s);
      // Note: d.toLocaleString() works, but too many people have the wrong locale set!
      // Note: d.toString() is explicit about spelling out the Month in case the user's browser locale is wrong, but is longer and I am not sure about how it responds to locale

      $(this).css("visibility", "visible");
    }
  );

  $(".convert_utc_to_local_deadline").each(function ()
    {
      $(this).removeClass("convert_utc_to_local_deadline");
      var dUTC = $(this).text();
      var year    = dUTC.substring(0, 4);
      var month   = dUTC.substring(5, 7) -1;
      var day     = dUTC.substring(8, 10);
      var hour    = dUTC.substring(11, 13);
      var minutes = dUTC.substring(14, 16);
      var seconds = dUTC.substring(17, 19);

      var d = new Date(Date.UTC(year, month, day, hour, minutes, seconds));

      var m = d.getMonth() + 1;
      if (m < 10) {
        m = "0" + m;
      }
      var da = d.getDate();
      if (da < 10) {
        da = "0" + da;
      }
      var h = d.getHours();
      if (h < 10) {
        h = "0" + h;
      }
      var mi = d.getMinutes();
      if (mi < 10) {
        mi = "0" + mi;
      }
      var s = d.getSeconds();
      if (s < 10) {
        s = "0" + s;
      }
      $(this).html(d.getFullYear() + "-" + m + "-" + da + " " + h + ":" + mi + ":" + s + " " + Intl.DateTimeFormat().resolvedOptions().timeZone);
      // Note: d.toLocaleString() works, but too many people have the wrong locale set!
      // Note: d.toString() is explicit about spelling out the Month in case the user's browser locale is wrong, but is longer and I am not sure about how it responds to locale

      $(this).css("visibility", "visible");
    }
  );

  $(".convert_utc_to_local_deadline_no_timezone").each(function ()
    {
      $(this).removeClass("convert_utc_to_local_deadline_no_timezone");
      const dUTC = $(this).text();
      const year    = dUTC.substring(0, 4);
      const month   = dUTC.substring(5, 7) -1;
      const day     = dUTC.substring(8, 10);
      const hour    = dUTC.substring(11, 13);
      const minutes = dUTC.substring(14, 16);
      const seconds = dUTC.substring(17, 19);

      const d = new Date(Date.UTC(year, month, day, hour, minutes, seconds));

      let m = d.getMonth() + 1;
      if (m < 10) {
        m = "0" + m;
      }
      let da = d.getDate();
      if (da < 10) {
        da = "0" + da;
      }
      let h = d.getHours();
      if (h < 10) {
        h = "0" + h;
      }
      let mi = d.getMinutes();
      if (mi < 10) {
        mi = "0" + mi;
      }
      let s = d.getSeconds();
      if (s < 10) {
        s = "0" + s;
      }
      $(this).html(d.getFullYear() + "-" + m + "-" + da + " " + h + ":" + mi + ":" + s);

      $(this).css("visibility", "visible");
    }
  );

  if (document.getElementById("put_updated_wordcount_here").innerHTML == "-") {
    intervalID = setInterval(DAOgetWordCount, 5000);

    DAOgetWordCount();
  }

  $(".convert_utc_to_local_deadline_natural").each(function ()
    {
      $(this).removeClass("convert_utc_to_local_deadline_natural");
      var dUTC = $(this).text();
      var year    = dUTC.substring(0, 4);
      var month   = dUTC.substring(5, 7) -1;
      var day     = dUTC.substring(8, 10);
      var hour    = dUTC.substring(11, 13);
      var minutes = dUTC.substring(14, 16);
      var seconds = dUTC.substring(17, 19);
      var d = new Date(Date.UTC(year, month, day, hour, minutes, seconds));

      const formatter = new Intl.DateTimeFormat((new Intl.DateTimeFormat()).resolvedOptions().locale, {month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', timeZoneName: 'shortGeneric'});
      $(this).html(formatter.format(d));

      $(this).css("visibility", "visible");
    }
  );

  document.querySelector('#show-revision-btn').addEventListener('click', highlightRevisionCard);
  document.getElementById('confirm_read_instructions').addEventListener('click', removeRevisionHighlight);
}

function DAOgetWordCount()
{
  $.ajax(
    {
      url: document.getElementById("siteLocationURL").innerHTML + "project/" + document.getElementById("project_id_for_updated_wordcount").innerHTML + "/getwordcount/",
      method: "GET"
    }
  )
  .done(function (data, textStatus, jqXHR)
      {
        if (jqXHR.status == 200) {
          if (data != "" && data != "-") {
            clearInterval(intervalID);

            document.getElementById("put_updated_wordcount_here").innerHTML = data;
          }
        }
      }
    )
}

function check15More()
{
  var count = 0;
  $(".not_sent:not(:checked)").each(function ()
    {
      if (count++ < 15) {
        $(this).attr("checked", "checked");
      }
    }
  );
}

function functionOnSuccess()
{
  document.getElementById('mymailto').click();
}

function functionOnFail()
{
  console.log("Error: task/" + document.getElementById("task_id_for_invites_sent").innerHTML + "/task_invites_sent/ failed");
}

function sendEmails()
{
  var userIDs = "";
  var emails  = "";
  var comma = "";
  $(".translator_invite:checked").each(function ()
    {
      userIDs += comma + $(this).attr("id");
      emails  += comma + $(this).attr("email");
      comma = ",";
    }
  );

  if (emails != "") {
    document.getElementById("mymailto").setAttribute("href", "mailto:?bcc=" + emails
      + "&subject=" + document.getElementById("mailto_subject").innerHTML
      + "&body="    + document.getElementById("mailto_body").innerHTML);
  }

  if (userIDs != "") DAOTaskInvitesSentToUsers(userIDs, functionOnSuccess, functionOnFail);
}

function DAOTaskInvitesSentToUsers(userIDs, functionOnSuccess, functionOnFail)
{
  $.ajax(
    {
      url: document.getElementById("siteLocationURL").innerHTML + "task/" + document.getElementById("task_id_for_invites_sent").innerHTML
        + "/task_invites_sent/" + document.getElementById("sesskey").innerHTML + "/",
      method: "POST",
      xhrFields: {
        withCredentials: true
      },
      contentType: 'text/plain; charset=UTF-8',
      processData: false,
      data: userIDs
    }
  )
  .done(function (data, textStatus, jqXHR)
      {
        if (jqXHR.status < 400) {
          functionOnSuccess();
        } else {
          functionOnFail();
          console.log("Error: task/" + document.getElementById("task_id_for_invites_sent").innerHTML + "/task_invites_sent/ returned " + jqXHR.status + " " + jqXHR.statusText);
        }
      }
    )
  .fail(functionOnFail);
}

var highlight_index = 0;

async function highlightRevisionCard() {
    const json = await get_user_instructions();
console.log(json);
    const read = [];
    json.forEach((elem) => { read.push(elem.number); });
console.log(read);

    highlight_index = 0
    for (; highlight_index < 4; highlight_index++) if (!read.includes(highlight_index)) break;
console.log(highlight_index);

//(**)if highlight_index == 4 then all done
//(**)set_user_instruction(number);

    const card = document.querySelector('.highlight_' + highlight_index);
    if (!card) return;

    // Prevent duplicate overlay
    if (document.querySelector('.revision-overlay')) return;

    const overlay = document.createElement('div');
    overlay.className = 'revision-overlay';
    document.body.appendChild(overlay);

    // Activate animation
    requestAnimationFrame(() => {
        overlay.classList.add('active');
    });

    // Bring card above overlay
    card.classList.add('revision-highlight');

    // Optional: smooth scroll into view
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });

    overlay.addEventListener('click', removeRevisionHighlight);
}

function removeRevisionHighlight() {
    const overlay = document.querySelector('.revision-overlay');
    const card = document.querySelector('.highlight_' + highlight_index);

    overlay?.classList.remove('active');
    card?.classList.remove('revision-highlight');

    setTimeout(() => overlay?.remove(), 250);
}

    async function get_user_instructions() {
        try {
            const task_id = document.getElementById("task_id").innerHTML;
            const response = await fetch(`/task/${task_id}/view`, {
                method: "POST",
                body: new URLSearchParams(
                    {
                        category: 1,
                        user_id: document.getElementById("user_id").innerHTML,
                        sesskey: document.getElementById("sesskey").innerHTML,
                    }
                )
            });
            if (!response.ok) {
                throw new Error("error");
            }
            return response.json();
        } catch (error) {
            console.error(error);
        }
    }

    async function set_user_instruction(number) {
        try {
            const task_id = document.getElementById("task_id").innerHTML;
            const response = await fetch(`/task/${task_id}/view`, {
                method: "POST",
                body: new URLSearchParams(
                    {
                        category: 1,
                        user_id: document.getElementById("user_id").innerHTML,
                        number: number,
                        sesskey: document.getElementById("sesskey").innerHTML,
                    }
                )
            });
            if (!response.ok) {
                throw new Error("error");
            }
        } catch (error) {
            console.error(error);
        }
    }
</script>
