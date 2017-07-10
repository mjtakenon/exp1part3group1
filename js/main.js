const width = 4;
const height = 4;

function init() {
  let s = '';
  for (let i = 0; i < height; i++) {
    s += '<tr class="y' + i + '">'
    for (let j = 0; j < width; j++) {
      s += '<td class="x' + j + '"></td>';
    }
    s += '</tr>'
  }
  $('#mosaic > tbody').html(s);

  for (let i = 0; i < height; i++)
    for (let j = 0; j < width; j++)
      $('.y' + i + ' > .x' + j).addClass('empty-cell', 3000, 'linear');

}

init();

$("#file_input").change(function () {
  $("#dummy_file").val($(this).val().replace("C:\\fakepath\\", ""));
});
