const width  = 4;
const height = 4;

function init() {
  let s = '';
  for (let i = 0; i < height; i++) {
    s += '<tr>'
    for (let j = 0; j < width; j++) {
      s += '<td></td>'
    }
    s += '</tr>'
  }
  $('#mosaic > tbody').html(s);
}

init();

$("#file_input").change(function () {
  $("#dummy_file").val($(this).val().replace("C:\\fakepath\\", ""));
});
