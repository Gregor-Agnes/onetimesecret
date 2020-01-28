function copyText() {
  /* Get the text field */
  var copyText = document.getElementById("copyText");

  /* Select the text field */
  copyText.select();
  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

  /* Copy the text inside the text field */
  document.execCommand("copy");

  /* Alert the copied text */
  alert("Copied the text: " + copyText.value);
}

$(document).ready(function () {
  $('#durationType').change(function () {
    var maxValue = settings.maxDuration;
    switch($(this).val()) {
      case 'minutes':
        maxValue = maxValue / 60;
        break;
      case 'hours':
        maxValue = maxValue / 60 / 60;
        break;
      case 'days':
        maxValue = maxValue / 60 / 60 / 24;
        break;
      default:
        maxValue = maxValue / 60 / 60 / 24 / 7;
        break;
    }
    var $valueField = $('#durationValue');
    $valueField.attr('max', maxValue);
    if ($valueField.val() > maxValue) {
      $valueField.val(maxValue);
    }
  });
});
