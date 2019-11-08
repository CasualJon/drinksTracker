//JS helper file for review.php ------------------------------------
//Converts a JS date object to a string that can be used for SQL comparisons on the server
const getServerDateTimeStr = (dt, withTime) => {
  let out = '' + dt.getFullYear() + '-';
  let month = dt.getMonth() + 1;
  if (month >= 10) out += month + '-';
  else out += '0' + month + '-';
  if (dt.getDate() >= 10) out += dt.getDate();
  else out += '0' + dt.getDate();
  if (withTime) out += ' 00:00:00';
  return out;
};

//Converts a string to a JS Date object because fuck safari
const getJSDateTime = (str) => {
  let dt;
  if (!fuckingSafari) dt = str;
  else {
    //Format for reference in Safari: 2018-02-06T20:00:00.000-06:00
    let tmp = str.split(' ');
    dt = tmp[0];
    dt += 'T' + tmp[1] + '.000-06:00'; //-06 for Central time
  }
  return new Date(dt);
};

//Enumeration object for defining allowed # per week and color thresholds
//Set here in case null, updated later via database
var limits = {
  MAX: 15,
  LOW: 4,
  MED: 9,
  HIGH: 13
};

//Empty arrays to be used for reference by later functions in adding content to the DOM
let weeksDrinks = new Array();

//Set d to today's date
let d = new Date();
//Weekly counts should start on Friday, so configure lookback
let lookback;
if (d.getDay() >= 5) lookback = (5 - d.getDay());
else lookback = (2 + d.getDay());
//Set the d as the the most recent Friday using the lookback and fetch server data
d.setDate(d.getDate() - lookback);
fetchCountsInRange(getServerDateTimeStr(d, true));
//With the core data rolling/complete, now let's get historicals
let monthRange = new Date();
monthRange.setDate(monthRange.getDate() - lookback - 28);
fetchHistoricalAggregates(getServerDateTimeStr(monthRange, false), 4);


