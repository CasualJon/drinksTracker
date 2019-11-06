//JS helper file for review.php
const getDateTimeStr = (dt) => {
  let out = '' + dt.getFullYear() + '-';
  let month = dt.getMonth() + 1;
  if (month >= 10) out += month + '-';
  else out += '0' + month + '-';
  if (dt.getDate() >= 10) out += dt.getDate() + ' 00:00:00';
  else out += '0' + dt.getDate() + ' 00:00:00';
  return out;
};
let drinksArr = new Array();

//Set d to today's date
let d = new Date();
//Weekly counts should start on Friday
let lookback = 2;
if (d.getDay() >= 5) lookback = (5 - d.getDay());
else lookback = (2 + d.getDay());
d.setDate(d.getDate() - lookback);
//Call the fetch function
fetchCountsInRange(getDateTimeStr(d), true);

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
                  drinksArr = obj;
                  if (updateHdr) $('#drink_count').html(obj.length);
                }
                else {
                  console.log(obj.error);
                }
    },
  });
}

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
                  if (obj.outcome == true) window.location.reload();
                }
                else {
                  console.log(obj);
                }
    },
  });
}
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
                  if (obj.outcome == true) window.location.reload();
                }
                else {
                  console.log(obj);
                }
    },
  });
}
