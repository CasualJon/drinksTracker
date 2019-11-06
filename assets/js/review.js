//JS helper file for review.php
const getServerDateTimeStr = (dt) => {
  let out = '' + dt.getFullYear() + '-';
  let month = dt.getMonth() + 1;
  if (month >= 10) out += month + '-';
  else out += '0' + month + '-';
  if (dt.getDate() >= 10) out += dt.getDate() + ' 00:00:00';
  else out += '0' + dt.getDate() + ' 00:00:00';
  return out;
};
const getJSDateTime = (str) => {
  let dt;
  if (!fuckingSafari) {
    dt = str;
  }
  else {
    //Format for reference in Safari
    //2018-02-06T20:00:00.000-06:00
    let tmp = str.split(' ');
    dt = tmp[0];
    dt += 'T' + tmp[1] + '.000-06:00';
  }
  return new Date(dt);
};
let weeksDrinks = new Array();
let monthsDrinks = new Array();

//Set d to today's date
let d = new Date();
//Weekly counts should start on Friday
let lookback;
if (d.getDay() >= 5) lookback = (5 - d.getDay());
else lookback = (2 + d.getDay());
d.setDate(d.getDate() - lookback);
//Call the fetch function
fetchCountsInRange(getServerDateTimeStr(d), true);


function fetchCountsInRange(fetchDate, updateHdr) {
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
                  console.log(obj);
                  weeksDrinks = obj;
                  if (checkDatesForUndo()) $('#undo_button').prop("hidden", false);
                  if (updateHdr) {
                    //Adjust count and color (0-4=blue, 5-8=black, 9-12=orange, 13+=red)
                    $('#drink_count').html(obj.length);
                    if (obj.length <= 4) $('#drink_count').addClass('carolina-blue');
                    else if (obj.length >= 13) $('#drink_count').addClass('wi-red');
                    else if (obj.length >= 9) $('#drink_count').addClass('gc-orange');
                    //Draw week's chart with data in hand
                    google.charts.load('current', {packages: ['corechart']});
                    google.charts.setOnLoadCallback(drawCurrentWeekBarchart);
                  }
                }
                else {
                  console.log(obj.error);
                }
    },
  });
} //END fetchCountsInRange()
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
    title : "Weekly Chart",
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