function fetchCountsInRange(fetchDate) {
  jQuery.ajax({
    type:     'POST',
    url:      '../../support/sql/consumption_interaction.php',
    dataType: 'json',
    data:     {functionname: 'fetch_within_range', arguments: [fetchDate]},
    error:    function(a, b, c) {
                console.log('jQuery.ajax could not execute php file.');
              },
    success:  function(obj) {
                if (!('error' in obj)) {
                  let tmp = obj[obj.length - 1];
                  obj.splice(obj.length - 1, 1);
                  if (tmp.pd_max !== null) {
                    limits.MAX = tmp.pd_max;
                    limits.LOW = tmp.pd_low;
                    limits.MED = tmp.pd_med;
                    limits.HIGH = tmp.pd_high;
                  }
                  console.log({limits, obj});
                  weeksDrinks = obj;
                  if (checkDatesForUndo()) $('#undo_button').prop("hidden", false);
                  //Adjust count and color (0-4=blue, 5-8=black, 9-12=orange, 13+=red)
                  $('#drink_count').html(obj.length);
                  if (obj.length <= limits.LOW) $('#drink_count').addClass('carolina-blue');
                  else if (obj.length >= limits.HIGH) $('#drink_count').addClass('wi-red');
                  else if (obj.length >= limits.MED) $('#drink_count').addClass('gc-orange');
                  //Draw week's chart with data in hand
                  google.charts.load('current', {packages: ['corechart']});
                  google.charts.setOnLoadCallback(drawCurrentWeekBarchart);
                }
                else {
                  console.log(obj.error);
                }
    },
  });
} //END fetchCountsInRange()
function fetchHistoricalAggregates(fetchDate, numWeeks) {
  jQuery.ajax({
    type:     'POST',
    url:      '../../support/sql/consumption_interaction.php',
    dataType: 'json',
    data:     {functionname: 'fetch_historical_aggregates', arguments: [fetchDate, numWeeks]},
    error:    function(a, b, c) {
                console.log('jQuery.ajax could not execute php file.');
              },
    success:  function(obj) {
                if (!('error' in obj)) {
                  console.log(obj);
                  writeHistoricalAggregates(obj, numWeeks);
                }
                else {
                  console.log(obj.error);
                }
    },
  });
} //END fetchHistoricalAggregates()
function addDrink() {
  jQuery.ajax({
    type:     'POST',
    url:      '../../support/sql/consumption_interaction.php',
    dataType: 'json',
    data:     {functionname: 'add_drink', arguments: null},
    error:    function(a, b, c) {
                console.log('jQuery.ajax could not execute php file.');
              },
    success:  function(obj) {
                if ('outcome' in obj) {
                  console.log(obj);
                  if (obj.outcome) window.location.reload();
                }
                else {
                  console.log(obj);
                }
    },
  });
} //END addDrink()
function whoopsies() {
  jQuery.ajax({
    type:     'POST',
    url:      '../../support/sql/consumption_interaction.php',
    dataType: 'json',
    data:     {functionname: 'remove_last', arguments: null},
    error:    function(a, b, c) {
                console.log('jQuery.ajax could not execute php file.');
              },
    success:  function(obj) {
                if ('outcome' in obj) {
                  console.log(obj);
                  if (obj.outcome) window.location.reload();
                }
                else {
                  console.log(obj);
                }
    },
  });
} //END whoopsies()
function checkDatesForUndo() {
  if (weeksDrinks.length === 0) return false;

  let mostRecent = getJSDateTime(weeksDrinks[weeksDrinks.length - 1].cl_datetime);
  let today = new Date();
  if (mostRecent.getDate() === today.getDate() && mostRecent.getMonth() === today.getMonth()) return true;
  else return false;
} //END checkDatesForUndo()
function drawCurrentWeekBarchart() {
  let drawSpace = document.getElementById('current_week_barchart');
  let dailyCounts = new Array(7).fill(0);
  let highestPerDay = 0;

  for (let i = 0; i < dailyCounts.length; i++) {
    for (let j = 0; j < weeksDrinks.length; j++) {
      let compDate = getJSDateTime(weeksDrinks[j].cl_datetime);
      if ((d.getDate() + i) === compDate.getDate()) dailyCounts[i]++;
      else if ((d.getMonth() === compDate.getMonth()) && ((d.getDate + i) < compDate.getDate())) break;
    }
    if (dailyCounts[i] > highestPerDay) highestPerDay = dailyCounts[i];
  }
  if (highestPerDay < 5) highestPerDay = 5;

  let weeklyData = google.visualization.arrayToDataTable([
      ['Day', 'Count'],
      ['Fri',  dailyCounts[0]],
      ['Sat',  dailyCounts[1]],
      ['Sun',  dailyCounts[2]],
      ['Mon',  dailyCounts[3]],
      ['Tue',  dailyCounts[4]],
      ['Wed',  dailyCounts[5]],
      ['Thu',  dailyCounts[6]]
  ]);
  let weeklyOptions = {
    title : 'Weekly Chart',
    titleTextStyle: {
      fontName: 'Nunito Sans',
      fontSize: 18,
      bold: true
    },
    height: 250,
    vAxis: {viewWindow: {max: highestPerDay}},
    legend: {position: 'none'}
  };
  let weeklyChart = new google.visualization.ColumnChart(drawSpace);
  weeklyChart.draw(weeklyData, weeklyOptions);
} //END drawCurrentWeekBarchart()
function writeHistoricalAggregates(arr, numWeeks) {
  let out = "";
  for (let i = numWeeks-1; i >= 0; i--) {
    out += '<h3><span class="sizeOneTransp">' + arr[i][0].substring(5) + ' to ' + arr[i][1].substring(5) + ': </span>';
    out += arr[i][2];
    if (arr[i][2] <= limits.MAX) out += '<span class="iconGreen"><i class="far fa-check-circle"></i></span>';
    else out += '<span class="iconRed"><i class="far fa-times-circle"></i></span>';
    out += '</h3>';
  }
  $('#previous_month_data').html(out);
} //END writeHistoricalAggregates()
function launchUserSettings() {
  console.log("launchUserSettings clicked");
  $('#maxWeekly').val(limits.MAX);
  $('#blueWeekly').val(limits.LOW);
  $('#orangeWeekly').val(limits.MED);
  $('#redWeekly').val(limits.HIGH);
} //END launchUserSettings()
function validatePersonalizations() {
  let errMsg = '';
  //If all are empty
  if (!$('#maxWeekly').val() && !$('#blueWeekly').val() && !$('#orangeWeekly').val() && !$('#redWeekly').val()) {
    //pass - write all nulls to the database
  }
  //If all are populated
  else if ($('#maxWeekly').val() && $('#blueWeekly').val() && $('#orangeWeekly').val() && $('#redWeekly').val()) {
    let max = parseInt($('#maxWeekly').val().trim());
    let blue = parseInt($('#blueWeekly').val().trim());
    let orange = parseInt($('#orangeWeekly').val().trim());
    let red = parseInt($('#redWeekly').val().trim());

    if (isNaN(max) || isNaN(blue) || isNaN(orange) || isNaN(red)) {
      errMsg += 'Values entered must be numbers\n';
    }
    else {
      //Check to ensure that the numbers are in appropriate sequential order
      let issueFound = false;
      if (max <= blue || max <= orange || max <= red) issueFound = true;
      if (red <= blue || red <= orange) issueFound = true;
      if (orange <= blue) issueFound = true;
      if (issueFound) errMsg += "Thresholds and Max/Week must be sequential: Blue < Orange < Red < Max\n";
    }
  }
  //If only some populated
  else {
    errMsg += 'All values must be empty (to use defaults), or all must be populated with your values:\n\n';
    if (!$('#maxWeekly').val()) errMsg += '- Max/Week is missing\n';
    if (!$('#blueWeekly').val()) errMsg += '- Blue threshold is missing\n';
    if (!$('#orangeWeekly').val()) errMsg += '- Orange threshold is missing\n';
    if (!$('#redWeekly').val()) errMsg += '- Red threshold is missing\n';
  }

  if (errMsg) {
    alert(errMsg);
    return;
  }

  let args = [$('#blueWeekly').val(), $('#orangeWeekly').val(), $('#redWeekly').val(), $('#maxWeekly').val()];
  jQuery.ajax({
    type:     'POST',
    url:      '../../support/sql/consumption_interaction.php',
    dataType: 'json',
    data:     {functionname: 'update_personal_limits', arguments: args},
    error:    function(a, b, c) {
                console.log('jQuery.ajax could not execute php file.');
              },
    success:  function(obj) {
                if (!('error' in obj)) {
                  if (obj.outcome) {
                    $('#userSettingsModal').modal('hide');
                    window.location.reload();
                  }
                }
                else {
                  console.log(obj.error);
                }
    },
  });
} //END validatePersonalizations()
